<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
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

        return view('front.folders.create', compact('children_level_n'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $folder = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })->findOrFail($request->parent_id);

        $newFolder = Folder::create([
            'parent_id' => $request->parent_id,
            'name' => $request->input('name'),
            'project_id' => $folder->project_id,
            'created_by' => \Auth::user()->id,
        ]);

        return redirect()
            ->route('folders.show', [$newFolder])
            ->withStatus('New folder has been created');
    }

    public function show(Folder $folder)
    {



            /*$folder = $folder->with('project', 'multiUsers')
                ->whereHas('project.users', function($query) {
                    $query->where('id', auth()->id());
                })
                ->findOrFail($folder->id);*/

            $foldersUsers = User::with('multiFolders')->findOrFail(auth()->id());

           //dd($foldersUsers->multiFolders->where('id', 11)->toArray());
            /*foreach ($foldersUsers->multiFolders->where('id', $folder->id) as $dev):
                dd($dev->toArray());
                dd($dev->findOrFail($folder->id)->toArray());
            endforeach;*/

            /*$folder = \DB::table('folder_user')
                ->join('folders', 'folder_user.folder_id', 'folders.id')
                ->join('users', 'folder_user.user_id', 'users.id')
                ->join('projects', 'folders.project_id', 'projects.id')
                ->where('folder_user.user_id', '=', auth()->id())
                ->where('folder_user.folder_id', '=', $folder->id)
                ->get();*/


        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        return view('front.folders.show_files', compact('folder', 'children_level_n', 'foldersUsers'));
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


        if(!$request->functionality){
            $folder = Folder::with('project')
                ->whereHas('project.users', function($query) {
                    $query->where('id', auth()->id());
                })->findOrFail($request->folder_id);
        } else{
            $folder = Folder::findOrFail($request->folder_id);
        }

        foreach ($request->input('files', []) as $file) {
            $folder->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
            Media::where('model_id', $folder->id)->update([
                'created_by' => \Auth::user()->id
            ]);
        }

        return redirect()->route('folders.show', $folder)->withStatus('Files has been uploaded');
    }
}
