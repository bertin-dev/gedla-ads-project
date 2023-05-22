<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use App\Models\Folder;
use App\Models\Parapheur;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    private $operationTypes = ["SEND_DOCUMENT", "VALIDATE_DOCUMENT", "VALIDATE_DOCUMENT_SIGNATURE",
        "SEND_DOCUMENT_SIGNATURE", "VALIDATE_DOCUMENT_PARAPHEUR", "SEND_DOCUMENT_PARAPHEUR", "OPEN_DOCUMENT", "PREVIEW_DOCUMENT",
        "START_VALIDATION", "REJECTED_DOCUMENT", "EDIT_DOCUMENT", "SAVE_DOCUMENT", "DOWNLOAD_DOCUMENT", "ARCHIVE_DOCUMENT", "IMPORT_DOCUMENT",
        "RESTORE_ARCHIVE_DOCUMENT"];

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



        $getActivity = AuditLog::where('user_id_sender', auth()->id())
            ->orWhere('user_id_receiver', auth()->id())
            ->whereIn('operation_type', $this->operationTypes)
            ->get()
            ->sortByDesc('created_at');
        //dd($getActivity->toArray());


        $title = trans('global.welcome') .' '. ucfirst(auth()->user()->name);
        return view('front.projects.index', compact('children_level_n', 'functionality', 'parapheur', 'getFolders', 'getProjects', 'getActivity', 'title'));
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

   public function filterActivityByDate($params){
       $getActivity = "";
       $dateType = "";
        switch ($params){
            case "filter_day":
                $dateType = "day";
                $date = date('Y-m-d', time());
                $getActivity = AuditLog::where(function($query) {
                    $query->where('user_id_sender', auth()->id())
                        ->orWhere('user_id_receiver', auth()->id());
                })
                    ->whereIn('operation_type', $this->operationTypes)
                    ->whereDate('created_at', $date)
                    ->orderByDesc('created_at')
                    //->take(5)
                    ->get();
                break;
            case "filter_month":
                $dateType = "month";
                $month = date('m', time());
                $year = date('Y', time());
                $getActivity = AuditLog::where(function($query) {
                    $query->where('user_id_sender', auth()->id())
                        ->orWhere('user_id_receiver', auth()->id());
                })
                    ->whereIn('operation_type', $this->operationTypes)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderByDesc('created_at')
                    //->take(5)
                    ->get();
                break;
            case "filter_year":
                $dateType = "year";
                $year = date('Y', time());
                $getActivity = AuditLog::where(function($query) {
                    $query->where('user_id_sender', auth()->id())
                        ->orWhere('user_id_receiver', auth()->id());
                })
                    ->whereIn('operation_type', $this->operationTypes)
                    ->whereYear('created_at', $year)
                    ->orderByDesc('created_at')
                    //->take(5)
                    ->get();
                break;
        }

       $result = "";
       foreach($getActivity as $key => $activityList):
           $result .= '<div class="activity-item d-flex">
                                        <div class="activite-label">'. Carbon::parse($activityList->created_at)->diffForHumans() .'</div>
                                        <i class="bi bi-circle-fill activity-badge text-success align-self-start"></i>
                                        <div class="activity-content">'. $activityList->message .'</div>
                                       </div>';
       endforeach;

       return response()->json([
           'result' => $result,
           '$date_type' => $dateType
       ]);
   }

   public function filterDocumentByDate($params){
       $filteredMedia = "";
       $result = "";
        switch ($params){
            case "filter_doc_day":
                $date = date('Y-m-d', time());
                $getFolders = User::with('multiFolders')->findOrFail(auth()->id())->multiFolders;

                foreach ($getFolders as $folder) {
                    $media = $folder->media;
                    $filteredMedia = $media->filter(function ($mediaItem) use ($date) {
                        return $mediaItem->created_at->isSameDay($date);
                    })->sortByDesc('created_at');
                        //->take(5);
                }
                break;
            case "filter_doc_month":
                $month = date('m', time());
                $year = date('Y', time());
                $getFolders = User::with('multiFolders')->findOrFail(auth()->id())->multiFolders;

                foreach ($getFolders as $folder) {
                    $media = $folder->media;
                    $filteredMedia = $media->filter(function ($mediaItem) use ($month, $year) {
                        return $mediaItem->created_at->year == $year && $mediaItem->created_at->month == $month;
                    })->sortByDesc('created_at');
                        //->take(5);
                }
                break;
            case "filter_doc_year":
                $year = date('Y', time());
                $getFolders = User::with('multiFolders')->findOrFail(auth()->id())->multiFolders;

                foreach ($getFolders as $folder) {
                    $media = $folder->media;
                    $filteredMedia = $media->filter(function ($mediaItem) use ($year) {
                        return $mediaItem->created_at->year == $year;
                    })->sortByDesc('created_at');
                        //->take(5);
                }
                break;
        }


        if(count($filteredMedia) != 0){
            foreach($filteredMedia as $key => $getMedia):
                $result .= '<div class="card">
                                            <div class="row no-gutters">
                                                <div class="col-sm-4">
                                                    <img class="img-thumbnail" width="75" src="'.url('images\document2.png').'">
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="card-body" style="padding: 5px 5px 0;">
                                                        <h5 class="card-title">'.strtolower(substr($getMedia->file_name, 14)).'</h5>
                                                        <span>'.trans('global.edit').' '.Carbon::parse($getMedia->updated_at)->diffForHumans().'</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
            endforeach;
        }

       return response()->json([
           'result' => $result
       ]);
   }
}
