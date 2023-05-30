@extends('layouts.front')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ trans('global.features') }}</h4>
                            <p class="card-category">{{ trans('global.details') }}</p>
                        </div>
                        <div class="card-body">

                            <div class="list-group">
                                <a href="{{ route('parapheur.show', $parapheur) }}" class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ trans('global.my') }} {{ trans('cruds.parapheur.title') }}</h5>
                                        <small>{{ $parapheur->medias->where('state', 'unlocked')->count() }} {{ trans('global.document') }}</small>
                                    </div>

                                    <small>{{ trans('global.last_document_saved') }}: {{ ($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first() != null) ? substr($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first()->file_name, 14) : "" }}</small>
                                    <small class="float-right">{{ ($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first() != null) ? Carbon\Carbon::parse($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first()->updated_at)->diffForHumans() : "" }}</small>
                                </a>
                                @can('ocr_access')
                                    <br style="margin-top: 20px">
                                    <a href="{{ route('openOCR') }}" class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">{{ trans('global.my') }} {{trans('panel.ocr')}}</h5>
                                            <small>{{ $parapheur->medias->where('state', 'unlocked')->count() }} {{ trans('global.document') }}</small>
                                        </div>

                                        <small>{{ trans('global.last_document_saved') }}: {{ ($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first() != null) ? substr($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first()->file_name, 14) : "" }}</small>
                                        <small class="float-right">{{ ($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first() != null) ? Carbon\Carbon::parse($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first()->updated_at)->diffForHumans() : "" }}</small>
                                    </a>
                                @endcan
                                @can('workflow_management_access_create')
                                    <br style="margin-top: 20px">
                                <a href="{{ route("workflow-create") }}" class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ trans('global.start_workflow_validation') }}</h5>
                                        <small>{{ $parapheur->medias->where('state', 'unlocked')->count() }} {{ trans('global.document') }}</small>
                                    </div>

                                    <small>{{ trans('global.last_document_saved') }}: {{ ($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first() != null) ? substr($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first()->file_name, 14) : "" }}</small>
                                    <small class="float-right">{{ ($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first() != null) ? Carbon\Carbon::parse($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first()->updated_at)->diffForHumans() : "" }}</small>
                                </a>
                                @endcan
                            </div>


                        </div>
                    </div>
                </div>
                <div class="col-md-6 dashboard">
                    <!-- Recent Activity -->
                    <div class="card" style="height: 398px;">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>{{ trans('global.filter') }}</h6>
                                </li>
                                <li><a class="dropdown-item" href="#" id="filter_day">{{ trans('global.today') }}</a></li>
                                <li><a class="dropdown-item" href="#" id="filter_month">{{ trans('global.this') }} {{ trans('global.month') }}</a></li>
                                <li><a class="dropdown-item" href="#" id="filter_year">{{ trans('global.this') }} {{ trans('global.year') }}</a></li>
                            </ul>
                        </div>

                        <div class="card-body overflow-auto">
                            <h5 class="card-title">{{ trans('global.my_recent_activity') }} <span class="dateType">| {{ trans('global.today') }}</span></h5>
                            <div id="filter_activity" class="activity">

                                @foreach($getActivity as $key => $activityList)

                                    <div class="activity-item d-flex">
                                        <div class="activite-label">{{ Carbon\Carbon::parse($activityList->created_at)->diffForHumans() }}</div>
                                        <i class='bi bi-circle-fill activity-badge
                                         @if ($key == 0  || $key == 3)
                                          {{'text-success'}}
                                          @elseif ($key == 1 || $key == 6)
                                          {{'text-danger'}}
                                         @elseif ($key == 2 || $key == 9)
                                         {{'text-primary'}}
                                         @elseif ($key == 3)
                                         {{'text-info'}}
                                         @elseif ($key == 4 || $key == 12)
                                        {{'text-warning'}}
                                         @else
                                         {{'text-muted'}}
                                         @endif
                                         align-self-start'></i>
                                        <div class="activity-content">
                                            {!! $activityList->message !!}
                                        </div>
                                    </div><!-- End activity item-->

                                @endforeach
                            </div>

                        </div>
                    </div><!-- End Recent Activity -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ trans('global.my_sites') }}</h4>
                            <p class="card-category">{{ trans('global.List_of_sites_you_belong_to') }}</p>
                        </div>
                        <div class="card-body ">

                            @foreach($getProjects as $key => $getProject)

                                <div class="list-group">
                                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">Site {{$key + 1}} : {{$getProject->name}}</h5>
                                            <small>{{ Carbon\Carbon::parse($getProject->created_at)->diffForHumans() }}</small>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Workflow validation -->
                    <div class="card dashboard recent-sales" style="height: 398px;">

                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>{{trans('global.filter')}}</h6>
                                </li>

                                <li><a class="dropdown-item" id="filter_approved" href="#">{{trans('global.approved')}}</a></li>
                                <li><a class="dropdown-item" id="filter_pending" href="#">{{trans('global.waiting')}}</a></li>
                                <li><a class="dropdown-item" id="filter_rejected" href="#">{{trans('global.rejected')}}</a></li>
                            </ul>
                        </div>

                        <div class="card-body overflow-auto">
                            <h5 class="card-title">{{ trans('global.workflow_validation') }} <span class="workflow_state">| {{ trans('global.all') }}</span></h5>

                            @if(count($workflow_validation) != 0)
                                @can('workflow_management_access_create')
                                <table class="table table-borderless datatable">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ trans('global.document') }}</th>
                                        <th scope="col">{{ trans('cruds.workflow_management.fields.users') }}</th>
                                        <th scope="col">{{ trans('global.deadline') }}</th>
                                        <th scope="col">{{ trans('global.status') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="workflow-v">
                                    @foreach($workflow_validation as $key => $stateWorkflow)
                                        <tr>
                                            <th scope="row"><a href="#">#{{ $key }}</a></th>
                                            <td>{{ substr($stateWorkflow->media_name, 14) ?? ''}}</td>
                                            <td><a href="#" class="text-primary">{{ $stateWorkflow->users_name ?? ''}}</a></td>
                                            <td>{{ \Carbon\Carbon::parse($stateWorkflow->media_deadline)->diffForHumans() ?? ''}}</td>
                                            @switch($stateWorkflow->final_statut_media)
                                                @case(0)
                                                <td><span class="badge bg-warning text-white">{{ trans('global.waiting') }}</span></td>
                                                @break
                                                @case(1)
                                                <td><span class="badge bg-success text-white">{{ trans('global.approved') }}</span></td>
                                                @break
                                                @default
                                                <td><span class="badge bg-danger text-white">{{ trans('global.rejected') }}</span></td>
                                            @endswitch
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @endcan
                            @else
                                <div class="list-group">
                                    @can('workflow_management_access_create')
                                        <h4>{{ trans('global.notes_title') }}</h4>
                                        <p>{{ trans('global.notes_body') }}</p>
                                        <a href="{{ route("workflow-create") }}" class="list-group-item list-group-item-action flex-column align-items-start">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">{{ trans('global.start_workflow_validation') }}</h5>
                                            </div>
                                        </a>
                                    @endcan
                                </div>
                            @endif

                        </div>

                    </div>
                    <!-- End Workflow validation -->
                </div>
                <div class="col-md-6 dashboard">
                    <div class="card card-tasks" style="height: 610px;">
                        <div class="card-header ">
                            <h4>{{trans('global.my')}} {{trans('global.document')}}</h4>
                            <p class="card-category">{{ trans('global.my_recent_activity') }}</p>
                        </div>
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>{{ trans('global.filter') }} </h6>
                                </li>
                                <li><a class="dropdown-item" href="#" id="filter_doc_day">{{ trans('global.today') }}</a></li>
                                <li><a class="dropdown-item" href="#" id="filter_doc_month">{{ trans('global.this') }} {{ trans('global.month') }}</a></li>
                                <li><a class="dropdown-item" href="#" id="filter_doc_year">{{ trans('global.this') }} {{ trans('global.year') }}</a></li>
                            </ul>
                        </div>

                        <div class="card-body overflow-auto">
                            <div class="table-full-width">
                                @foreach($getFolders as $key => $getFolder)

                                    @foreach($getFolder->media->sortByDesc('updated_at') as $getMedia)
                                        <div class="card">
                                            <div class="row no-gutters">
                                                <div class="col-sm-2">
                                                    <img class="img-thumbnail" width="75" src="{!! url('images\document2.png') !!}">
                                                </div>
                                                <div class="col-sm-10">
                                                    <div class="card-body" style="padding: 5px 5px 0;">
                                                        <h5 class="card-title">{{ strtolower(substr($getMedia->file_name, 14)) }}</h5>
                                                        <span>{{ trans('global.edit') }} {{Carbon\Carbon::parse($getMedia->updated_at)->diffForHumans()}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            // Javascript method's body can be found in assets/js/demos.js
            demo.initDashboardPageCharts();

            demo.showNotification();

        });
    </script>
@endpush--}}
