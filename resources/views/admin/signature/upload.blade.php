@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{trans('global.import')}} {{ trans('cruds.signature.title') }} {{ trans('global.or') }} {{ trans('cruds.parapheur.title') }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.signature.postUpload') }}">
                            @csrf

                            <div class="form-group">
                                <label class="required" for="user_signature">{{ trans('cruds.signature.fields.owner') }}</label>
                                <select class="form-control select2 {{ $errors->has('user_signature') ? 'is-invalid' : '' }}" name="user_signature" id="user_signature" required>
                                    @foreach($users as $id => $user)
                                        <option value="{{ $id }}" {{ in_array($id, old('user_signature', [])) ? 'selected' : '' }}>{{ $user }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('user_signature'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('user_signature') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.signature.fields.signature_text_helper') }}</span>
                            </div>

                            <div class="form-group">
                                <label class="required" for="initial">marque</label>
                                <select class="form-control select2 {{ $errors->has('initial') ? 'is-invalid' : '' }}" name="initial" id="initial" required>
                                        <option value="signature">{{ trans('cruds.signature.title') }}</option>
                                        <option value="paraphe">{{ trans('cruds.parapheur.title') }}</option>
                                </select>
                                @if($errors->has('initial'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('initial') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.folder.fields.project_helper') }}</span>
                            </div>

                            <div class="form-group">
                                <label for="files">Image</label>
                                <div class="needsclick dropzone {{ $errors->has('files') ? 'is-invalid' : '' }}" id="files-dropzone" style="display: block;min-height: 250px;border:2px dashed rgba(0,0,0,0.3);border-radius: 20px;background:white;padding:20px 20px;background: url(/images/upload-bg.png) no-repeat center;">
                                </div>
                                @if($errors->has('files'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('files') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{trans('global.import')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var uploadedFilesMap = {}
        Dropzone.options.filesDropzone = {
            url: '{{ route('admin.signature.store-media') }}',
            maxFilesize: 2, // MB
            acceptedFiles: '.jpeg,.jpg,.png',
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
                    if (file.thumbnail) {
                        this.options.thumbnail.call(this, file, file.thumbnail)
                    }
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
