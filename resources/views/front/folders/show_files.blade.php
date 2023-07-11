@extends('layouts.front')

@section('content')

    @if(!$folder->multiUsers->isEmpty() && !$folder->multiUsers->where('id', auth()->id())->isEmpty())

        <!--**********************************
        Content body start
    ***********************************-->
        <div class="content-body">
            <!-- row -->
            <div class="container-fluid">

                <div class="row">
                    @include('partials.filter_search', ['parapheurWithMedia' => null ,'folder' => $folder])


                    <div id="folder_media_bloc" class="col-lg-12 form-row">
                        {{--Documents--}}
                        @php
                            $getMedias = $folder->files
                            ->where('archived', 0)
                            ->where('saved', 0)
                            ->where('state', 'unlocked')
                            ->where('visibility', 'public')
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
                                            <img class="img-thumbnail"
                                                 src="{{ $result ?? url('images/file-thumbnail.png') }}"
                                                 alt="{{ $file->name }}" title="{{ $file->name }}">
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="card-body" style="padding: 5px 5px 0;">
                                                <h5 class="card-title">{{ strtolower(Str::substr($file->name, 14, 22)) }}</h5>
                                                <div style="margin-top: 13px">
                                                        <span><small style="margin-right: 70px">
                                                                {{($file->created_at==$file->updated_at) ? trans('global.create') ." " . Carbon\Carbon::parse($file->created_at)->diffForHumans() . " " . ($file->createdBy != null ? trans('global.by')." ".ucfirst($file->createdBy->name) : "") : trans('global.edit')." " . Carbon\Carbon::parse($file->updated_at)->diffForHumans() . " " . ($file->updatedBy != null ? trans('global.edit')." ".ucfirst($file->updatedBy->name) : "") }}
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
                                                                <small class="alert-success">{{ trans('global.approved') }}</small>
                                                            @elseif($data->statut == 0)
                                                                <small class="alert-info">{{ trans('global.waiting') }}</small>
                                                            @else
                                                                <small class="alert-danger">{{ trans('global.rejected') }}</small>
                                                            @endif
                                                        @endforeach
                                                        <small class="alert-link" style="font-size: 9px;">{{trans('global.expiry')}}: {{now()->diffForHumans($file->globa_deadline)}}</small>
                                                    @else
                                                        <small class="alert-info">{{trans('global.import')}}</small>
                                                    @endif

                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{--Folders--}}
                        @foreach ($folder->children as $folder)
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="row no-gutters">
                                        <div class="col-sm-4">
                                            <a href="{{ route('folders.show', [$folder]) }}">
                                                <img class="img-thumbnail"
                                                     src="{{ $folder->thumbnail ? $folder->thumbnail->thumbnail : url('images/empty-folder.png') }}"
                                                     alt="{{ $folder->name }}" title="{{ $folder->name }}">
                                            </a>
                                        </div>
                                        <div class="col-sm-8">
                                            <a href="{{ route('folders.show', [$folder]) }}" style="color: initial">
                                                <div class="card-body" style="padding: 5px 5px 0;">
                                                    <h5 class="card-title"> {{ strtoupper($folder->name) }} </h5>
                                                    <div style="margin-top: 10px">
                                                        <span><small> {{($folder->created_at==$folder->updated_at) ? ' '.trans('global.create') .' '. Carbon\Carbon::parse($folder->created_at)->diffForHumans() . ' '.trans('global.by') .' '. ($folder->userCreatedFolderBy != null ? ucfirst($folder->userCreatedFolderBy->name) : "") : ' '.trans('global.edit') . Carbon\Carbon::parse($folder->updated_at)->diffForHumans() . ' '.trans('global.by') . ($folder->userUpdatedFolderBy != null ? ucfirst($folder->userUpdatedFolderBy->name) : "") }}</small></span><br>
                                                        <small class="text-muted">{!! $folder->description ?? trans('global.no_description') !!}</small><br>
                                                        <small>{{ trans('global.no_tag') }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->

    @else
        @include('partials.not_found')
    @endif

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
                                {{--<img class="img-thumbnail img_detail">--}}
                                <div class="iframeFile"></div>
                                <div class="text-left">
                                    @can('open_file_access')
                                        <a class="myUrl col-lg-4 open-document"
                                           target="_blank">{{trans('global.open')}} </a>
                                    @endcan
                                    @can('edit_file_access')
                                        <a class="col-lg-4 mediaId" target="_blank"
                                           href="{{ route('edit-document') }}?folder_id={{ $folder->id ?? '' }}">{{trans('global.edit_document')}}</a>
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

                            <div class="col-md-6 ml-auto">
                                <span class="folder_id col-lg-4"></span>
                                <span class="folder_size col-lg-4"></span>
                                <span class="version col-lg-4"></span>
                                <hr>

                                <div class="">
                                    <!---------------------------->
                                    <section id="schedule" class="section-with-bg">
                                        <div class="section-header">
                                            <h4>{{trans('global.my_recent_activity')}}</h4>
                                            <p class="initMyActivity">{{trans('global.document_history')}}</p>
                                        </div>
                                        <div class="loading">
                                            <center><img src="{{asset('images/loading.gif')}}" alt=""></center>
                                        </div>

                                        <div class="tab-content row justify-content-center aos-init aos-animate"
                                             data-aos="fade-up" data-aos-delay="200">

                                            <!-- Schdule Day 1 -->
                                            <div role="tabpanel" class="col-lg-9 tab-pane fade show active myActivity"
                                                 id="day-1">
                                                <div class="row schedule-item"></div>
                                            </div>
                                            <!-- End Schdule Day 1 -->

                                        </div>

                                    </section>

                                </div>

                                <div class="card workflow_form" style="display: none">
                                    <div class="card-header">{{trans('global.shared_document')}}</div>

                                    <div class="card-body">
                                        @if (session('status'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session('status') }}
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('admin.workflow-shareDocument') }}">
                                            @csrf
                                            <input type="hidden" name="send_validation_workflow"
                                                   value="send_validation_workflow">
                                            <input id="media_id" type="hidden" name="media_id"/>

                                            <div class="form-group">
                                                <label for="user_assign">{{trans('global.assigned_to')}}</label>
                                                <select name="user_assign" id="user_assign" class="form-control">
                                                    @foreach($users as $id => $user)
                                                        {{--@foreach($user->roles as $role)
                                                            @if(strtolower($role->title)=="user")
                                                                <option
                                                                    value="{{ $id }}" {{ in_array($id, old('user_assign', [])) ? 'selected' : '' }}>{{ $user }}</option>
                                                            @endif
                                                        @endforeach--}}
                                                        <option
                                                            value="{{ $id }}" {{ in_array($id, old('user_assign', [])) ? 'selected' : '' }}>{{ $user }}</option>

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
                                   title="validation_signature">{{ trans('global.validation_signature') }}</a></li>
                            <li><a href="#" class="validation_paraphe validate_file_access"
                                   title="validation_paraphe">{{ trans('global.validation_initialed') }}</a></li>
                            <li><a href="#" class="document_id rejected validate_file_access"
                                   title="rejected">{{ trans('global.Rejected_Refused') }}</a>
                            </li>
                        @endcan
                        @can('send_document')
                            <li><a href="#"
                                   class="workflow_validate workflow">{{trans('global.send_document_to')}}</a>
                            </li>
                        @endcan
                    </ul>
                </div>

            </div>
        </div>
    </div>

@stop
