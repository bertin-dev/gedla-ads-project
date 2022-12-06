@extends('layouts.admin')
@section('content')
    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">

            <div class="row">

             {{--   <form action="{{route('post-upload-ocr')}}" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        @csrf
                        <label for="filechoose">Choose File</label>
                        <input type="file" name="file" class="form-control-file" id="filechoose">
                        <button class="btn btn-success mt-3" type="submit" name="submit">Upload</button>
                    </div>
                </form>--}}

                @can('ocr_access')
                <div class="col-lg-4">
                    <div class="card">
                        <div class="row no-gutters">
                            <div class="col-sm-4">
                                <img class="img-thumbnail" src="{!! url('images\menu_items\ocr.jpg') !!}" alt="Ocr" title="Ocr">
                            </div>
                            <div class="col-sm-8">
                                <div class="card-body" style="padding: 5px 5px 0;">
                                    <h5 class="card-title">OCR</h5>
                                    <p class="text-right">
                                        <i class="icon-user"></i>
                                        <a href="#" class="btn-link">Acquisition des documents</a>
                                    </p>
                                    {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                @can('page_access')
                <div class="col-lg-4">
                    <div class="card">
                        <div class="row no-gutters">
                            <div class="col-sm-4">
                                <img class="img-thumbnail" src="{!! url('images\menu_items\page.jpg') !!}" alt="Page" title="Page">
                            </div>
                            <div class="col-sm-8">
                                <div class="card-body" style="padding: 5px 5px 0;">
                                    <h5 class="card-title">PAGE</h5>
                                    <p class="text-right">
                                        <i class="icon-user"></i>
                                        <a href="#" class="btn-link">Informations</a>
                                    </p>
                                    {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @endcan

                @can('paraph_access')
                <div class="col-lg-4">
                    <div class="card">
                        <div class="row no-gutters">
                            <div class="col-sm-4">
                                <img class="img-thumbnail" src="{!! url('images\menu_items\paraph.jpg') !!}" alt="Parapheur" title="Parapheur">
                            </div>
                            <div class="col-sm-8">
                                <div class="card-body" style="padding: 5px 5px 0;">
                                    <h5 class="card-title">PARAPHEUR</h5>
                                    <p class="text-right">
                                        <i class="icon-user"></i>
                                        <a href="#" class="btn-link">Informations</a>
                                    </p>
                                    {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @endcan

                @can('safe_access')
                <div class="col-lg-4">
                    <div class="card">
                        <div class="row no-gutters">
                            <div class="col-sm-4">
                                <img class="img-thumbnail" src="{!! url('images\menu_items\safe.jpg') !!}" alt="Safe" title="Safe">
                            </div>
                            <div class="col-sm-8">
                                <div class="card-body" style="padding: 5px 5px 0;">
                                    <h5 class="card-title">SAFE</h5>
                                    <p class="text-right">
                                        <i class="icon-user"></i>
                                        <a href="#" class="btn-link">Informations</a>
                                    </p>
                                    {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @endcan

                @can('lunch_access')
                <div class="col-lg-4">
                    <div class="card">
                        <div class="row no-gutters">
                            <div class="col-sm-4">
                                <img class="img-thumbnail" src="{!! url('images\menu_items\lunch.jpg') !!}" alt="Lunch" title="lunch">
                            </div>
                            <div class="col-sm-8">
                                <div class="card-body" style="padding: 5px 5px 0;">
                                    <h5 class="card-title">LUNCH</h5>
                                    <p class="text-right">
                                        <i class="icon-user"></i>
                                        <a href="#" class="btn-link">Informations</a>
                                    </p>
                                    {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @endcan

                @can('dispath_access')
                <div class="col-lg-4">
                    <div class="card">
                        <div class="row no-gutters">
                            <div class="col-sm-4">
                                <img class="img-thumbnail" src="{!! url('images\menu_items\dispath.jpg') !!}" alt="Dispath" title="Dispath">
                            </div>
                            <div class="col-sm-8">
                                <div class="card-body" style="padding: 5px 5px 0;">
                                    <h5 class="card-title">DISPATH</h5>
                                    <p class="text-right">
                                        <i class="icon-user"></i>
                                        <a href="#" class="btn-link">Informations</a>
                                    </p>
                                    {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @endcan

                @can('design_access')
                <div class="col-lg-4">
                    <div class="card">
                        <div class="row no-gutters">
                            <div class="col-sm-4">
                                <img class="img-thumbnail" src="{!! url('images\menu_items\design.jpg') !!}" alt="design" title="Design">
                            </div>
                            <div class="col-sm-8">
                                <div class="card-body" style="padding: 5px 5px 0;">
                                    <h5 class="card-title">DESIGN</h5>
                                    <p class="text-right">
                                        <i class="icon-user"></i>
                                        <a href="#" class="btn-link">Informations</a>
                                    </p>
                                    {{--  <a href="#" class="btn btn-primary">View Profile</a>--}}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @endcan

            </div>

        </div>
    </div>
    <!--**********************************
        Content body end
    ***********************************-->
@endsection
@section('scripts')
@parent

@endsection
