@extends('layouts.front')

@section('content')

    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">

            <div class="row">


                {{--     @foreach ($folder->children as $folder)
                         <div class="col-lg-4">
                             <div class="card">
                                 <div class="row no-gutters">
                                     <div class="col-sm-4">
                                         <img class="img-thumbnail" src="{{ $folder->thumbnail ? $folder->thumbnail->thumbnail : url('images/empty-folder.png') }}" alt="{{ $folder->name }}" title="{{ $folder->name }}">
                                     </div>
                                     <div class="col-sm-8">
                                         <div class="card-body" style="padding: 5px 5px 0;">
                                             <h5 class="card-title">Folder {{ $folder->name }}</h5>
                                             <p class="text-right">
                                                 <i class="icon-user"></i>
                                                 <a href="{{ route('folders.show', [$folder]) }}" class="btn-link">Acquisition des documents</a>
                                             </p>
                                             --}}{{--  <a href="#" class="btn btn-primary">View Profile</a>--}}{{--
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     @endforeach--}}



                @foreach($foldersUsers->multiFolders->where('id', $folder->id) as $folderItems)
                    <div class="col-lg-12">
                        <a class="btn btn-success" href="{{ route('folders.upload') }}?folder_id={{ $folderItems->id }}&functionality={{$folderItems->functionality}}">
                            {{ trans('global.add') }} Upload file
                        </a>
                        <a class="btn btn-success" href="{{ route('folders.create') }}?parent_id={{ $folderItems->id }}">
                            {{ trans('global.add') }} Create a new folder
                        </a>
                    </div>


                    <div class="col-lg-12" style="margin-top: 20px">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                    </div>


                    @if($folderItems->files->isEmpty())
                        @include('front.folders.upload', ['folder' => $foldersUsers])
                    @endif



                    @foreach ($folderItems->files as $file)
                        @php
                            $result=match($file->mime_type){"application/pdf"=>url('images/pdf.png'),"text/plain"=>url('images/txt.png'),"application/vnd.openxmlformats-officedocument.wordprocessingml.document"=>url('images/docx.png'),"application/x-msaccess"=>url('images/access.png'),"application/vnd.ms-visio.drawing.main+xml"=>url('images/visio.png'),"application/vnd.openxmlformats-officedocument.presentationml.presentation"=>url('images/power_point.png'),"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"=>url('images/xlsx.png'),"image/jpeg"=>url('images/file-thumbnail.png'),default => '',};
                            $realSize = number_format($file->size/1024, 1, '.', '');
                        @endphp
                        <div class="col-lg-4">
                            <div class="card" data-toggle="modal" data-target=".bd-example-modal-lg"
                                 data-id="{{$file->id}}" data-name="{{ucfirst(strtolower(Str::substr($file->file_name, 14)))}}" data-size="{{$realSize}}"
                                 data-item_type="{{$result}}" data-url="{{$file->getUrl()}}">
                                <div class="row no-gutters">
                                    <div class="col-sm-4">
                                        <img class="img-thumbnail" src="{{ $result ?? url('images/file-thumbnail.png') }}"
                                             alt="{{ $file->name }}" title="{{ $file->name }}">
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="card-body" style="padding: 5px 5px 0;">
                                            <h5 class="card-title">{{ strtolower(Str::substr($file->file_name, 14, 42)) }}</h5>
                                            <div style="margin-top: 23px">
                                                <span>{{ date('d/m/Y' , strtotime($file->created_at)) }}</span>
                                                <div class="text-right"> {{$realSize}} KO</div>
                                                {{--<a href="{{ $file->getUrl() }}" target="_blank" class="btn-link">Acquisition</a>--}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach


            </div>

        </div>
    </div>
    <!--**********************************
        Content body end
    ***********************************-->

    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title file-title">{{trans('global.file_details')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <img class="img-thumbnail img_detail">
                                <div class="text-left">
                                    <a class="myUrl col-lg-4" target="_blank">Ouvrir </a>
                                    <a href="#" class="col-lg-4"> Télécharger</a>
                                    <a href="#" class="col-lg-4">Archiver le document </a>
                                </div>
                            </div>

                            <div class="col-md-6 ml-auto">
                                <span class="folder_id col-lg-4"></span>
                                <span class="folder_size col-lg-4"></span>
                                <span class="version col-lg-4">Version: 0.1</span>
                                <hr>

                                <div class="card workflow_form" style="display: none">
                                    <div class="card-header">Workflow de Validation</div>

                                    <div class="card-body">
                                        @if (session('status'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session('status') }}
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('operation.store') }}">
                                            @csrf
                                            <input id="media_id" type="hidden" name="media_id" />
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
                                                    <option value="confidential">Confidentiel</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="user_assign">Assigné à</label>
                                                <select name="user_assign" id="user_assign" class="form-control">
                                                    @foreach(\App\Models\User::all() as $user)
                                                         @if($user->id != Auth::user()->id)
                                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
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
                                                <button type="submit" class="btn bg-card-violet text-white">Envoyer</button>
                                                <button type="reset" class="btn bg-card-green text-white">Annuler</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <h5 class="modal-title">{{ trans('global.file_action') }}</h5><br>
                    <ul>
                        <li><a href="#">Valider</a></li>
                        @can('operation_access')
                            <li><a href="#" class="workflow_validate">Démarrer le workflow de validation</a></li>
                        @endcan
                    </ul>
                </div>

                {{--<iframe src="{{$file->getUrl()}}"></iframe>--}}

            </div>
        </div>
    </div>



    {{-- <div class="container">
         <div class="row justify-content-center">
             <div class="col-md-10">
                 <div class="card">
                     <div class="card-header">My Assigned Projects</div>

                     <div class="card-body">
                         @if (session('status'))
                             <div class="alert alert-success" role="alert">
                                 {{ session('status') }}
                             </div>
                         @endif

                         <div class="row">
                             @foreach ($projects as $project)
                                 <div class="col-lg-2 col-md-3 col-sm-4 mb-3">
                                     <div class="card">
                                         <a href="{{ route('folders.show', $project) }}">
                                             <img class="card-img-top" src="{{ $project->thumbnail ? $project->thumbnail->thumbnail : url('images/no-image.png') }}" alt="{{ $project->name }}">
                                         </a>
                                         <div class="card-footer text-center">
                                             <a href="{{ route('projects.show', $project) }}">
                                                 {{ $project->name }}
                                             </a>
                                         </div>
                                     </div>
                                 </div>
                             @endforeach
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>--}}
@stop
