@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.folder_access.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.folders_access.update", [$folder->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="form-group">
           <label class="required" for="project_id">{{ trans('cruds.folder.fields.project') }}</label>
           <select class="form-control select2 {{ $errors->has('project') ? 'is-invalid' : '' }}" name="project_id" id="project_id" required>
               @foreach($folder->project->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '') as $id => $project)
                   <option value="{{ $id }}" {{ (old('project_id') ? old('project_id') : $project->id ?? '') == $id ? 'selected' : '' }}>{{ $project }}</option>
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
                <select class="form-control select2 {{ $errors->has('folder_access') ? 'is-invalid' : '' }}" name="folder_access" id="folder_access" required>
                    @foreach($folders as $id => $folder1)
                        <option value="{{ $id }}" {{ (old('folder_access') ? old('folder_access') : $folder1->id ?? '') == $id ? 'selected' : '' }}>{{ $folder1 }}</option>
                    @endforeach
                </select>
                @if($errors->has('folder_access'))
                    <div class="invalid-feedback">
                        {{ $errors->first('folder_access') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.folder_access.fields.folder_access_helper') }}</span>
            </div>

            <div class="form-group">
                <label for="user_access">{{ trans('cruds.folder_access.fields.user_access') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('user_access') ? 'is-invalid' : '' }}" name="user_access[]" id="user_access" multiple>
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
    var uploadedFilesMap = {}
Dropzone.options.filesDropzone = {
    url: '{{ route('admin.folders.storeMedia') }}',
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="files[]" value="' + response.name + '">')
      uploadedFilesMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedFilesMap[file.name]
      }
      $('form').find('input[name="files[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($folder) && $folder->files)
          var files =
            {!! json_encode($folder->files) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="files[]" value="' + file.file_name + '">')
            }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
@endsection
