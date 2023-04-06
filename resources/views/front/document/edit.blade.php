@extends('layouts.front')

@section('content')

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">File Editor</div>

            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="post" action="{{route('post-upload-file')}}" enctype="multipart/form-data" onsubmit="return false;">
                    @csrf
                    <div class="row">
                        <div class="form-group col">
                            <label for="fileName" class="required"><strong>{{trans('global.file_name')}}</strong></label>
                            <input id="fileName" type="text" class="form-control" required name="fileName" maxlength="50" value="{{substr($getMedia->file_name, 14) ?? ''}}" placeholder="Example: document">
                        </div>
                        <div class="form-group col">
                            <label for="document_format" class="required"><strong>Format du document</strong></label>
                            <select id="document_format" class="form-control">
                                <option value="pdf" selected>Format PDF</option>
                                <option value="word">Format Word</option>
                                <option value="odt">Format ODText</option>
                                <option value="rtf">Format RTF</option>
                                <option value="html">Format HTML</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="hidden" name="folder_id" value="{{$folderId}}">
                        <input type="hidden" name="parapheur_id" value="{{$parapheurId}}">
                        <textarea id="description1" class="form-control" required name="description1">{!! $text ?? '' !!}</textarea>
                    </div>

                    <div class="form-group text-right">
                        <button id="submit" type="submit" role="button" class="btn btn-success">{{trans('global.save')}}</button>
                    </div>

                </form>


            </div>
        </div>
    </div>

@stop

@section('scripts')
