@extends('layouts.front')

@section('content')

    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">

            <div class="row">

                {{--<form action="{{route('post-upload-ocr')}}" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        @csrf
                        <label for="filechoose">Choose File</label>
                        <input type="file" name="file" class="form-control-file" id="filechoose">
                        <button class="btn btn-success mt-3" type="submit" name="submit">Upload</button>
                    </div>
                </form>--}}

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
                                                {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}

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
                                                {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
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
                                                    <a href="{{ route('folders.show', $item->id) }}" class="btn-link">Informations</a>
                                                </p>
                                                {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
                                            </div>
                                        </div>
                                        <span class="position-absolute badge rounded-pill badge-danger">{{$result->where('folders.id', $item->id)->count()}}</span>
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
                                                {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
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
                                                {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
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
                                                {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
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
                                                {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
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


    {{--<div class="container">
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
    </div>--}}
@stop
