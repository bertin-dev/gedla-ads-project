@extends('layouts.admin')
@section('content')
    <style>
        .container .table-wrap {
            overflow-x: auto;
        }

        .container .table-wrap::-webkit-scrollbar {
            height: 5px;
        }

        .container .table-wrap::-webkit-scrollbar-thumb {
            border-radius: 5px;
            background-image: linear-gradient(to right, #5D7ECD, #0C91E6);
        }


        .table>:not(caption)>*>* {
            padding: 2rem 0.5rem;
        }

        .table tbody td input[type="checkbox"] {
            appearance: none;
            width: 20px;
            height: 20px;
            background-color: #eee;
            position: relative;
            border-radius: 3px;
            cursor: pointer;
        }

        .table tbody td input[type="checkbox"]:after {
            position: absolute;
            width: 100%;
            height: 100%;
            font-family: "Font Awesome 5 Free", serif;
            font-weight: 600;
            content: "\f00c";
            color: #fff;
            font-size: 15px;
            display: none;
        }

        .table tbody td input[type="checkbox"]:checked,
        .table tbody td input[type="checkbox"]:checked:hover {
            background-color: #40bfc1;
        }

        .table tbody td input[type="checkbox"]:checked::after {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table tbody td input[type="checkbox"]:hover {
            background-color: #ddd;
        }

        .table tbody td .img-container {
            width: 50px;
            height: 50px;
        }

        .table tbody td .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .table tbody,
        .table thead {
            background-color: #fff;
        }

        .table tbody tr td:nth-of-type(1) {
            text-align: center;
            min-width: 70px;
            max-width: 70px;
        }

        .table tbody tr td:nth-of-type(2) {
            min-width: 300px;
            max-width: 300px;
        }


        .table tbody tr td:nth-of-type(3) {
            min-width: 150px;
            max-width: 150px;
        }

        .table tbody tr td:nth-of-type(4) {
            min-width: 300px;
            max-width: 300px;
        }

        .table tbody tr td:nth-of-type(5) {
            min-width: 50px;
            max-width: 50px;
        }

        .table tbody tr {
            box-shadow: 0px 2px 3px #1f1f1f1a;
        }

        .table thead tr {
            border-bottom: 4px solid #E1F5FE;
        }

        .table tbody td .active,
        .table tbody td .waiting {
            background-color: #B9F6CA;
            color: #388E3C;
            font-weight: 600;
            padding: 1px 10px;
            border-radius: 15px;
            font-size: 0.9rem;
        }

        .table tbody td .waiting {
            background-color: #FFECB3;
            color: #FFA000;
        }

        .table tbody td .active .circle,
        .table tbody td .waiting .circle {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #388E3C;
        }

        .table tbody td .waiting .circle {
            background-color: #FFA000;
        }

        .table tbody td .fa-times {
            color: #D32F2F;
            font-size: 0.9rem;
        }

        .fw-600 {
            font-weight: 600 !important;
        }

        .fs-09 {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .text-grey {
            color: #a8a8a8 !important;
        }


        @media(min-width: 992px) {
            .container .table-wrap {
                overflow: hidden;
            }
        }
    </style>

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.workflow_management.new_workflow') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.workflow-management.store") }}" enctype="multipart/form-data">
            @csrf

            {{--<div class="form-group">
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
                <select class="form-control select2 {{ $errors->has('folder_access') ? 'is-invalid' : '' }}" name="folder_access" id="folder_access" required>
                    @foreach($folders as $id => $folder)
                        <option value="{{ $id }}" {{ old('folder_access') == $id ? 'selected' : '' }}>{{ $folder }}</option>
                    @endforeach
                </select>
                @if($errors->has('folder_access'))
                    <div class="invalid-feedback">
                        {{ $errors->first('folder_access') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.folder_access.fields.folder_access_helper') }}</span>
            </div>--}}

            <div class="form-group">
                <label for="deadline">Echéance</label>
                <input type="date" id="deadline" name="deadline" class="form-control">
            </div>

            <div class="form-group">
                <label for="priority">Priorité</label>
                <select name="priority" id="priority" class="form-control">
                    <option value="low">Basse</option>
                    <option value="medium">Moyenne</option>
                    <option value="high">Important</option>
                </select>
            </div>

            <div class="form-group">
                <label for="visibility">Visibilité</label>
                <select name="visibility" id="visibility" class="form-control">
                    <option value="public">Public</option>
                    <option value="private">Privé</option>
                </select>
            </div>

            <div class="form-group">
                <label for="files">Files</label>
                <div class="needsclick dropzone {{ $errors->has('files') ? 'is-invalid' : '' }}" id="files-dropzone">
                </div>
                @if($errors->has('files'))
                    <div class="invalid-feedback">
                        {{ $errors->first('files') }}
                    </div>
                @endif
            </div>


            <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" id="message" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <div class="form-check checkbox">
                    <input class="form-check-input" type="checkbox" name="flexCheckChecked" id="flexCheckChecked" style="vertical-align: middle;">
                    <label class="form-check-label" for="flexCheckChecked" style="vertical-align: middle;">
                        Envoyer des notifications par Email
                    </label>
                </div>
            </div>


            <div class="form-group">
                <label class="required" for="user_access">{{ trans('cruds.folder_access.fields.user_access') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('user_access') ? 'is-invalid' : '' }}" name="workflow_user[]" id="workflow_user" multiple required>
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

            <div class="list-classified-user">
                <div class="table-wrap">
                    <table class="table table-borderless table-responsive">
                        <thead>
                        <tr>
                            <th class="text-muted fw-600">Order</th>
                            <th class="text-muted fw-600">Email</th>
                            <th class="text-muted fw-600">Username</th>
                            <th class="text-muted fw-600">Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="align-middle alert" role="alert">
                            <td>
                                {{--<input type="checkbox" id="check">--}}
                                <span class="badge badge-primary badge-pill">1</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="img-thumbnail">
                                        <img src="https://images.pexels.com/photos/2379005/pexels-photo-2379005.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940"
                                             alt="" width="50" height="50">
                                    </div>
                                    <div class="ps-3">
                                        <div class="fw-600 pb-1">mark@gmail.com</div>
                                        <p class="m-0 text-grey fs-09">Added: 03/02/2012</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-600">Markov98</div>
                            </td>
                            <td>
                                <div class="d-inline-flex align-items-center active">
                                    <div class="circle"></div>
                                    <div class="ps-2">Active</div>
                                </div>
                            </td>
                            <td>
                                <div class="btn p-0" data-bs-dismiss="alert">
                                    <span class="fas fa-times"></span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.validate') }}
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
            url: '{{ route('admin.workflow-management.storeMedia') }}',
            maxFilesize: 2, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.pdf,.doc,.docx',
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




        /*$(function () {
            var x = document.getElementById("workflow_user");

            $('#workflow_user').on('change', function () {

                for (var i = 0; i < x.options.length; i++) {
                    if(x.options[i].selected ==true){
                        alert("dffsdf " + x.options[i].value);
                    }
                }

            });
        });*/

    </script>
    {{--<script>
    $(function () {

        $('#project_id').on('change', function() {
            alert( this.value );
            load_folder(this.value);
        });


        function load_folder(id = '2') {
            $.ajax({
                headers: {'x-csrf-token': _token},
                url: '{{ route('admin.folders_access.show', 1) }}',
                method: 'GET',
                //data: {view: id},
                dataType: 'json',
                success: function (data) {
                    alert(data.id);
                    /*$('.menu').html(data.notification);

                    if (data.unseen_notification > 0) {
                        $('.count').html(data.unseen_notification);
                    }*/

                },
                error: function(data){
                    console.log('Erreur de chargement des Notifications');
                }
            });
        }

    });
</script>--}}
@endsection
