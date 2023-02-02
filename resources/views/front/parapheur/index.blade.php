@extends('layouts.front')

@section('content')


    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-12">
                    <a class="btn btn-success" href="{{ route('parapheur.upload') }}?parapheur_id={{$parapheur->id}}">
                        {{ trans('global.add') }} {{ trans('global.upload_file') }}
                    </a>
                </div>

                <div class="col-lg-12" style="margin-top: 20px">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>

                @foreach($parapheur->medias as $file)

                    @php
                        $result=match($file->mime_type){"application/pdf"=>url('images/pdf.png'),"text/plain"=>url('images/txt.png'),"application/vnd.openxmlformats-officedocument.wordprocessingml.document"=>url('images/docx.png'),"application/x-msaccess"=>url('images/access.png'),"application/vnd.ms-visio.drawing.main+xml"=>url('images/visio.png'),"application/vnd.openxmlformats-officedocument.presentationml.presentation"=>url('images/power_point.png'),"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"=>url('images/xlsx.png'),"image/jpeg"=>url('images/file-thumbnail.png'),default => '',};
                        $realSize = number_format($file->size/1024, 1, '.', '');
                    @endphp

                    <div class="col-lg-4">
                        <div class="card" data-toggle="modal" data-target=".bd-example-modal-lg"
                             data-id="{{$file->id}}" data-name="{{ucfirst(strtolower(Str::substr($file->file_name, 14)))}}" data-size="{{$realSize}}"
                             data-item_type="{{$result}}" data-url="{{$file->getUrl()}}" data-version="{{$file->version}}">
                            <div class="row no-gutters">
                                <div class="col-sm-4">
                                    <img class="img-thumbnail" src="{{ $result ?? url('images/file-thumbnail.png') }}"
                                         alt="{{ $file->name }}" title="{{ $file->name }}">
                                </div>
                                <div class="col-sm-8">
                                    <div class="card-body" style="padding: 5px 5px 0;">
                                        <h5 class="card-title">{{ strtolower(Str::substr($file->file_name, 14, 42)) }}</h5>
                                        <div style="margin-top: 23px">
                                            <span>{{ date('d/m/Y' , strtotime($file->created_at)) }}</span>
                                            <div class="text-right"> {{$realSize}} KO</div>
                                            {{--<a href="{{ $file->getUrl() }}" target="_blank" class="btn-link">Acquisition</a>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

        </div>

    </div>


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
                            <div class="col-md-6">
                                <div style="display: none" class="alert alert-success success_download" role="alert"></div>
                                <img class="img-thumbnail img_detail" alt="" src="">
                                <div class="text-left">
                                    @can('open_file_access')
                                    <a class="myUrl col-lg-4" target="_blank">{{trans('global.open')}} </a>
                                    @endcan
                                    @can('download_access')
                                    <a href="#" class="mediaDownload col-lg-4"> {{trans('global.download')}}</a>
                                    @endcan
                                    @can('archive_file_access')
                                        <a href="#" class="col-lg-4" data-toggle="modal" data-target=".open-file">{{trans('global.archive_document')}} </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="col-md-6 ml-auto">
                                <span class="folder_id col-lg-4"></span>
                                <span class="folder_size col-lg-4"></span>
                                <span class="version col-lg-4"></span>
                                <hr>

                                <div class="myActivity">
                                    <h5>{{trans('global.my_recent_activity')}}</h5>
                                        <small><strong>Bertin</strong> à envoyer un document en attente de validation à <strong>cyrille</strong></small><br>
                                        <small>il y a 10h</small>
                                </div>

                                <div class="card workflow_form" style="display: none">
                                    <div class="card-header">{{trans('global.workflow_validation')}}</div>

                                    <div class="card-body">
                                        @if (session('status'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session('status') }}
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('operation.store') }}">
                                            @csrf
                                            <input type="hidden" name="send_validation_workflow" value="send_validation_workflow">
                                            <input id="media_id" type="hidden" name="media_id" />
                                            <input type="hidden" name="parapheur_id" value="{{$parapheurWithMedia->id}}" />
                                            
                                            <div class="form-group">
                                                <label for="deadline">{{trans('global.term')}}</label>
                                                <input type="date" id="deadline" name="deadline" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label for="priority">{{trans('global.priority')}}</label>
                                                <select name="priority" id="priority" class="form-control">
                                                    <option value="low">{{trans('global.message')}}</option>
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
                                                    <input class="form-check-input" type="checkbox" name="flexCheckChecked" id="flexCheckChecked" style="vertical-align: middle;">
                                                    <label class="form-check-label" for="flexCheckChecked" style="vertical-align: middle;">
                                                        {{trans('global.send_notification_by_email')}}
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <button type="submit" class="btn bg-card-violet text-white">{{trans('global.send')}}</button>
                                                <button type="reset" class="btn bg-card-green text-white">{{trans('global.cancel')}}</button>
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
                        <li><a href="#">{{trans('global.validate')}}</a></li>
                        @endcan
                        @can('operation_access')
                            <li><a href="#" class="workflow_validate">{{trans('global.start_workflow_validation')}}</a></li>
                        @endcan
                    </ul>
                </div>

                {{--<iframe src="{{$file->getUrl()}}"></iframe>--}}

            </div>
        </div>
    </div>

    <div class="modal fade open-file" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
                            {{--<iframe src="{{$file->getUrl()}}"></iframe>--}}

                            <form method="post" action="" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>{{trans('global.name')}}</label>
                                    <input type="text" name="name" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label><strong>{{trans('global.description')}} :</strong></label>
                                    <textarea class="ckeditor form-control" name="description"></textarea>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-success btn-sm">{{trans('global.save')}}</button>
                                </div>
                            </form>


                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <h5 class="modal-title">{{ trans('global.file_action') }}</h5><br>
                    <ul>
                        <li><a href="#">{{trans('global.edit_document')}}</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>




@stop
