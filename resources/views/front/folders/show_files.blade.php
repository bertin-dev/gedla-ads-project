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
                            {{ trans('global.add') }} {{trans('global.upload_file')}}
                        </a>
                        {{--<a class="btn btn-success" href="{{ route('folders.create') }}?parent_id={{ $folderItems->id }}">
                            {{ trans('global.add') }} Create a new folder
                        </a>--}}
                        <a class="btn btn-success" href="{{ route('create-document') }}?folder_id={{ $folderItems->id }}">
                            {{ trans('global.create') }} {{trans('global.file')}}
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
                        @include('front.folders.upload', ['folder' => $folderItems])
                    @endif



                    @foreach ($folderItems->files as $file)
                        @php
                            $result=match($file->mime_type){"application/pdf"=>url('images/pdf.png'),"text/plain"=>url('images/txt.png'),"application/vnd.openxmlformats-officedocument.wordprocessingml.document"=>url('images/docx.png'),"application/x-msaccess"=>url('images/access.png'),"application/vnd.ms-visio.drawing.main+xml"=>url('images/visio.png'),"application/vnd.openxmlformats-officedocument.presentationml.presentation"=>url('images/power_point.png'),"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"=>url('images/xlsx.png'),"image/jpeg"=>url('images/file-thumbnail.png'),default => '',};
                            $realSize = number_format($file->size/1024, 1, '.', '');
                        @endphp
                        {{--show media file where state is unlocked and file has not validate--}}
                        @if($file->state=="unlocked" && $file->signing == 0)
                        <div class="col-lg-4">
                            <div class="card" data-toggle="modal" data-target=".bd-example-modal-lg"
                                 data-id="{{$file->id}}" data-name="{{ucfirst(strtolower(Str::substr($file->file_name, 14)))}}" data-size="{{$realSize}}"
                                 data-item_type="{{$result}}" data-url="{{$file->getUrl()}}" data-version="{{$file->version}}">
                                <div class="row no-gutters">
                                    <div class="col-sm-4">
                                        <img class="img-thumbnail" src="{{ $result ?? url('images/file-thumbnail.png') }}"
                                             alt="{{ $file->name }}" title="{{ $file->name }}">
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="card-body" style="padding: 5px 5px 0;">
                                            <h5 class="card-title">{{ strtolower(Str::substr($file->file_name, 14, 42)) }}</h5>
                                            <div style="margin-top: 13px">
                                                <span><small style="margin-right: 70px">{{ date('d/m/Y' , strtotime($file->created_at)) }}</small></span>
                                                <span> <small class="text-right">{{$realSize}} KO</small></span>
                                            </div>

                                            {{--@dd($foldersUsers->receiveOperations->where('media_id', $file->id)->first()->priority)--}}
                                            @php
                                                $mediaAndOperation = $foldersUsers->receiveOperations->where('media_id', $file->id)->first();
                                            @endphp
                                            @if($mediaAndOperation != null)
                                                @if($mediaAndOperation->priority == "high")
                                                    <span class="badge rounded-pill badge-danger">.</span>
                                                @elseif($mediaAndOperation->priority == "medium")
                                                    <span class="badge rounded-pill badge-warning">.</span>
                                                @else
                                                    <span class="badge rounded-pill badge-info">.</span>
                                                @endif
                                            @endif


                                            <span>
                                                @if($file->step_workflow !=null)

                                                    @php
                                                        $oldValue = json_decode($file->step_workflow);
                                                        $counter = 0;
                                                        for($i=0; $i<count($oldValue); $i++){
                                                            if($oldValue[$i]->state == "finish"){
                                                                $counter++;
                                                            }
                                                        }
                                                    @endphp


                                                        @if($counter==count($oldValue))
                                                            <small class="alert-success">validate</small>
                                                        @else
                                                            <small class="alert-success">receive</small>
                                                        @endif


                                                    <small class="alert-link" style="font-size: 9px;">{{trans('global.expiry')}}: {{now()->diffForHumans($mediaAndOperation->deadline)}}</small>
                                                @else
                                                    <small class="alert-info">import</small>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
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
                                {{--<img class="img-thumbnail img_detail">--}}
                                <div class="iframeFile"></div>
                                <div class="text-left">
                                    @can('open_file_access')
                                    <a class="myUrl col-lg-4" target="_blank">{{trans('global.open')}} </a>
                                    @endcan
                                    @can('edit_file_access')
                                    <a class="col-lg-4" target="_blank" href="#">{{trans('global.edit_document')}} </a>
                                    @endcan
                                    @can('download_access')
                                    <a href="#" class="mediaDownload col-lg-4"> {{trans('global.download')}}</a>
                                    @endcan
                                    @can('archive_file_access')
                                    <a href="#" class="col-lg-4" data-toggle="modal" data-target=".open-file">{{trans('global.archive_document')}} </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="col-md-6 ml-auto">
                                <span class="folder_id col-lg-4"></span>
                                <span class="folder_size col-lg-4"></span>
                                <span class="version col-lg-4"></span>
                                <hr>

                                <div class="">
                                    <h5>{{trans('global.my_recent_activity')}}</h5>
                                    <div class="loading">
                                        <center><img src="{{asset('images/loading.gif')}}" alt=""></center>
                                    </div>
                                    <h6 class="initMyActivity"></h6>
                                    <ul class="myActivity"></ul>


                                    <!---------------------------->
                                    <section id="schedule" class="section-with-bg">
                                        <div class="section-header">
                                            <h2>Event Schedule</h2>
                                            <p>Here is our event schedule</p>
                                        </div>
                                        <div class="tab-content row justify-content-center aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">

                                            <!-- Schdule Day 1 -->
                                            <div role="tabpanel" class="col-lg-9 tab-pane fade show active" id="day-1">

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>09:30 AM</time></div>
                                                    <div class="col-md-10">
                                                        <h4>Registration</h4>
                                                        <p>Fugit voluptas iusto maiores temporibus autem numquam magnam.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>10:00 AM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/1.jpg" alt="Brenden Legros">
                                                        </div>
                                                        <h4>Keynote <span>Brenden Legros</span></h4>
                                                        <p>Facere provident incidunt quos voluptas.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>11:00 AM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/2.jpg" alt="Hubert Hirthe">
                                                        </div>
                                                        <h4>Et voluptatem iusto dicta nobis. <span>Hubert Hirthe</span></h4>
                                                        <p>Maiores dignissimos neque qui cum accusantium ut sit sint inventore.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>12:00 AM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/3.jpg" alt="Cole Emmerich">
                                                        </div>
                                                        <h4>Explicabo et rerum quis et ut ea. <span>Cole Emmerich</span></h4>
                                                        <p>Veniam accusantium laborum nihil eos eaque accusantium aspernatur.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>02:00 PM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/4.jpg" alt="Jack Christiansen">
                                                        </div>
                                                        <h4>Qui non qui vel amet culpa sequi. <span>Jack Christiansen</span></h4>
                                                        <p>Nam ex distinctio voluptatem doloremque suscipit iusto.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>03:00 PM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/5.jpg" alt="Alejandrin Littel">
                                                        </div>
                                                        <h4>Quos ratione neque expedita asperiores. <span>Alejandrin Littel</span></h4>
                                                        <p>Eligendi quo eveniet est nobis et ad temporibus odio quo.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>04:00 PM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/6.jpg" alt="Willow Trantow">
                                                        </div>
                                                        <h4>Quo qui praesentium nesciunt <span>Willow Trantow</span></h4>
                                                        <p>Voluptatem et alias dolorum est aut sit enim neque veritatis.</p>
                                                    </div>
                                                </div>

                                            </div>
                                            <!-- End Schdule Day 1 -->

                                            <!-- Schdule Day 2 -->
                                            <div role="tabpanel" class="col-lg-9  tab-pane fade" id="day-2">

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>10:00 AM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/1.jpg" alt="Brenden Legros">
                                                        </div>
                                                        <h4>Libero corrupti explicabo itaque. <span>Brenden Legros</span></h4>
                                                        <p>Facere provident incidunt quos voluptas.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>11:00 AM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/2.jpg" alt="Hubert Hirthe">
                                                        </div>
                                                        <h4>Et voluptatem iusto dicta nobis. <span>Hubert Hirthe</span></h4>
                                                        <p>Maiores dignissimos neque qui cum accusantium ut sit sint inventore.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>12:00 AM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/3.jpg" alt="Cole Emmerich">
                                                        </div>
                                                        <h4>Explicabo et rerum quis et ut ea. <span>Cole Emmerich</span></h4>
                                                        <p>Veniam accusantium laborum nihil eos eaque accusantium aspernatur.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>02:00 PM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/4.jpg" alt="Jack Christiansen">
                                                        </div>
                                                        <h4>Qui non qui vel amet culpa sequi. <span>Jack Christiansen</span></h4>
                                                        <p>Nam ex distinctio voluptatem doloremque suscipit iusto.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>03:00 PM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/5.jpg" alt="Alejandrin Littel">
                                                        </div>
                                                        <h4>Quos ratione neque expedita asperiores. <span>Alejandrin Littel</span></h4>
                                                        <p>Eligendi quo eveniet est nobis et ad temporibus odio quo.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>04:00 PM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/6.jpg" alt="Willow Trantow">
                                                        </div>
                                                        <h4>Quo qui praesentium nesciunt <span>Willow Trantow</span></h4>
                                                        <p>Voluptatem et alias dolorum est aut sit enim neque veritatis.</p>
                                                    </div>
                                                </div>

                                            </div>
                                            <!-- End Schdule Day 2 -->

                                            <!-- Schdule Day 3 -->
                                            <div role="tabpanel" class="col-lg-9  tab-pane fade" id="day-3">

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>10:00 AM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/2.jpg" alt="Hubert Hirthe">
                                                        </div>
                                                        <h4>Et voluptatem iusto dicta nobis. <span>Hubert Hirthe</span></h4>
                                                        <p>Maiores dignissimos neque qui cum accusantium ut sit sint inventore.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>11:00 AM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/3.jpg" alt="Cole Emmerich">
                                                        </div>
                                                        <h4>Explicabo et rerum quis et ut ea. <span>Cole Emmerich</span></h4>
                                                        <p>Veniam accusantium laborum nihil eos eaque accusantium aspernatur.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>12:00 AM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/1.jpg" alt="Brenden Legros">
                                                        </div>
                                                        <h4>Libero corrupti explicabo itaque. <span>Brenden Legros</span></h4>
                                                        <p>Facere provident incidunt quos voluptas.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>02:00 PM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/4.jpg" alt="Jack Christiansen">
                                                        </div>
                                                        <h4>Qui non qui vel amet culpa sequi. <span>Jack Christiansen</span></h4>
                                                        <p>Nam ex distinctio voluptatem doloremque suscipit iusto.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>03:00 PM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/5.jpg" alt="Alejandrin Littel">
                                                        </div>
                                                        <h4>Quos ratione neque expedita asperiores. <span>Alejandrin Littel</span></h4>
                                                        <p>Eligendi quo eveniet est nobis et ad temporibus odio quo.</p>
                                                    </div>
                                                </div>

                                                <div class="row schedule-item">
                                                    <div class="col-md-2"><time>04:00 PM</time></div>
                                                    <div class="col-md-10">
                                                        <div class="speaker">
                                                            <img src="assets/img/speakers/6.jpg" alt="Willow Trantow">
                                                        </div>
                                                        <h4>Quo qui praesentium nesciunt <span>Willow Trantow</span></h4>
                                                        <p>Voluptatem et alias dolorum est aut sit enim neque veritatis.</p>
                                                    </div>
                                                </div>

                                            </div>
                                            <!-- End Schdule Day 2 -->

                                        </div>

                                    </section>

                                </div>

                                <div class="card workflow_form" style="display: none">
                                    <div class="card-header">{{trans('global.workflow_validation')}}</div>

                                    <div class="card-body">
                                        @if (session('status'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session('status') }}
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('operation.store') }}">
                                            @csrf
                                            <input type="hidden" name="send_validation_workflow" value="send_validation_workflow">
                                            <input id="media_id" type="hidden" name="media_id" />
                                                <div class="form-group">
                                                    <label for="deadline">{{trans('global.term')}}</label>
                                                    <input type="date" id="deadline" name="deadline" class="form-control">
                                                </div>

                                                <div class="form-group">
                                                    <label for="priority">{{trans('global.priority')}}</label>
                                                    <select name="priority" id="priority" class="form-control">
                                                        <option value="low">{{trans('global.low')}}</option>
                                                        <option value="medium">{{trans('global.means')}}</option>
                                                        <option value="high">{{trans('global.important')}}</option>
                                                    </select>
                                                </div>

                                            <div class="form-group">
                                                <label for="visibility">{{trans('global.visibility')}}</label>
                                                <select name="visibility" id="visibility" class="form-control">
                                                    <option value="public">{{trans('global.public')}}</option>
                                                    <option value="private">{{trans('global.private')}}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="user_assign">{{trans('global.assigned_to')}}</label>
                                                <select name="user_assign" id="user_assign" class="form-control">
                                                    @foreach($users as $id => $user)
                                                        <option value="{{ $id }}" {{ in_array($id, old('user_assign', [])) ? 'selected' : '' }}>{{ $user }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="message">{{trans('global.message')}}</label>
                                                <textarea name="message" id="message" class="form-control"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <div class="form-check checkbox">
                                                    <input class="form-check-input" type="checkbox" name="flexCheckChecked" id="flexCheckChecked" style="vertical-align: middle;">
                                                    <label class="form-check-label" for="flexCheckChecked" style="vertical-align: middle;">
                                                        {{trans('global.send_notification_by_email')}}
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <button type="submit" class="btn bg-card-violet text-white">{{trans('global.send')}}</button>
                                                <button type="reset" class="btn bg-card-green text-white">{{trans('global.cancel')}}</button>
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
                        @can('validate_file_access')
                        <li><a href="#" class="document_id validation validate_file_access" title="validation">{{trans('global.validate')}}</a></li>
                        <li><a href="#" class="document_id validation_signature validate_file_access" title="validation_signature">validation avec signature</a></li>
                        <li><a href="#" class="validation_paraphe validate_file_access" title="validation_paraphe">validation avec paraphe</a></li>
                        <li><a href="#" class="document_id rejected validate_file_access" title="rejected">Rejeter/refuser</a></li>
                        @endcan
                        @can('operation_access')
                            <li><a href="#" class="workflow_validate workflow">{{trans('global.start_workflow_validation')}}</a></li>
                        @endcan
                    </ul>
                </div>

                {{--<iframe src="{{$file->getUrl()}}"></iframe>--}}

            </div>
        </div>
    </div>


    <div class="modal fade open-file" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
                            {{--<iframe src="{{$file->getUrl()}}"></iframe>--}}

                            <form method="post" action="" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>{{trans('global.name')}}</label>
                                    <input type="text" name="name" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label><strong>{{trans('global.description')}} :</strong></label>
                                    <textarea class="ckeditor form-control" name="description"></textarea>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-success btn-sm">{{trans('global.save')}}</button>
                                </div>
                            </form>


                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <h5 class="modal-title">{{ trans('global.file_action') }}</h5><br>
                    <ul>
                        <li><a href="#">{{trans('global.edit_document')}}</a></li>
                    </ul>
                </div>

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
