<?php

namespace App\Http\Controllers\Admin;
use App\Models\Event;
use App\Models\Folder;
use App\Models\Project;
use App\Models\User;
use App\Models\ValidationStep;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HomeController
{
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
        //dd($workflow_validation->toArray());
        return view('home', compact('chart_users', 'chart_medias', 'allUsers', 'allMedias', 'allFolders', 'allProjects', 'allEvents', 'allMessages', 'workflow_validation'));
    }
}
