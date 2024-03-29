<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('project_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::with('userCreatedProjectBy', 'userUpdatedProjectBy')->get();

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        abort_if(Gate::denies('project_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::all()->pluck('name', 'id');

        return view('admin.projects.create', compact('users'));
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create([
            'name' => $request->name,
            'users' => $request->users,
            'created_by' => \Auth::user()->id,
        ]);
        $project->folders()->create([
            'name' => 'Parent Directory'
        ]);
        $project->users()->sync($request->input('users', []));

        return redirect()->route('admin.projects.index');
    }

    public function edit(Project $project)
    {
        abort_if(Gate::denies('project_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::all()->pluck('name', 'id');

        $project->load('users');

        return view('admin.projects.edit', compact('users', 'project'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update([
            'name' => $request->name,
            'users' => $request->users,
            'updated_by' => \Auth::user()->id,
        ]);
        $project->users()->sync($request->input('users', []));

        return redirect()->route('admin.projects.index');
    }

    public function show(Project $project)
    {
        abort_if(Gate::denies('project_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $project->load('users', 'userCreatedProjectBy', 'userUpdatedProjectBy');

        return view('admin.projects.show', compact('project'));
    }

    public function destroy(Project $project)
    {
        abort_if(Gate::denies('project_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $project->delete();

        return back();
    }

    public function massDestroy(MassDestroyProjectRequest $request)
    {
        Project::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
