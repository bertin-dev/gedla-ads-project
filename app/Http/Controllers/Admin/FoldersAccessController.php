<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MassDestroyFolderRequest;
use App\Models\Folder;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class FoldersAccessController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('folder_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $folderUser = Folder::with('multiUsers')
            ->get();
        //dd($folderUser->toArray());

        return view('admin.folders_access.index', compact('folderUser'));
    }

    public function create()
    {
        abort_if(Gate::denies('folder_access_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::where('id', '!=', \Auth::user()->id)
            ->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $folders = Folder::where('functionality', false)->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.folders_access.create', compact('users', 'folders', 'projects'));
    }

    public function store(Request $request)
    {
        $usersSelected = $request->input('user_access', []);

        $folder = Folder::findOrFail($request->folder_access);
        $folder->multiUsers()->sync($usersSelected);
        return redirect()->route('admin.folders_access.index');
    }

    public function show($id)
    {
        abort_if(Gate::denies('folder_access_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $folder = Folder::with('project', 'multiUsers')
            ->where('functionality', false)
            ->findOrFail($id);

        //return new FolderResource($folder);

        return view('admin.folders_access.show', compact('folder'));

        //return response()->json($folder, Response::HTTP_CREATED);
    }

    public function edit($id)
    {
        abort_if(Gate::denies('folder_access_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $folders = Folder::where('id', $id)
            ->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $folder = Folder::with('project')
        ->where('functionality', false)
            ->findOrFail($id);
            //->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        //$folder->load('project', 'multiUsers');

        $users = User::where('id','!=', \Auth::user()->id)
            ->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        //dd($users->toArray());


        return view('admin.folders_access.edit', compact('folders', 'folder', 'users'));
    }

    public function update(Request $request, $id)
    {
        $usersSelected = $request->input('user_access', []);

        $folder = Folder::findOrFail($request->folder_access);
        $folder->multiUsers()->sync($usersSelected);

        return redirect()->route('admin.folders_access.index');
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('folder_access_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

         Folder::with('multiUsers')
        ->where('folder_user.user_id', $id)
        ->delete();

        return back();
    }

    public function massDestroy(MassDestroyFolderRequest $request)
    {
        Folder::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

}

