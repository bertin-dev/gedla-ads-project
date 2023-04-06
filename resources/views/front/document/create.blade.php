@extends('layouts.front')

@section('content')
<style>
    /*body {
        max-width: 1200px;
        padding: 0 20px;
        margin: 20px auto;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        overflow-y: scroll;
    }*/
    /* Set the minimum height of Classic editor */
    .ck.ck-editor__editable_inline:not(.ck-editor__nested-editable) {
        min-height: 400px;
    }
    /* Styles to render an editor with a sidebar for comments & suggestions */
    .container {
        display: flex;
        flex-direction: row;
    }
    .sidebar {
        width: 320px;
    }
    #editor-container .ck.ck-editor {
        width: auto;
    }
    #editor-container .sidebar {
        margin-left: 20px;
    }
    #editor-container .sidebar.narrow {
        width: 30px;
    }
    /* Keep the automatic height of the editor for adding comments */
    .ck-annotation-wrapper .ck.ck-editor__editable_inline {
        min-height: auto;
    }
    /* Styles for viewing revision history */
    #revision-viewer-container {
        display: none;
    }
    #revision-viewer-container .ck.ck-editor {
        width: 860px;
    }
    #revision-viewer-container .ck.ck-content {
        min-height: 400px;
    }
    #revision-viewer-container .sidebar {
        border: 1px #c4c4c4 solid;
        margin-left: -1px;
        width: 320px;
    }
    #revision-viewer-container .ck.ck-revision-history-sidebar__header {
        height: 39px;
        background: #FAFAFA;
    }
    .hidden {
        display: none!important;
    }
</style>

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
                        <input id="fileName" type="text" class="form-control" required name="fileName" maxlength="50" value="{{$fileName ?? ''}}" placeholder="Example: document">
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
                    <textarea id="description1" class="form-control" required name="description1">{!!  $fileRead ?? '' !!}</textarea>
                </div>

                <div class="form-group text-right">
                    <button id="submit" type="submit" role="button" class="btn btn-success">{{trans('global.save')}}</button>
                </div>

            </form>


        </div>
    </div>
</div>



<!-- Use simpler CSS and HTML structure if you do not want to integrate i.e. the Revision history feature. !-->
<div id="presence-list-container"></div>

<div id="editor-container">
    <div class="container">
        <div id="editor"></div>
        <div class="sidebar" id="sidebar"></div>
    </div>
</div>

<div id="revision-viewer-container">
    <div class="container">
        <div id="revision-viewer-editor"></div>
        <div class="sidebar" id="revision-viewer-sidebar"></div>
    </div>
</div>

@stop

@section('scripts')
