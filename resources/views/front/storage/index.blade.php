@extends('layouts.front')

@section('content')

    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">

            <div class="row">

                <div id="folder_media_bloc" class="col-lg-12 form-row">

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
                                                            <small
                                                                class="alert-success">{{ trans('global.approved') }}</small>
                                                        @elseif($data->statut == 0)
                                                            <small
                                                                class="alert-info">{{ trans('global.waiting') }}</small>
                                                        @else
                                                            <small
                                                                class="alert-danger">{{ trans('global.rejected') }}</small>
                                                        @endif
                                                    @endforeach
                                                    <small class="alert-link" style="font-size: 9px;">{{trans('global.expiry')}}: {{now()->diffForHumans($file->globa_deadline)}}</small>
                                                @else
                                                    <small class="alert-info">{{trans('global.import')}}</small>
                                                @endif
                                                {{--@can('restore_document')--}}
                                                    <small class="alert-link"> <a
                                                            href="{{ route('restore-document', $file->id) }}">{{ trans('global.Restore_document') }}</a></small>
                                                {{--@endcan--}}
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
    </div>

@stop
