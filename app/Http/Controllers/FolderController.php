<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Project;
use App\Models\User;
use App\Models\ValidationStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FolderController extends Controller
{
    public function create()
    {
        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();


        //nous recherchons tous les utilisateurs appartenant au même project
        $getProjectId = DB::table('project_user')->where('user_id', Auth()->id())->first()->project_id;

        $users = Project::with('users')
            ->where('id', $getProjectId)
            ->first()
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
            $folder = $folder->with('project', 'multiUsers', 'userCreatedFolderBy', 'userUpdatedFolderBy')
                ->whereHas('project.users', function($query) {
                    $query->where('id', auth()->id());
                })
                ->findOrFail($folder->id);
            //dd($folder->children->toArray());

            /*$folder = \DB::table('folder_user')
                ->join('folders', 'folder_user.folder_id', 'folders.id')
                ->join('users', 'folder_user.user_id', 'users.id')
                ->join('projects', 'folders.project_id', 'projects.id')
                ->where('folder_user.user_id', '=', auth()->id())
                ->where('folder_user.folder_id', '=', $folder->id)
                ->get();*/


        $children_level_n = $folder->with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        //dd($children_level_n->project);

       /* dd($folder->with('project') ->whereHas('project.users', function($query) {
            $query->where('id', auth()->id());
        })  ->whereNull('parent_id')
            ->with('subChildren')
            ->get()
            ->toArray());*/

        $users = User::where('id', '!=', \Auth::user()->id)
            ->whereHas('multiFolders')
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $getValidationDatas = ValidationStep::where('user_id', auth()->id());

        //dd($folder->children->toArray());
        return view('front.folders.show_files', compact('folder', 'children_level_n', 'users', 'getValidationDatas'));
        //return view('front.folders.show', compact('folder','projects'));
    }

    public function upload()
    {
        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
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
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function postUpload(Request $request)
    {
        $folder = Folder::findOrFail($request->folder_id);

        foreach ($request->input('files', []) as $file) {
            $folder->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
            Media::where('model_id', $folder->id)->update([
                'created_by' => \Auth::user()->id
            ]);
        }

        return redirect()->route('folders.show', $folder)->withStatus('Files has been uploaded');
    }
}
