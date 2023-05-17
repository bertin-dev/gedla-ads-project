<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Folder;
use App\Models\Parapheur;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        $getParapheur = Parapheur::where('user_id', auth()->id())->first();
        if($getParapheur == null){
            $getLastInsertId = Parapheur::all()->max('id');
            Parapheur::create([
                'name' => 'parapheur'. $getLastInsertId + 1,
                'project_id' => 1,
                'user_id' => auth()->id()
            ]);
        }
        $parapheur = Parapheur::with('medias')->where('user_id', auth()->id())->first();
        //dd($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first()->updated_at);

        $getProjects = Project::all();
        //dd($getProjects->toArray());

        $getFolders = User::with('multiFolders')->findOrFail(auth()->id())->multiFolders;
        //dd($getFolders->toArray());

        $getActivity = AuditLog::where('current_user_id', auth()->id())
            ->orWhere('user_id_sender', auth()->id())
            ->orWhere('user_id_receiver', auth()->id())
            ->get()
            ->sortByDesc('created_at')
            ->take(5);
        //dd($getActivity->toArray());


        return view('front.projects.index', compact('children_level_n', 'functionality', 'parapheur', 'getFolders', 'getProjects', 'getActivity'));
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
