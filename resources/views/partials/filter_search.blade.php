<div class="col-lg-12 form-row">
    <div class="col-md-7 mb-3">
        @can('document_upload')
            @if($folder != null)
                <a class="btn btn-outline-success"
                   href="{{ route('folders.upload') }}?folder_id={{ $folder->id }}">
                    {{trans('global.upload_file')}}
                </a>
            @elseif($parapheurWithMedia != null)
                <a class="btn btn-outline-success"
                   href="{{ route('parapheur.upload') }}?parapheur_id={{ $parapheurWithMedia->id }}">
                    {{trans('global.upload_file')}}
                </a>
            @endif
        @endcan
        @can('document_create')
            @if($folder != null)
                <a class="btn btn-outline-primary"
                   href="{{ route('create-document') }}?folder_id={{ $folder->id }}">
                    {{ trans('global.create') }} {{trans('global.document')}}
                </a>
            @elseif($parapheurWithMedia != null)
                <a class="btn btn-outline-primary"
                   href="{{ route('create-document') }}?parapheur_id={{ $parapheurWithMedia->id }}">
                    {{ trans('global.create') }} {{trans('global.document')}}
                </a>
            @endif
        @endcan
        @can('folder_create')
            @if($folder != null)
                <a class="btn btn-outline-danger"
                   href="{{ route('folders.create') }}?parent_id={{ $folder->id }}&project_id={{$folder->project_id}}">
                    {{ trans('global.create') }} {{trans('global.folder')}}
                </a>
            @endif
        @endcan
    </div>
    <div class="col-md-5 mb-3">
        <form action="{{ ($folder != null) ? route('search-document', $folder) : "" }}" method="GET"
              class="navbar-search"
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
    @if (session('status') ?? session('message'))
        <div class="alert alert-success" role="alert">
            {{ session('status')  ?? session('message') }}
        </div>
    @endif
</div>
