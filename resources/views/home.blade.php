@extends('layouts.admin')
@section('content')
    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">

                <div class="pagetitle">
                    <h1>{{trans('global.dashboard')}}</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{trans('global.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('global.dashboard')}}</li>
                        </ol>
                    </nav>
                </div>
                <!-- End Page Title -->
                <section class="section dashboard">
                    <div class="row">

                        <!-- Left side columns -->
                        <div class="col-lg-8">
                            <div class="row">

                                <!-- Users Card -->
                                <div class="col-xxl-4 col-md-6">
                                    <div class="card info-card sales-card">

                                        <div class="filter">
                                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <li class="dropdown-header text-start">
                                                    <h6>{{trans('global.filter')}}</h6>
                                                </li>

                                                <li><a class="dropdown-item active" href="#">{{trans('global.all')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.today')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.month')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.year')}}</a></li>
                                            </ul>
                                        </div>

                                        <div class="card-body">
                                            <h5 class="card-title">{{trans('cruds.workflow_management.fields.users')}} <span>| {{trans('global.all')}}</span></h5>

                                            <div class="d-flex align-items-center">
                                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <div class="ps-3">
                                                    <h6>{{ $allUsers }}</h6>
                                                    <span class="text-success small pt-1 fw-bold">{{ trans('global.online') }}</span> <span class="text-muted small pt-2 ps-1">1</span>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div><!-- End Users Card -->

                                <!-- Medias Card -->
                                <div class="col-xxl-4 col-md-6">
                                    <div class="card info-card revenue-card">

                                        <div class="filter">
                                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <li class="dropdown-header text-start">
                                                    <h6>Filter</h6>
                                                </li>

                                                <li><a class="dropdown-item" href="#">Today</a></li>
                                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                                <li><a class="dropdown-item" href="#">This Year</a></li>
                                            </ul>
                                        </div>

                                        <div class="card-body">
                                            <h5 class="card-title">{{trans('global.document')}} <span>| {{trans('global.all')}}</span></h5>

                                            <div class="d-flex align-items-center">
                                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-file"></i>
                                                </div>
                                                <div class="ps-3">
                                                    <h6>{{ $allMedias }}</h6>
                                                    <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div><!-- End Medias Card -->

                                <!-- Folders Card -->
                                <div class="col-xxl-4 col-md-6">
                                    <div class="card info-card sales-card">

                                        <div class="filter">
                                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <li class="dropdown-header text-start">
                                                    <h6>{{trans('global.filter')}}</h6>
                                                </li>

                                                <li><a class="dropdown-item active" href="#">{{trans('global.all')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.today')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.month')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.year')}}</a></li>
                                            </ul>
                                        </div>

                                        <div class="card-body">
                                            <h5 class="card-title">{{trans('global.folder')}} <span>| {{trans('global.all')}}</span></h5>

                                            <div class="d-flex align-items-center">
                                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-folder"></i>
                                                </div>
                                                <div class="ps-3">
                                                    <h6>{{ $allFolders }}</h6>
                                                    <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div><!-- End Folders Card -->

                                <!-- Projects Card -->
                                <div class="col-xxl-4 col-md-6">
                                    <div class="card info-card revenue-card">

                                        <div class="filter">
                                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <li class="dropdown-header text-start">
                                                    <h6>{{trans('global.filter')}}</h6>
                                                </li>

                                                <li><a class="dropdown-item" href="#">{{trans('global.today')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.month')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.year')}}</a></li>
                                            </ul>
                                        </div>

                                        <div class="card-body">
                                            <h5 class="card-title">{{trans('cruds.project.title')}} <span>| {{trans('global.all')}}</span></h5>

                                            <div class="d-flex align-items-center">
                                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="fa fa-book"></i>
                                                </div>
                                                <div class="ps-3">
                                                    <h6>{{ $allProjects }}</h6>
                                                    <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div><!-- End Projects Card -->

                                <!-- Events Card -->
                                <div class="col-xxl-4 col-md-6">
                                    <div class="card info-card sales-card">

                                        <div class="filter">
                                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <li class="dropdown-header text-start">
                                                    <h6>{{trans('global.filter')}}</h6>
                                                </li>

                                                <li><a class="dropdown-item active" href="#">{{trans('global.all')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.today')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.month')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.year')}}</a></li>
                                            </ul>
                                        </div>

                                        <div class="card-body">
                                            <h5 class="card-title">{{trans('global.event')}} <span>| {{trans('global.all')}}</span></h5>

                                            <div class="d-flex align-items-center">
                                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-calendar-event"></i>
                                                </div>
                                                <div class="ps-3">
                                                    <h6>{{ $allEvents }}</h6>
                                                    <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div><!-- End Folders Card -->

                                <!-- message Card -->
                                <div class="col-xxl-4 col-md-6">
                                    <div class="card info-card revenue-card">

                                        <div class="filter">
                                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <li class="dropdown-header text-start">
                                                    <h6>{{trans('global.filter')}}</h6>
                                                </li>

                                                <li><a class="dropdown-item" href="#">{{trans('global.today')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.month')}}</a></li>
                                                <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.year')}}</a></li>
                                            </ul>
                                        </div>

                                        <div class="card-body">
                                            <h5 class="card-title">{{trans('global.message')}} <span>| {{trans('global.all')}}</span></h5>

                                            <div class="d-flex align-items-center">
                                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-messenger"></i>
                                                </div>
                                                <div class="ps-3">
                                                    <h6>{{ $allMessages }}</h6>
                                                    <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div><!-- End Projects Card -->

                            </div>
                        </div><!-- End Left side columns -->

                        <!-- Right side columns -->
                        <div class="col-md-4">
                            <!-- Recent Activity -->
                            <div class="card" style="height: 498px;">
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
                        </div><!-- End Right side columns -->

                        <div class="col-xxl-4 col-md-4">
                            <div class="card">
                                <div class="card-header">{{ trans('global.dashboard') }} {{ trans('global.user_created_by_month') }}</div>
                                <div class="card-body">
                                    <h1>{{ $chart_users->options['chart_title'] }}</h1>
                                    {!! $chart_users->renderHtml() !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-8 col-md-8">
                            <div class="card">
                                <div class="card-header">{{ trans('global.dashboard') }} {{ trans('global.document_created_by_day') }}</div>
                                <div class="card-body">
                                    <h1>{{ $chart_medias->options['chart_title'] }}</h1>
                                    {!! $chart_medias->renderHtml() !!}
                                </div>
                            </div>
                        </div>

                        <!-- Workflow validation -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">

                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>{{trans('global.filter')}}</h6>
                                        </li>

                                        <li><a class="dropdown-item" href="#">{{trans('global.today')}}</a></li>
                                        <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.month')}}</a></li>
                                        <li><a class="dropdown-item" href="#">{{trans('global.this')}} {{trans('global.year')}}</a></li>
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">{{ trans('global.workflow_validation') }} <span>| {{ trans('global.all') }}</span></h5>

                                    <table class="table table-borderless datatable">
                                        <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">{{ trans('global.document') }}</th>
                                            <th scope="col">{{ trans('cruds.workflow_management.fields.users') }}</th>
                                            <th scope="col">{{ trans('global.created_by') }}</th>
                                            <th scope="col">{{ trans('global.date_validation') }}</th>
                                            <th scope="col">{{ trans('global.deadline') }}</th>
                                            <th scope="col">{{ trans('global.status') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($workflow_validation as $key => $stateWorkflow)
                                            <tr>
                                                <th scope="row"><a href="#">#{{ $key }}</a></th>
                                                <td>{{ substr($stateWorkflow->media_name, 14) ?? ''}}</td>
                                                <td><a href="#" class="text-primary">{{ $stateWorkflow->users_name ?? ''}}</a></td>
                                                <td>{{ $stateWorkflow->start_workflow_by ?? ''}}</td>
                                                <td>{{ \Carbon\Carbon::parse($stateWorkflow->date_validation )->diffForHumans()?? ''}}</td>
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

                                </div>

                            </div>
                        </div><!-- End Workflow validation -->

                    </div>
                </section>
            </div>

        </div>
    </div>
    <!--**********************************
        Content body end
    ***********************************-->
@endsection




@section('javascript')
    {!! $chart_users->renderChartJsLibrary() !!}
    {!! $chart_users->renderJs() !!}
    {!! $chart_medias->renderJs() !!}
@endsection

@section('scripts')
@parent
@endsection
