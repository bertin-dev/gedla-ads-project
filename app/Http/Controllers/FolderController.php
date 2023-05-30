<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Auditable;
use App\Models\AuditLog;
use App\Models\Folder;
use App\Models\Project;
use App\Models\User;
use App\Models\ValidationStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FolderController extends Controller
{
    use Auditable;

    public function create(Request $request)
    {
        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function ($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();


        $users = Project::with('users')
            ->findOrFail($request->project_id)
            ->users
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');


        return view('front.folders.create', compact('children_level_n', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'user_access' => 'required',
        ]);

        $usersSelected = $request->input('user_access', []);

        $newFolder = Folder::create([
            'parent_id' => $request->parent_id,
            'name' => $request->input('name'),
            'description' => nl2br(htmlentities($request->desc)),
            'project_id' => $request->project_id,
            'created_by' => \Auth::user()->id,
        ]);

        $newFolder->multiUsers()->sync($usersSelected);

        return redirect()
            ->route('folders.show', [$newFolder->parent])
            ->withStatus('New folder has been created');
    }

    public function show(Folder $folder)
    {
        //abort_if(Gate::denies('workflow_management_access_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $folder = $folder->with('project', 'multiUsers', 'userCreatedFolderBy', 'userUpdatedFolderBy')
            ->whereHas('project.users', function ($query) {
                $query->where('id', auth()->id());
            })
            ->findOrFail($folder->id);

        $children_level_n = $folder->with('project')
            ->whereHas('project.users', function ($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        $users = User::where('id', '!=', \Auth::user()->id)
            ->whereHas('multiFolders')
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $getValidationDatas = ValidationStep::where('user_id', auth()->id());

        $checkUsersIfUserIdInArray = $folder->multiUsers->where('id', auth()->id());
        //dd($checkUsersIfUserIdInArray->toArray());
        return view('front.folders.show_files', compact('folder', 'children_level_n', 'users', 'getValidationDatas'));
    }

    public function upload()
    {
        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function ($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        return view('front.folders.upload', compact('children_level_n'));
    }

    public function storeMedia(Request $request)
    {
        // Validates file size
        if (request()->has('size')) {
            $this->validate(request(), [
                'file' => 'max:' . request()->input('size') * 1024,
            ]);
        }

        // If width or height is preset - we are validating it as an image
        if (request()->has('width') || request()->has('height')) {
            $this->validate(request(), [
                'file' => sprintf(
                    'image|dimensions:max_width=%s,max_height=%s',
                    request()->input('width', 100000),
                    request()->input('height', 100000)
                ),
            ]);
        }

        $path = storage_path('tmp/uploads');

        try {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        } catch (\Exception $e) {
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function postUpload(Request $request)
    {
        $folder = Folder::findOrFail($request->folder_id);

        foreach ($request->input('files', []) as $file) {
            $media = $folder->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
            Media::where('model_id', $folder->id)->update([
                'created_by' => \Auth::user()->id
            ]);
            $getLog = AuditLog::where('media_id', $media->id)
                ->where('current_user_id', auth()->id())
                ->where('operation_type', 'IMPORT_DOCUMENT')
                ->get();
            if (count($getLog) === 0) {
                self::trackOperations($media->id,
                    "IMPORT_DOCUMENT",
                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) . ' vient d\'importer le document ' . strtoupper(substr($media->file_name, 14))),
                    'success',
                    null,
                    auth()->id(),
                    '',
                    ucfirst(auth()->user()->name) . ' vient d\'importer le document ' . strtoupper(substr($media->file_name, 14)));
            }
        }

        return redirect()->route('folders.show', $folder)->withStatus('Files has been uploaded');
    }

    private function templateForDocumentHistoric($params = '')
    {
        return '<div class="row schedule-item>
                <div class="col-md-2">
                <time class="timeago">Le ' . date('d-m-Y à H:i:s', time()) . '</time>
                </div>
                <div class="col-md-12">
                <p>' . $params . '</p>
                </div>
                </div>';
    }
}
