{{--@extends('layouts.front')

@section('content')

    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">

            <div class="row">

                @foreach($functionality as $item)

                    @php
                        $result = \DB::table('media')->join('folders', 'media.model_id', '=', 'folders.id')
                        ->where('functionality', '=', true);


                    @endphp

                    @switch($item->id)
                        @case(1)
                        @can('ocr_access')
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="row no-gutters">
                                        <div class="col-sm-4">
                                            <img class="img-thumbnail" src="{!! url('images\menu_items\ocr.jpg') !!}"
                                                 alt="{{trans('panel.ocr')}}" title="{{trans('panel.ocr')}}">
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="card-body" style="padding: 5px 5px 0;">
                                                <h5 class="card-title">OCR</h5>
                                                <p class="text-right">
                                                    <i class="icon-user"></i>
                                                    <a href="{{ route('openOCR') }}"
                                                       class="btn-link"> {{trans('panel.acquisition_by')}} {{trans('panel.ocr')}}</a>
                                                </p>
                                                --}}{{--  <a href="#" class="btn btn-primary">View Profile</a>--}}{{--

                                            </div>
                                        </div>
                                        <span class="position-absolute badge rounded-pill badge-danger">{{$result->where('folders.id', $item->id)->count()}}</span>
                                    </div>
                                </div>
                            </div>
                        @endcan
                        @break

                        @case(2)
                        @can('page_access')
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="row no-gutters">
                                        <div class="col-sm-4">
                                            <img class="img-thumbnail" src="{!! url('images\menu_items\page.jpg') !!}"
                                                 alt="Page" title="Page">
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="card-body" style="padding: 5px 5px 0;">
                                                <h5 class="card-title">PAGE</h5>
                                                <p class="text-right">
                                                    <i class="icon-user"></i>
                                                    <a href="{{ route('folders.show', [$item]) }}" class="btn-link">{{$item->name}}</a>
                                                </p>
                                                --}}{{--  <a href="#" class="btn btn-primary">View Profile</a>--}}{{--
                                            </div>
                                        </div>
                                        <span class="position-absolute badge rounded-pill badge-danger">{{$result->where('folders.id', $item->id)->count()}}</span>
                                    </div>
                                </div>

                            </div>
                        @endcan
                        @break

                        @case(3)
                        @can('paraph_access')
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="row no-gutters">
                                        <div class="col-sm-4">
                                            <img class="img-thumbnail" src="{!! url('images\menu_items\paraph.jpg') !!}"
                                                 alt="Parapheur" title="Parapheur">
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="card-body" style="padding: 5px 5px 0;">
                                                <h5 class="card-title">PARAPHEUR</h5>
                                                <p class="text-right">
                                                    <i class="icon-user"></i>
                                                    <a href="{{ route('parapheur.show') }}?parapheur_id={{$parapheur->id}}" class="btn-link">Ouvrir</a>
                                                </p>
                                                --}}{{--  <a href="#" class="btn btn-primary">View Profile</a>--}}{{--
                                            </div>
                                        </div>
                                        <span class="position-absolute badge rounded-pill badge-danger">
                                                {{ $parapheur->medias->where('state', 'unlocked')->where('signing', 0)->count() }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        @endcan
                        @break

                        @case(4)
                        @can('safe_access')
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="row no-gutters">
                                        <div class="col-sm-4">
                                            <img class="img-thumbnail" src="{!! url('images\menu_items\safe.jpg') !!}"
                                                 alt="Safe" title="Safe">
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="card-body" style="padding: 5px 5px 0;">
                                                <h5 class="card-title">SAFE</h5>
                                                <p class="text-right">
                                                    <i class="icon-user"></i>
                                                    <a href="{{ route('folders.show', $item->id) }}" class="btn-link">Informations</a>
                                                </p>
                                                --}}{{--  <a href="#" class="btn btn-primary">View Profile</a>--}}{{--
                                            </div>
                                        </div>
                                        <span class="position-absolute badge rounded-pill badge-danger">{{$result->where('folders.id', $item->id)->count()}}</span>
                                    </div>
                                </div>

                            </div>
                        @endcan
                        @break

                        @case(5)
                        @can('lunch_access')
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="row no-gutters">
                                        <div class="col-sm-4">
                                            <img class="img-thumbnail" src="{!! url('images\menu_items\lunch.jpg') !!}"
                                                 alt="Lunch" title="lunch">
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="card-body" style="padding: 5px 5px 0;">
                                                <h5 class="card-title">LUNCH</h5>
                                                <p class="text-right">
                                                    <i class="icon-user"></i>
                                                    <a href="{{ route('folders.show', $item->id) }}" class="btn-link">Informations</a>
                                                </p>
                                                --}}{{--  <a href="#" class="btn btn-primary">View Profile</a>--}}{{--
                                            </div>
                                        </div>
                                        <span class="position-absolute badge rounded-pill badge-danger">{{$result->where('folders.id', $item->id)->count()}}</span>
                                    </div>
                                </div>

                            </div>
                        @endcan
                        @break

                        @case(6)
                        @can('dispath_access')
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="row no-gutters">
                                        <div class="col-sm-4">
                                            <img class="img-thumbnail" src="{!! url('images\menu_items\dispath.jpg') !!}"
                                                 alt="Dispath" title="Dispath">
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="card-body" style="padding: 5px 5px 0;">
                                                <h5 class="card-title">DISPATH</h5>
                                                <p class="text-right">
                                                    <i class="icon-user"></i>
                                                    <a href="{{ route('folders.show', $item->id) }}" class="btn-link">Informations</a>
                                                </p>
                                                --}}{{--  <a href="#" class="btn btn-primary">View Profile</a>--}}{{--
                                            </div>
                                        </div>
                                        <span class="position-absolute badge rounded-pill badge-danger">{{$result->where('folders.id', $item->id)->count()}}</span>
                                    </div>
                                </div>

                            </div>
                        @endcan
                        @break

                        @case(7)
                        @can('design_access')
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="row no-gutters">
                                        <div class="col-sm-4">
                                            <img class="img-thumbnail" src="{!! url('images\menu_items\design.jpg') !!}"
                                                 alt="design" title="Design">
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="card-body" style="padding: 5px 5px 0;">
                                                <h5 class="card-title">DESIGN</h5>
                                                <p class="text-right">
                                                    <i class="icon-user"></i>
                                                    <a href="{{ route('folders.show', $item->id) }}" class="btn-link">Informations</a>
                                                </p>
                                                --}}{{--  <a href="#" class="btn btn-primary">View Profile</a>--}}{{--
                                            </div>
                                        </div>
                                        <span class="position-absolute badge rounded-pill badge-danger">{{$result->where('folders.id', $item->id)->count()}}</span>
                                    </div>
                                </div>

                            </div>
                        @endcan
                        @break

                    @endswitch
                @endforeach

            </div>

        </div>
    </div>
    <!--**********************************
        Content body end
    ***********************************-->


    --}}{{--<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">My Assigned Projects</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="row">
                            @foreach ($projects as $project)
                                <div class="col-lg-2 col-md-3 col-sm-4 mb-3">
                                    <div class="card">
                                        <a href="{{ route('folders.show', $project) }}">
                                            <img class="card-img-top" src="{{ $project->thumbnail ? $project->thumbnail->thumbnail : url('images/no-image.png') }}" alt="{{ $project->name }}">
                                        </a>
                                        <div class="card-footer text-center">
                                            <a href="{{ route('projects.show', $project) }}">
                                                {{ $project->name }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>--}}{{--
@stop--}}



@extends('layouts.front')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h4>Bienvenue {{ auth()->user()->name }}</h4>
                    <p>Dernière connexion: {{ Carbon\Carbon::parse(auth()->user()->last_login_at)->diffForHumans() }}</p>
                </div>
                <div class="col-md-5">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">Accessibilité</h4>
                            <p class="card-category">Details</p>
                        </div>
                        <div class="card-body ">


                            <div class="list-group">
                                <a href="{{ route('parapheur.show') }}?parapheur_id={{$parapheur->id}}" class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Mon Parapheur</h5>
                                        <small>{{ $parapheur->medias->where('state', 'unlocked')->count() }} documents</small>
                                    </div>

                                    <small>Dernier document Enregistré: {{ ($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first() != null) ? substr($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first()->file_name, 14) : "" }}</small>
                                    <small class="float-right">{{ ($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first() != null) ? Carbon\Carbon::parse($parapheur->medias->where('state', 'unlocked')->sortByDesc('updated_at')->first()->updated_at)->diffForHumans() : "" }}</small>
                                </a>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">Mes activités</h4>
                            <p class="card-category">Liste de toutes mes activités</p>
                        </div>
                        <div class="card-body overflow-auto">
                            @foreach($getActivity as $key => $activityList)
                                <div class="media">
                                    <img class="mr-3 rounded-circle" width="50" src="{{asset('images/profil.png')}}" alt="Generic placeholder image">
                                    <div class="media-body">
                                        <h5 class="mt-0">{{$activityList->name ?? ''}}</h5>
                                        {{$activityList->message ?? $activityList->description ?? ''}}
                                        <small class="float-right">{{ Carbon\Carbon::parse($activityList->created_at)->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">Mes Sites</h4>
                            <p class="card-category">Liste des sites auxquels vous appartenez</p>
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
                </div>
                <div class="col-md-6">
                    <div class="card  card-tasks">
                        <div class="card-header ">
                            <h4 class="card-title">Mes Documents</h4>
                            <p class="card-category">Mes récentes modifications</p>
                        </div>
                        <div class="card-body ">
                            <div class="table-full-width">
                                @foreach($getFolders as $key => $gedFolder)

                                    @foreach($gedFolder->media->sortByDesc('updated_at') as $gedMedia)
                                        <div class="card">
                                            <div class="row no-gutters">
                                                <div class="col-sm-4">
                                                    <img class="img-thumbnail" width="75" src="{!! url('images\document2.png') !!}">
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="card-body" style="padding: 5px 5px 0;">
                                                        <h5 class="card-title">{{ strtolower(substr($gedMedia->file_name, 14)) }}</h5>
                                                        <span>Modifié {{Carbon\Carbon::parse($gedMedia->updated_at)->diffForHumans()}}</span>
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
