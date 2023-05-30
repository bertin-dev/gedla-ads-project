<?php

namespace App\Http\Controllers\Admin;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Folder;
use App\Models\Project;
use App\Models\User;
use App\Models\ValidationStep;
use Carbon\Carbon;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HomeController
{
    private $operationTypes = ["SEND_DOCUMENT", "VALIDATE_DOCUMENT", "VALIDATE_DOCUMENT_SIGNATURE",
        "SEND_DOCUMENT_SIGNATURE", "VALIDATE_DOCUMENT_PARAPHEUR", "SEND_DOCUMENT_PARAPHEUR", "OPEN_DOCUMENT", "PREVIEW_DOCUMENT",
        "START_VALIDATION", "REJECTED_DOCUMENT", "EDIT_DOCUMENT", "SAVE_DOCUMENT", "DOWNLOAD_DOCUMENT", "ARCHIVE_DOCUMENT", "IMPORT_DOCUMENT",
        "RESTORE_ARCHIVE_DOCUMENT"];

    public function index()
    {
        $chart_options_users = [
            'chart_title' => trans('global.user_created_by_month'),
            'report_type' => 'group_by_date',
            'model' => User::class,
            'group_by_field' => 'created_at',
            'group_by_period' => 'month',
            'chart_type' => 'pie',
        ];
        $chart_options_medias = [
            'chart_title' => trans('global.document_created_by_day'),
            'report_type' => 'group_by_date',
            'model' => Media::class,
            'group_by_field' => 'created_at',
            'group_by_period' => 'day',
            'chart_type' => 'line',
        ];
        $chart_users = new LaravelChart($chart_options_users);
        $chart_medias = new LaravelChart($chart_options_medias);

        $allUsers = User::all()->count();
        $allMedias = Media::all()->count();
        $allFolders = Folder::all()->count();
        $allProjects = Project::all()->count();
        $allEvents = Event::all()->count();
        $allMessages = \DB::table('ch_messages')->count();

        $workflow_validation = ValidationStep::select('media.name AS media_name', \DB::raw('MAX(validation_steps.id) AS max_id'),
            \DB::raw('GROUP_CONCAT(users.name SEPARATOR ", ") AS users_name'),
            \DB::raw('GROUP_CONCAT(validation_steps.statut SEPARATOR ", ") AS step_statut'),
            'media.statut AS final_statut_media', 'media.global_deadline AS media_deadline', 'validation_steps.date_validation AS date_validation',
             'validation_steps.start_workflow_by AS start_workflow_by')
            ->join('media', 'validation_steps.media_id', '=', 'media.id')
            ->join('users', 'validation_steps.user_id', '=', 'users.id')
            ->groupBy('media.id', 'media.name', 'media.statut', 'media.global_deadline', 'validation_steps.start_workflow_by', 'validation_steps.date_validation')
            ->get();

        $getActivity = AuditLog::whereIn('operation_type', $this->operationTypes)
            ->get()
            ->sortByDesc('created_at');


        //dd($workflow_validation->toArray());
        return view('home', compact('chart_users', 'chart_medias', 'allUsers', 'allMedias', 'allFolders', 'allProjects', 'allEvents', 'allMessages', 'workflow_validation', 'getActivity'));
    }

    public function filterAllActivityByDate($params){
        $getActivity = "";
        $dateType = "";
        switch ($params){
            case "filter_day":
                $dateType = "day";
                $date = date('Y-m-d', time());
                $getActivity = AuditLog::whereIn('operation_type', $this->operationTypes)
                    ->whereDate('created_at', $date)
                    ->orderByDesc('created_at')
                    ->get();
                break;
            case "filter_month":
                $dateType = "month";
                $month = date('m', time());
                $year = date('Y', time());
                $getActivity = AuditLog::whereIn('operation_type', $this->operationTypes)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderByDesc('created_at')
                    ->get();
                break;
            case "filter_year":
                $dateType = "year";
                $year = date('Y', time());
                $getActivity = AuditLog::whereIn('operation_type', $this->operationTypes)
                    ->whereYear('created_at', $year)
                    ->orderByDesc('created_at')
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
}
