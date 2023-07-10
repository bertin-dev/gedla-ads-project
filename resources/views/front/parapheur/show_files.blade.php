@extends('layouts.front')

@section('content')

    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-12 form-row">
                    <div class="col-md-7 mb-3">
                        @can('document_upload')
                            <a class="btn btn-outline-success"
                               href="{{ route('parapheur.upload') }}?parapheur_id={{ $parapheurWithMedia->id }}">
                                {{trans('global.upload_file')}}
                            </a>
                        @endcan
                        @can('document_create')
                            <a class="btn btn-outline-primary"
                               href="{{ route('create-document') }}?parapheur_id={{ $parapheurWithMedia->id }}">
                                {{ trans('global.create') }} {{trans('global.document')}}
                            </a>
                        @endcan
                        {{--<a class="btn btn-outline-danger" href="--}}{{--{{ route('folders.create') }}?parent_id={{ $folderItems->id }}--}}{{--">
                             Create a new folder
                        </a>--}}
                    </div>

                    <div class="col-md-5 mb-3">
                        <form action="{{--{{ route('search', $folder) }}--}}" method="GET" class="navbar-search"
                              role="search">
                            <div class="form-row">
                                <div class="col-md-5 mb-3 small">
                                    <select name="type" class="custom-select">
                                        <option value="">Tous les types</option>
                                        <option value="application/pdf">PDF</option>
                                        <option
                                            value="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                            Word
                                        </option>
                                        <option value="excel">Excel</option>
                                        <option
                                            value="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                            Excel
                                        </option>
                                        <option
                                            value="application/vnd.openxmlformats-officedocument.presentationml.presentation">
                                            PowerPoint
                                        </option>
                                        <option value="application/vnd.ms-visio.drawing.main+xml">Visio</option>
                                        <option value="text/plain">Text</option>
                                        <option value="image/jpeg">Image</option>
                                    </select>
                                </div>
                                <div class="col-md-7 mb-3">
                                    <div class="input-group">
                                        <input id="search_content" type="text" name="q" class="form-control"
                                               placeholder="Search ..." aria-describedby="inputGroupPrepend2" required
                                               value="{{ request()->get('q') }}">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                        <div id="output_search" class="invalid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="col-lg-12" style="margin-top: 20px">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>


                @if($parapheurWithMedia->medias->isEmpty())
                    @include('front.parapheur.upload', ['parapheur' => $parapheurWithMedia->medias])
                @endif

                @php
                    $getMedias = $parapheurWithMedia->medias
                    ->where('archived', 0)
                    ->where('saved', 0)
                    ->where('state', 'unlocked')
                    ->where('visibility', 'private')
                    ->sortByDesc('created_at');
                @endphp

                @foreach ($getMedias as $file)
                    @php
                        $result=match($file->mime_type){"application/pdf"=>url('images/pdf.png'),"text/plain"=>url('images/txt.png'),"application/vnd.openxmlformats-officedocument.wordprocessingml.document"=>url('images/docx.png'),"application/x-msaccess"=>url('images/access.png'),"application/vnd.ms-visio.drawing.main+xml"=>url('images/visio.png'),"application/vnd.openxmlformats-officedocument.presentationml.presentation"=>url('images/power_point.png'),"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"=>url('images/xlsx.png'),"image/jpeg"=>url('images/file-thumbnail.png'),default => '',};
                        $realSize = number_format($file->size/1024, 1, '.', '');
                    @endphp
                    <div class="col-lg-4">
                        <div class="card" data-toggle="modal" data-target=".bd-example-modal-lg"
                             data-id="{{$file->id}}"
                             data-name="{{ucfirst(strtolower(Str::substr($file->file_name, 14)))}}"
                             data-size="{{$realSize}}"

                             data-item_type="{{$result}}" data-url="{{$file->getUrl()}}"
                             data-version="{{$file->version}}" data-mime_type="{{$file->mime_type}}">
                            <div class="row no-gutters">
                                <div class="col-sm-4">
                                    <img class="img-thumbnail" src="{{ $result ?? url('images/file-thumbnail.png') }}"
                                         alt="{{ $file->name }}" title="{{ $file->name }}">
                                </div>
                                <div class="col-sm-8">
                                    <div class="card-body" style="padding: 5px 5px 0;">
                                        <h5 class="card-title">{{ strtolower(Str::substr($file->file_name, 14, 22)) }}</h5>
                                        <div style="margin-top: 13px">
                                                <span><small style="margin-right: 70px">
                                                        {{($file->created_at==$file->updated_at) ? trans('global.create')." " . Carbon\Carbon::parse($file->created_at)->diffForHumans() . " " . ($file->createdBy != null ? trans('global.by')." ".ucfirst($file->createdBy->name) : "") : trans('global.edit')." " . Carbon\Carbon::parse($file->updated_at)->diffForHumans() . " " . ($file->updatedBy != null ? trans('global.by')." ".ucfirst($file->updatedBy->name) : "") }}
                                                    </small></span>
                                            <span> <small class="text-right">{{$realSize}} KO</small></span>
                                        </div>
                                        @if($file->priority == "high")
                                            <span class="badge rounded-pill badge-danger">.</span>
                                        @elseif($file->priority == "medium")
                                            <span class="badge rounded-pill badge-warning">.</span>
                                        @else
                                            <span class="badge rounded-pill badge-info">.</span>
                                        @endif


                                        <span>
                                                 @php
                                                     $getValidationData = $getValidationDatas->where('media_id', $file->id)->get();
                                                 @endphp

                                            @if(count($getValidationData) != 0)
                                                @foreach($getValidationData as $data)
                                                    @if($data->statut == 1)
                                                        <small class="alert-success">validate</small>
                                                    @else
                                                        <small class="alert-success">receive</small>
                                                    @endif
                                                @endforeach
                                                <small class="alert-link" style="font-size: 9px;">{{trans('global.expiry')}}: {{now()->diffForHumans($file->globa_deadline)}}</small>
                                            @else
                                                <small class="alert-info">import</small>
                                            @endif

                                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

        </div>
    </div>
    <!--**********************************
        Content body end
    ***********************************-->

    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title file-title">{{trans('global.file_details')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-7">

                                <div class="iframeFile"></div>
                                {{--<embed class="iframeFile" id='100' width='100%' height='600px'/>--}}
                                <div class="text-left">
                                    @can('open_file_access')
                                        <a class="myUrl col-lg-4 open-document"
                                           target="_blank">{{trans('global.open')}} </a>
                                    @endcan
                                    @can('edit_file_access')
                                        <a class="col-lg-4 mediaId" target="_blank"
                                           href="{{ route('edit-document') }}?parapheur_id={{ $parapheurWithMedia->id }}">{{trans('global.edit_document')}}</a>
                                    @endcan
                                    @can('download_access')
                                        <a href="#" class="mediaDownload col-lg-4"> {{trans('global.download')}}</a>
                                    @endcan
                                    @can('storage_access')
                                        <a href="#"
                                           class="col-lg-4 documentStorage">{{trans('global.store_document')}} </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="col-md-5 ml-auto">
                                <span class="folder_id col-lg-4"></span>
                                <span class="folder_size col-lg-4"></span>
                                <span class="version col-lg-4"></span>
                                <hr>

                                <div class="">
                                    <h5>{{trans('global.my_recent_activity')}}</h5>
                                    <div class="loading">
                                        <center><img src="{{asset('images/loading.gif')}}" alt=""></center>
                                    </div>
                                    <h6 class="initMyActivity"></h6>
                                    <ul class="myActivity"></ul>
                                </div>

                                <div class="card workflow_form" style="display: none">
                                    <div class="card-header">{{trans('global.workflow_validation')}}</div>

                                    <div class="card-body">
                                        @if (session('status') || session()->has('message'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session('status') ?? session()->get('message')}}
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('operation.store') }}">
                                            @csrf
                                            <input type="hidden" name="send_validation_workflow"
                                                   value="send_validation_workflow">
                                            <input id="media_id" type="hidden" name="media_id"/>
                                            <input type="hidden" name="parapheur_id"
                                                   value="{{$parapheurWithMedia->id}}"/>
                                            <div class="form-group">
                                                <label for="deadline">{{trans('global.term')}}</label>
                                                <input type="date" id="deadline" name="deadline" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label for="priority">{{trans('global.priority')}}</label>
                                                <select name="priority" id="priority" class="form-control">
                                                    <option value="low">{{trans('global.low')}}</option>
                                                    <option value="medium">{{trans('global.means')}}</option>
                                                    <option value="high">{{trans('global.important')}}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="visibility">{{trans('global.visibility')}}</label>
                                                <select name="visibility" id="visibility" class="form-control">
                                                    <option value="public">{{trans('global.public')}}</option>
                                                    <option value="private">{{trans('global.private')}}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="user_assign">{{trans('global.assigned_to')}}</label>
                                                <select name="user_assign" id="user_assign" class="form-control">
                                                    @foreach(\App\Models\User::all() as $user)
                                                        @if($user->id != Auth::user()->id)
                                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="message">{{trans('global.message')}}</label>
                                                <textarea name="message" id="message" class="form-control"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <div class="form-check checkbox">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="flexCheckChecked" id="flexCheckChecked"
                                                           style="vertical-align: middle;">
                                                    <label class="form-check-label" for="flexCheckChecked"
                                                           style="vertical-align: middle;">
                                                        {{trans('global.send_notification_by_email')}}
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <button type="submit"
                                                        class="btn bg-card-violet text-white">{{trans('global.send')}}</button>
                                                <button type="reset"
                                                        class="btn bg-card-green text-white">{{trans('global.cancel')}}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <h5 class="modal-title">{{ trans('global.file_action') }}</h5><br>
                    <ul>
                        @can('validate_file_access')
                            <li><a href="#" class="document_id validation validate_file_access"
                                   title="validation">{{trans('global.validate')}}</a></li>
                            <li><a href="#" class="document_id validation_signature validate_file_access"
                                   title="validation_signature">validation avec signature</a></li>
                            <li><a href="#" class="validation_paraphe validate_file_access" title="validation_paraphe">validation
                                    avec paraphe</a></li>
                            <li><a href="#" class="document_id rejected validate_file_access" title="rejected">Rejeter/refuser</a>
                            </li>
                        @endcan
                        @can('operation_access')
                            <li><a href="#"
                                   class="workflow_validate workflow">{{trans('global.start_workflow_validation')}}</a>
                            </li>
                        @endcan
                    </ul>
                </div>

                {{--<iframe src="{{$file->getUrl()}}"></iframe>--}}

            </div>
        </div>
    </div>

@stop
