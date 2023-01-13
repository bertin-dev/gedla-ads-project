<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Parapheur;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        /*$projects = Project::whereHas('users', function($query) {
            $query->where('id', auth()->id());
        })->get();*/

        $functionality = Folder::where('functionality', true)->get();

        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        /*$dev = Folder::with('project', 'multiUsers')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();*/


        //dd($children_level_n->toArray());

        $parapheurCount = Parapheur::with('medias')->where('user_id', auth()->id())->get();


        return view('front.projects.index', compact('children_level_n', 'functionality', 'parapheurCount'));
    }

    public function show($id)
    {
        $project = Project::whereHas('users', function($query) {
            $query->where('id', auth()->id());
        })->findOrFail($id);

        if (!$project->parentDirectory) {
            return back();
        }

        return redirect()->route('folders.show', $project->parentDirectory->id);
    }


    public function showAll()
    {
        $project = Project::with('folder')
            ->whereHas('users', function($query) {
            $query->where('id', auth()->id());
        })->get();

        dd($project);

      /*  $folder = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })->findOrFail($id);*/



        return view('front.projects.index', compact('project'));

   }
}
