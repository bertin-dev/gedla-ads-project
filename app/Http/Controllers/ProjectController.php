<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use App\Models\Folder;
use App\Models\Parapheur;
use App\Models\Project;
use App\Models\User;
use App\Models\ValidationStep;
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

        $workflow_validation = ValidationStep::select('media.file_name AS media_name', \DB::raw('MAX(validation_steps.id) AS max_id'),
            \DB::raw('GROUP_CONCAT(users.name SEPARATOR ", ") AS users_name'),
            \DB::raw('GROUP_CONCAT(validation_steps.statut SEPARATOR ", ") AS step_statut'),
            'media.statut AS final_statut_media', 'media.global_deadline AS media_deadline')
            ->join('media', 'validation_steps.media_id', '=', 'media.id')
            ->join('users', 'validation_steps.user_id', '=', 'users.id')
            ->where('validation_steps.user_id', auth()->id())
            ->groupBy('media.id', 'media.file_name', 'media.statut', 'media.global_deadline')
            ->orderBy('media.global_deadline')
            ->get();
        //dd($workflow_validation->toArray());

        $title = trans('global.welcome') .' '. ucfirst(auth()->user()->name);
        return view('front.projects.index', compact('children_level_n','parapheur', 'getFolders', 'getProjects', 'getActivity', 'workflow_validation', 'title'));
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


    public function filterWorkflowByStatus($params){
        $result = "";
        switch ($params){
            case "filter_approved":
                $workflow_validation = $this->workflowValidationStatus(1);
                break;
            case "filter_pending":
                $workflow_validation = $this->workflowValidationStatus(0);
                break;
            case "filter_rejected":
                $workflow_validation = $this->workflowValidationStatus(-1);
                break;
        }

        foreach ($workflow_validation as $key => $stateWorkflow){
            $result = '<tr>
                                            <th scope="row"><a href="#">#'. $key .'</a></th>
                                            <td>'. substr($stateWorkflow->media_name, 14) .'</td>
                                            <td><a href="#" class="text-primary">'.$stateWorkflow->users_name .'</a></td>
                                            <td>'. \Carbon\Carbon::parse($stateWorkflow->media_deadline)->diffForHumans() .'</td>';
            $result .= match ($stateWorkflow->final_statut_media) {
                0 => '<td><span class="badge bg-warning text-white">' . trans('global.waiting') . '</span></td>',
                1 => '<td><span class="badge bg-success text-white">' . trans('global.approved') . '</span></td>',
                default => '<td><span class="badge bg-danger text-white">' . trans('global.rejected') . '</span></td>',
            };
            $result .=   '</tr>';
        }
        return response()->json([
            'result' => $result,
            'status' => $params
        ]);
    }

    private function workflowValidationStatus ($status){
        return ValidationStep::select('media.file_name AS media_name', \DB::raw('MAX(validation_steps.id) AS max_id'),
            \DB::raw('GROUP_CONCAT(users.name SEPARATOR ", ") AS users_name'),
            \DB::raw('GROUP_CONCAT(validation_steps.statut SEPARATOR ", ") AS step_statut'),
            'media.statut AS final_statut_media', 'media.global_deadline AS media_deadline')
            ->join('media', 'validation_steps.media_id', '=', 'media.id')
            ->join('users', 'validation_steps.user_id', '=', 'users.id')
            ->where('validation_steps.user_id', auth()->id())
            ->where('media.statut', $status)
            ->groupBy('media.id', 'media.file_name', 'media.statut', 'media.global_deadline')
            ->orderBy('media.global_deadline')
            ->get();
    }
}

