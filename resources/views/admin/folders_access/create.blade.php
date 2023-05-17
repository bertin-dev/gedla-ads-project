@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.folder_access.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.folders_access.store") }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="required" for="project_id">{{ trans('cruds.folder.fields.project') }}</label>
                <select class="form-control select2 {{ $errors->has('project') ? 'is-invalid' : '' }}" name="project_id" id="project_id" required>
                    @foreach($projects as $id => $project)
                        <option value="{{ $id }}" {{ old('project_id') == $id ? 'selected' : '' }}>{{ $project }}</option>
                    @endforeach
                </select>
                @if($errors->has('project'))
                    <div class="invalid-feedback">
                        {{ $errors->first('project') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.folder.fields.project_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="folder_access">{{ trans('cruds.folder_access.fields.folder_access') }}</label>
                <select class="form-control select2 {{ $errors->has('folder_access') ? 'is-invalid' : '' }}" name="folder_access" id="folder_access" required></select>
                @if($errors->has('folder_access'))
                    <div class="invalid-feedback">
                        {{ $errors->first('folder_access') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.folder_access.fields.folder_access_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="user_access">{{ trans('cruds.folder_access.fields.user_access') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('user_access') ? 'is-invalid' : '' }}" name="user_access[]" id="user_access" multiple required>
                    @foreach($users as $id => $user)
                        <option value="{{ $id }}" {{ in_array($id, old('user_access', [])) ? 'selected' : '' }}>{{ $user }}</option>
                    @endforeach
                </select>
                @if($errors->has('user_access'))
                    <div class="invalid-feedback">
                        {{ $errors->first('user_access') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.folder_access.fields.user_access_helper') }}</span>
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    $(function () {

        $('#project_id').on('change', function() {
            //alert( this.value );
            load_folder(this.value);
        });


        function load_folder(id = '') {
            let folders = $("#folder_access");
            folders.empty();
            $.ajax({
                headers: {'x-csrf-token': _token},
                url:"{{ route('admin.load-folders', '') }}"+"/"+id,
                method: 'GET',
                //data: {view: id},
                dataType: 'json',
                success: function (data) {
                    $.each(data.data, function(index, item){
                        folders.append('<option value="'+index+'" "">'+item+'</option>');
                    });

                },
                error: function(data){
                    console.log('Erreur de chargement des folders');
                }
            });
        }

    });
</script>

@endsection
