@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.workflow_management.new_workflow') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.workflow-management.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="container">
                <div class="row">

                    <div class="form-group col">
                        <label class="required" for="project_list">{{ trans('global.add') }} {{ trans('cruds.project.title') }}</label>
                        <select class="form-control {{ $errors->has('project') ? 'is-invalid' : '' }}" name="project" id="project" required>
                            @foreach($projects as $id => $project)
                                <option value="{{ $id }}" {{ in_array($id, old('project', [])) ? 'selected' : '' }}>{{ $project }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('project'))
                            <div class="invalid-feedback">
                                {{ $errors->first('project') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.project.fields.name_helper') }}</span>
                    </div>

                    <div class="form-group col">
                        <label class="required" for="global_deadline">{{trans('global.term')}}</label>
                        <input type="datetime-local" id="global_deadline" value="{{ old('global_deadline') }}" name="global_deadline" class="form-control" required>
                    </div>

                    <div class="form-group col">
                        <label for="priority" class="required">{{trans('global.priority')}}</label>
                        <select name="priority" id="priority" class="form-control" required>
                            <option value="low">{{trans('global.normal')}}</option>
                            <option value="medium">{{trans('global.urgent')}}</option>
                            <option value="high">{{trans('global.very_urgent')}}</option>
                        </select>
                    </div>

                    <div class="form-group col">
                        <label for="visibility">{{trans('global.visibility')}}</label>
                        <select name="visibility" id="visibility" class="form-control">
                            <option value="public">{{trans('global.public')}}</option>
                            <option value="private">{{trans('global.private')}}</option>
                        </select>
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-6">
                        <label for="files">{{ trans('global.add') }} {{ trans('global.file') }}</label>
                        <div class="needsclick dropzone {{ $errors->has('files') ? 'is-invalid' : '' }}" id="files-dropzone" style="display: block;border:2px dashed rgba(0,0,0,0.3);background: url(/images/upload-bg.png) no-repeat center;">
                        </div>
                        @if($errors->has('files'))
                            <div class="invalid-feedback">
                                {{ $errors->first('files') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group col-6">
                        <label for="message">{{ trans('global.message') }}</label>
                        <textarea name="message" id="message" class="form-control">{{old('message')}}</textarea>
                    </div>

                    <div class="form-group col-lg-12">
                        <div class="form-check checkbox">
                            <input class="form-check-input" type="checkbox" name="flexCheckChecked" id="flexCheckChecked" style="vertical-align: middle;">
                            <label class="form-check-label" for="flexCheckChecked" style="vertical-align: middle;">
                                {{trans('global.send_notification_by_email')}}
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="required" for="user_list">{{ trans('global.add') }} {{ trans('cruds.workflow_management.fields.users') }}</label>
                        <div style="padding-bottom: 4px">
                            <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                            <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                        </div>
                        <select class="form-control select2 {{ $errors->has('user_list') ? 'is-invalid' : '' }}" name="user_list[]" id="user_list" multiple required></select>
                        @if($errors->has('user_list'))
                            <div class="invalid-feedback">
                                {{ $errors->first('user_list') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.workflow_management.fields.users_helper') }}</span>
                    </div>
                </div>

                <div id="section2"><!-- for dynamic controls--></div>

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.validate') }}
                    </button>
                </div>
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

    <script>

        $(function () {

            let getUserSelect = $('#user_list');
            getUserSelect.on('change', function () {

                    let values = [];
                    let username = [];
                    let input = [];
                    let $selectedOptions = $(this).find('option:selected');
                    $selectedOptions.each(function(){
                        values.push($(this).val());
                        username.push($(this).text());
                    });

                    const userId = values.toString().split(",");
                    const name = username.toString().split(",");
                    for(let i=0; i<userId.length; i++){
                         input[i] = $("<div class='card'><div class='row'>" +

                             "<div class='col text-center'>"+
                             "<img id='profil' src='{{asset('images/profil.png')}}' alt='' width='50'><br>"+
                             "<label for='profil'>"+name[i]+"</label>"+
                             "</div>" +

                            "<div class='form-group col'>" +
                            "<label for='deadline"+userId[i]+"'>{{ trans('global.term') }} {{ trans('global.for') }} " +name[i]+ "</label>" +
                            "<input id='deadline"+userId[i]+"' name='deadline"+userId[i]+"' type='datetime-local' class='form-control' required>" +
                            "</div> " +

                            "<div class='col'> " +
                            "<label>{{ trans('global.enable_delegation') }}</label><br>"+
                            "<label class='switch'>"+
                             "<input class='switch"+userId[i]+"' type='checkbox' name='switch"+userId[i]+"' value='"+userId[i]+"'>"+
                             "<span class='slider round'></span>"+
                             "</label>" +
                             "</div> " +


                            "<div class='form-group col switch"+userId[i]+"' style='display:none'>" +
                            "<label for='user_list"+userId[i]+"'>{{trans('global.delegated_user')}}</label>" +
                            "<select id='user_list"+userId[i]+"' name='user_list"+userId[i]+"' class='form-control'>" +
                            @foreach($users as $id => $user)
                                "<option value='{{ $id }}' {{ in_array($id, old('switch"+userId[i]+"', [])) ? 'selected' : '' }}>{{ $user }}</option>" +
                            @endforeach +
                                "</select>" +
                            "</div> "+

                            "</div></div>");
                    }

                $('#section2').find('.card').remove().end().append(input);
            });

            //$('.switch').on('click', '.switch'+counter, function () {}
            $('#section2').on('click', '.switch input', function () {
                const getId = $(this).val();
                $('.switch'+getId).toggle();
                //alert($(this).val());
            });
        });



    </script>


    <script>
        $(function () {

            $('#project').on('change', function() {
                //alert( this.value );
                load_users(this.value);
            });


            function load_users(id = '') {
                let userList = $("#user_list");
                userList.empty();
                $.ajax({
                    headers: {'x-csrf-token': _token},
                    url:"{{ route('admin.workflow-management.load-users', '') }}"+"/"+id,
                    method: 'GET',
                    //data: {view: id},
                    dataType: 'json',
                    success: function (data) {
                        $.each(data.data, function(index, item){
                            userList.append('<option value="'+index+'" "selected">'+item+'</option>');
                        });

                    },
                    error: function(data){
                        alert("Erreur de chargement des utilisateurs. Veuillez Sélectionner à nouveau l'entité")
                        console.log('Erreur de chargement des utilisateurs');
                    }
                });
            }

        });
    </script>
@endsection
