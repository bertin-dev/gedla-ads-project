<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\Auditable;
use App\Models\AuditLog;
use App\Models\Folder;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyFolderRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class FoldersController extends Controller
{
    use MediaUploadingTrait, Auditable;

    public function index()
    {
        abort_if(Gate::denies('folder_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $folders = Folder::with(['userCreatedFolderBy', 'userUpdatedFolderBy'])->get();

        return view('admin.folders.index', compact('folders'));
    }

    public function create()
    {
        abort_if(Gate::denies('folder_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $parents = Folder::where('functionality', false)->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.folders.create', compact('projects', 'parents'));
    }

    public function store(StoreFolderRequest $request)
    {
        $folder = Folder::create([
            'name' => $request->name,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'parent_id' => $request->parent_id,
            'thumbnail_id' => $request->thumbnail_id,
            'created_by' => \Auth::user()->id
        ]);

        foreach ($request->input('files', []) as $file) {
            $media = $folder->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
            Media::where('model_id', $folder->id)->update([
                'created_by' => \Auth::user()->id
            ]);

            $getLog = AuditLog::where('media_id', $media->id)
                ->where('current_user_id', auth()->id())
                ->where('operation_type', 'IMPORT_DOCUMENT')
                ->get();
            if(count($getLog) === 0){
                self::trackOperations($media->id,
                    "IMPORT_DOCUMENT",
                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' vient d\'importer le document '. strtoupper(substr($media->file_name, 14))),
                    'success',
                    null,
                    auth()->id(),
                    '',
                    ucfirst(auth()->user()->name) .' vient d\'importer le document '. strtoupper(substr($media->file_name, 14)));
            }
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update([
                'model_id' => $folder->id,
                'created_by' => \Auth::user()->id
            ]);
        }

        return redirect()->route('admin.folders.index');
    }

    public function edit(Folder $folder)
    {
        abort_if(Gate::denies('folder_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $parents = Folder::where('functionality', false)->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $folder->load('project', 'parent');

        return view('admin.folders.edit', compact('projects', 'parents', 'folder'));
    }

    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        $folder->update([
            'name' => $request->name,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'parent_id' => $request->parent_id,
            'thumbnail_id' => $request->thumbnail_id,
            'updated_by' => \Auth::user()->id
        ]);

        if (count($folder->files) > 0) {
            foreach ($folder->files as $media) {
                if (!in_array($media->file_name, $request->input('files', []))) {
                    $media->delete();
                }
            }
        }

        $media = $folder->files->pluck('file_name')->toArray();

        foreach ($request->input('files', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $folder->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
            }
        }

        return redirect()->route('admin.folders.index');
    }

    public function show(Folder $folder)
    {
        abort_if(Gate::denies('folder_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $folder->load('project', 'parent', 'userCreatedFolderBy', 'userUpdatedFolderBy');

        return view('admin.folders.show', compact('folder'));
    }

    public function destroy(Folder $folder)
    {
        abort_if(Gate::denies('folder_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $folder->delete();

        return back();
    }

    public function massDestroy(MassDestroyFolderRequest $request)
    {
        Folder::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('folder_create') && Gate::denies('folder_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Folder();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    private function templateForDocumentHistoric($params = ''){
        return '<div class="row schedule-item>
                <div class="col-md-2">
                <time class="timeago">Le '.date('d-m-Y à H:i:s', time()).'</time>
                </div>
                <div class="col-md-12">
                <p>' .$params . '</p>
                </div>
                </div>';
    }
}
