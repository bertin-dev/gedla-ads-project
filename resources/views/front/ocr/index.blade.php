@extends('layouts.front')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Upload Image</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('postUploadOCR') }}">
                            @csrf
                            <input id="folder_id" type="hidden" name="folder_id" value="" />
                            {{--<div class="form-group">
                                <label for="parent_id">{{ trans('cruds.folder.fields.parent') }}</label>
                                <select class="form-control select2 {{ $errors->has('parent') ? 'is-invalid' : '' }}" name="parent_id" id="parent_id" required>
                                    @foreach($parents as $id => $parent)
                                        <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>{{ strtolower($parent) }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('parent'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('parent') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.folder.fields.parent_helper') }}</span>
                            </div>--}}

                            <div class="form-group">
                                <label for="files">Image File</label>
                                <div class="needsclick dropzone {{ $errors->has('files') ? 'is-invalid' : '' }}" id="files-dropzone">
                                </div>
                                @if($errors->has('files'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('files') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Upload Image</button>
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
            url: '{{ route('storeImgOCR') }}',
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
                let documentSelected = $('#parent_id');
                documentSelected.on('change', function () {
                    $('#folder_id').attr('value', documentSelected.val());
                    alert(documentSelected.val());
                });
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

    {{--<script src="//cdn.ckeditor.com/4.20.1/standard/ckeditor5.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.ckeditor').ckeditor();
    });

    CKEDITOR.replace('description', {
        filebrowserUploadUrl: "--}}{{--{{route('ckeditor.image-upload', ['_token' => csrf_token() ])}}--}}{{--",
        filebrowserUploadMethod: 'form'
    });

</script>--}}
@endsection
