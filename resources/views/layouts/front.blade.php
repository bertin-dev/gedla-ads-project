<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, , user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{!! url('images/logo-official.jpg') !!}">
    <title>{{ trans('panel.home_gedla') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{--<link rel="stylesheet" href="{{asset('css/bootstrap_4.1.3.css')}}">
    <link rel="stylesheet" href="{{asset('css/all.css')}}">
    <link rel="stylesheet" href="{{asset('css/jquery.dataTables.css')}}">
    <link rel="stylesheet" href="{{asset('css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/buttons.dataTables.css')}}">
    <link rel="stylesheet" href="{{asset('css/select.dataTables.css')}}">
    <link rel="stylesheet" href="{{asset('css/select2.css')}}">
    <link rel="stylesheet" href="{{asset('css/bootstrap-datetimepicker.css')}}">
    <link rel="stylesheet" href="{{asset('css/coreui.css')}}">
    <link rel="stylesheet" href="{{asset('css/font-awesome.css')}}">
    <link rel="stylesheet" href="{{asset('css/dropzone.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/perfect-scrollbar.css')}}">--}}

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet"/>
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"
        rel="stylesheet"/>
    <link href="https://unpkg.com/@coreui/coreui@3.2/dist/css/coreui.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.0/css/perfect-scrollbar.min.css"
          rel="stylesheet"/>

    <link href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    @yield('styles')
    @livewireStyles
</head>
<body class="c-app">
<div id="cover-spin"></div>
@include('partials.menu-user')

<div class="c-wrapper">
    <header class="c-header c-header-fixed px-3">
        <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
            <i class="fas fa-fw fa-bars"></i>
        </button>

        <a class="c-header-brand d-lg-none" href="#">{{ trans('panel.site_title') }}</a>

        <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
            <i class="fas fa-fw fa-bars"></i>
        </button>

        <ul class="c-header-nav ml-auto">
            @if(count(config('panel.available_languages', [])) > 1)
                <li class="c-header-nav-item dropdown d-md-down-none">
                    <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-sign-language fa-sm fa-fw mr-2 text-gray-400"></i>
                        {{ strtoupper(app()->getLocale()) }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach(config('panel.available_languages') as $langLocale => $langName)
                            <a class="dropdown-item" href="{{ url()->current() }}?change_language={{ $langLocale }}">{{ strtoupper($langLocale) }} ({{ $langName }})</a>
                        @endforeach
                    </div>
                </li>
            @endif
        </ul>

        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        @php
                            $user = auth()->user();
                            $notifications = $user->notifications()->paginate(10);
                        @endphp

                        <li class="nav-item dropdown notification-ui show">
                            <a class="nav-link dropdown-toggle notification-ui_icon" href="#" id="navbarDropdownNotifications" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger opacity-75 text-white">{{ $user->unreadNotifications()->count() }}</span>
                            </a>
                            <div class="dropdown-menu notification-ui_dd" aria-labelledby="navbarDropdownNotifications">
                                <div class="notification-ui_dd-header">
                                    <h3 class="text-center">{{$user->unreadNotifications()->count()<2 ? trans('global.notification') : trans('global.notifications')}}</h3>
                                </div>
                                <div class="notification-ui_dd-content">

                                    @foreach($notifications as $notification)

                                        @if($notification->unread())
                                            <a href="#!" class="notification-list text-dark">
                                                <div class="notification-list_img">
                                                    <img src="{{asset('images/pdf.png')}}" alt="user">
                                                </div>
                                                <div class="notification-list_detail">
                                                    <p><b>{{$notification->data['subject'] ?? ""}}</b> <br><span class="text-muted">{{$notification->data['subject'] ?? ''}}</span></p>
                                                    <p class="nt-link text-body">{{$notification->data['body'] ?? ""}}</p>
                                                </div>
                                                <p><small>{{--<time class="timeago" datetime="{{$notification->created_at ?? ''}}"></time>--}} {{ $notification->created_at->diffForHumans() }}</small></p>
                                            </a>
                                        @else
                                            <a href="#!" class="notification-list notification-list--unread text-dark">
                                                <div class="notification-list_img">
                                                    <img class="img-thumbnail" src="{{asset('images/pdf.png')}}" alt="user">
                                                </div>
                                                <div class="notification-list_detail">
                                                    <p><b>{{$notification->data['subject'] ?? ""}}</b> <br><span class="text-muted">{{$notification->data['subject'] ?? ''}}</span></p>
                                                    <p class="nt-link text-body">{{$notification->data['body'] ?? ""}} </p>
                                                </div>
                                                <p><small>{{--<time class="timeago" datetime="{{$notification->created_at ?? ''}}"></time>--}} {{ $notification->created_at->diffForHumans() }}</small></p>
                                            </a>
                                        @endif

                                    @endforeach
                                </div>
                                @if($user->unreadNotifications()->count() >= 10)
                                    <div class="notification-ui_dd-footer">
                                        <a href="{{ route('notifications.index') }}" class="btn btn-success btn-block"> {{ trans('global.view') }} {{ trans('global.all') }}</a>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ ucfirst(Auth::user()->name) }}</span>
                                    <img class="img-profile rounded-circle" src="https://source.unsplash.com/QAB-WJcbgJk/60x60">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}" data-toggle="modal" data-target="#logoutModal"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="c-body">
        <main class="c-main">

            @isset($title)
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2" style="padding-left: 15px; padding-right: 15px;">
                            <div class="col-sm-6">
                                <h1 class="m-0">{{ $title }}</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">{{trans('global.home')}}</a></li>
                                    <li class="breadcrumb-item active">{{trans('global.dashboard')}}</li>
                                    <li class="breadcrumb-item">{{ trans('global.last_connexion') }} : {{ Carbon\Carbon::parse(auth()->user()->last_login_at)->diffForHumans() }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset

            <div class="container-fluid">
                @if(session('message'))
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="alert alert-success" role="alert">{{ session('message') }}</div>
                        </div>
                    </div>
                @endif
                @if($errors->count() > 0)
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')

            </div>


        </main>
        <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </div>

    <!--**********************************
    Footer start
***********************************-->
@include('partials.copy')
<!--**********************************
        Footer end
    ***********************************-->
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
{{--<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{asset('js/bootstrap.js')}}"></script>
<script src="{{asset('js/popper.js')}}"></script>
<script src="{{asset('js/perfect-scrollbar.js')}}"></script>
<script src="{{asset('js/coreui.js')}}"></script>
<script src="{{asset('js/jquery.dataTables.js')}}"></script>
<script src="{{asset('js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('js/buttons.flash.js')}}"></script>
<script src="{{asset('js/buttons.html5.js')}}"></script>
<script src="{{asset('js/buttons.print.js')}}"></script>
<script src="{{asset('js/buttons.colVis.js')}}"></script>
<script src="{{asset('js/build_pdfmake.js')}}"></script>
<script src="{{asset('js/vfs_fonts.js')}}"></script>
<script src="{{asset('js/jszip.js')}}"></script>
<script src="{{asset('js/dataTables.select.min.js')}}"></script>
<script src="{{asset('js/ckeditor5.js')}}"></script>
<script src="{{asset('js/moment.js')}}"></script>
<script src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('js/select2.full.js')}}"></script>
<script src="{{asset('js/dropzone.min.js')}}"></script>
<script src="{{asset('js/jquery.timego.js')}}"></script>--}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.0/perfect-scrollbar.min.js"></script>
<script src="https://unpkg.com/@coreui/coreui@3.2/dist/js/coreui.min.js"></script>
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/super-build/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timeago/1.6.5/jquery.timeago.min.js"></script>
<script src="{{asset('js/mustache.js')}}"></script>
<script src="{{asset('js/jquery.notif.js')}}"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.js') }}"></script>

@if(str_replace('_', '-', app()->getLocale()) === "en")
    <script src="{{ asset('js/jquery.timeago.en.js') }}"></script>
@else
    <script src="{{ asset('js/jquery.timeago.fr.js') }}"></script>
@endif


<script>
    $(function() {
        let copyButtonTrans = '{{ trans('global.datatables.copy') }}'
        let csvButtonTrans = '{{ trans('global.datatables.csv') }}'
        let excelButtonTrans = '{{ trans('global.datatables.excel') }}'
        let pdfButtonTrans = '{{ trans('global.datatables.pdf') }}'
        let printButtonTrans = '{{ trans('global.datatables.print') }}'
        let colvisButtonTrans = '{{ trans('global.datatables.colvis') }}'
        let selectAllButtonTrans = '{{ trans('global.select_all') }}'
        let selectNoneButtonTrans = '{{ trans('global.deselect_all') }}'

        let languages = {
            'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json',
            'fr': 'https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json'
        };

        $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, { className: 'btn' })
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: languages['{{ app()->getLocale() }}']
            },
            columnDefs: [{
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }, {
                orderable: false,
                searchable: false,
                targets: -1
            }],
            select: {
                style:    'multi+shift',
                selector: 'td:first-child'
            },
            order: [],
            scrollX: true,
            pageLength: 100,
            dom: 'lBfrtip<"actions">',
            buttons: [
                {
                    extend: 'selectAll',
                    className: 'btn-primary',
                    text: selectAllButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    },
                    action: function(e, dt) {
                        e.preventDefault()
                        dt.rows().deselect();
                        dt.rows({ search: 'applied' }).select();
                    }
                },
                {
                    extend: 'selectNone',
                    className: 'btn-primary',
                    text: selectNoneButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'copy',
                    className: 'btn-default',
                    text: copyButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-default',
                    text: csvButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-default',
                    text: excelButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-default',
                    text: pdfButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn-default',
                    text: printButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'colvis',
                    className: 'btn-default',
                    text: colvisButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });

        $.fn.dataTable.ext.classes.sPageButton = '';
    });
</script>
@yield('scripts')



<script>
    let editor;
      ClassicEditor
        .create( document.querySelector( '#description1' ) )
          .then( newEditor => {
              editor = newEditor;
          } )
        .catch( error => {
            console.error( error );
        } );

    /*CKEDITOR.replace('description1', {
        filebrowserUploadUrl: "{{ route('folders.storeMedia') }}",
        filebrowserUploadMethod: 'form'
    });*/


    $(function () {
        let submitRequest = $('#submit');
        submitRequest.on('click', function () {
            const editorData = editor.getData();
            let fullLoading =  $('#cover-spin');

            if(editorData.length === 0 || fullLoading.length === 0){
                alert("Veuillez remplir tous les champs");
                return;
            }

            $.ajax({
                url: "{{ route('upload-document') }}",
                method: 'POST',
                data: {
                    fileName: $('#fileName').val(),
                    documentFormat: $('#document_format').val(),
                    description1: editorData,
                    folder_id: {{$folderId ?? 0}},
                    parapheur_id: {{$parapheurId ?? 0}},
                },
                beforeSend: function (){
                    submitRequest.text("Enregistrement encours....");
                    fullLoading.show();
                },
                dataType: 'json',
                success: function (data) {
                    //alert(data.title);
                    $('body').notif({
                        title: 'Opération Réussie',
                        content: "Enregistrement effectué avec succès",
                        img: '{{asset('images/success-notif.jpg')}}',
                        cls: 'success1'
                    });

                    setTimeout(function() {
                        window.history.back();
                        location.reload();
                    }, 5000);

                },
                error: function () {
                    alert("une erreur est survenue");
                },
                complete: function (){
                    submitRequest.text("Enregistrer");
                    fullLoading.hide();
                }
            });

        });
    });

    jQuery(document).ready(function() {
        $("time.timeago").timeago();
    });
</script>


<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let docId = 0;
    $(function () {
        $('.bd-example-modal-lg').on('show.bs.modal', function (e) {
            let id = $(e.relatedTarget).data('id');
            docId = id;
            let url = $(e.relatedTarget).data('url');
            let name = $(e.relatedTarget).data('name');
            let size = $(e.relatedTarget).data('size');
            let version = $(e.relatedTarget).data('version');
            let itemType = $(e.relatedTarget).data('item_type');
            let mimeType = $(e.relatedTarget).data('mime_type');
            let loadFile = $('.iframeFile');
            let mediaId = $('.mediaId');
            //let objects = $('#objectLink').data('name');

            switch (mimeType){
                case "application/pdf":
                    loadFile.html("<embed src='"+url+"' id='100' width='100%' height='600px'/>");
                    break;
                case "image/jpeg":
                    loadFile.html("<img class='img-thumbnail' src='"+itemType+"' alt='"+name+"' title='"+name+"' >");
                    break;
                case "application/vnd.openxmlformats-officedocument.wordprocessingml.document": //Word docx
                    //loadFile.html("<iframe src='https://view.officeapps.live.com/op/view.aspx?src=http%3A%2F%2Fieee802%2Eorg%3A80%2Fsecmail%2FdocIZSEwEqHFr%2Edoc' frameborder='0' style='width:100%;min-height:640px;'></iframe>");
                    //loadFile.html("<iframe src='https://view.officeapps.live.com/op/embed.aspx?src="+encodeURIComponent(url)+"' width='100%' height='623px' frameborder='0'></iframe>");
                    loadFile.html("<iframe data-url='"+url+"' src='https://view.officeapps.live.com/op/embed.aspx?src=http%3A%2F%2Fieee802%2Eorg%3A80%2Fsecmail%2FdocIZSEwEqHFr%2Edoc' width='100%' height='600px' frameborder='0'></iframe>");
                    break;
                case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet": //Excel .xlsx
                    loadFile.html("<iframe data-url='"+url+"' src='https://view.officeapps.live.com/op/embed.aspx?src="+encodeURIComponent(url)+"' width='100%' height='623px' frameborder='0'></iframe>");
                    break;
                default:
                    loadFile.html("<img class='img-thumbnail' src='"+itemType+"' alt='"+name+"' title='"+name+"' >");
            }

           /* $('.iframeFile').attr({
                src: url
            });*/
            $('.file-title').text('{{trans('global.file_details')}} : ' + name);
            $('.folder_id').text('{{ trans('global.folder') }} : ' + id + '');
            $('.folder_size').text('{{ trans('global.size') }} : ' + size + ' KO');
            $('.version').text('{{ trans('global.version') }} : ' + version);
            $('.myUrl').attr("href", url);
            $('.mediaDownload').attr("href", id);
            $('#media_id').attr("value", id);
            $('.document_id').attr("href", id);
            mediaId.attr("href", mediaId.attr("href") + "&mediaId=" + id);
            $('.documentStorage').attr("href", id);
            console.log(id);
            updateMediaTable(id, name);
        });


        $('.workflow_validate').click(function () {
            $('.workflow_form').toggle('slow');
        });


        //when user as seen Media, media tableupdate
        function updateMediaTable(id, name) {
            /*let url = "{{ route('admin.workflow-management.preview-document', ":id") }}";
            url = url.replace(':id', id);*/
            let myActivity = $(".myActivity");
            let initMyActivity = $('.initMyActivity');
            myActivity.empty();
           // initMyActivity.empty();
            $.ajax({
                url: "{{ route('admin.workflow-management.preview-document') }}",
                method: 'POST',
                data: {
                    id: id,
                    name: name
                },
                dataType: 'json',
                beforeSend: function (){
                    $('.loading').show();
                },
                success: function (data) {


                    $.each(data.tracking, function(index, item){
                        myActivity.append(item.description);
                    });

                    /*if(data.media.parapheur == null){
                        initMyActivity.text("{{trans('global.import')}} {{trans('global.file')}} " + data.media.file_name.substring(14) + " {{trans('global.shared_file')}} " + formatDate(data.media.created_at));
                    }

                    $.each(data.media.operations, function(index, operationItem){

                        if(operationItem.operation_state==="pending"){
                            myActivity.append('<li><strong>' + getUser(operationItem.user_id_sender) + '</strong> à envoyer un document en attente de validation à <strong>' + getUser(operationItem.user_id_receiver) + '</strong></li>');
                        }else{
                            myActivity.append('<li><strong>' + getUser(operationItem.user_id_receiver) + '</strong> a validé le document ' + data.media.file_name.substring(14) + '</li>');
                        }
                    });*/


                    //let dataURL = $(this).attr('data-id');
                    /*if(data.workflow_validation != null){
                        $('.workflow').hide();
                    }else{
                        $('.workflow').show();
                    }*/
                },
                error: function(){
                    $('.loading').hide();
                    alert("Error founded where user display document");
                    console.log('Error founded where user display document');
                },
                complete:function (){
                    $('.loading').hide();
                }
            });
        }

    });

    $(function () {

        $('.open-document').on('click', function (){
            $.ajax({
                url: "{{ route('admin.workflow-management.open-document') }}",
                method: 'POST',
                data: {
                    id: docId
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                },
                error: function(){
                    console.log('Error founded where user display document');
                }
            });
        });

        $('.mediaDownload').on('click', function (e) {
            e.preventDefault();
            let getId = $('.mediaDownload').attr('href');
            //alert(link);
            let url = "{{ route('admin.workflow-management.download') }}";
            //url = url.replace(':link', link);
            //alert(url);
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    id: getId
                },
                dataType: 'json',
                success: function (data) {
                    $('body').notif({
                        title: 'Opération Réussie',
                        content: data,
                        img: '{{asset('images/success-notif.jpg')}}',
                        cls: 'success1'
                    });
                },
                error: function () {
                    console.log('Link not found');
                },
                complete: function (){
                    $('body').notif({
                        title: 'Opération Réussie',
                        content: 'Element Téléchargé avec succès.',
                        img: '{{asset('images/success-notif.jpg')}}',
                        cls: 'success1'
                    });
                }
            });
        });

        //When user has validate media
        $('.validation').on('click', function (e) {
            e.preventDefault();
            let getDocumentId = $('.validate_file_access').attr('href');
            let validation = $('.validation').attr('title');
            let fullLoading =  $('#cover-spin');

            $.ajax({
                url: "{{ route('admin.workflow-management.validateDocument') }}",
                method: 'POST',
                data: {
                    id: getDocumentId,
                    validationType: validation
                },
                dataType: 'json',
                beforeSend: function (){
                    fullLoading.show();
                },
                success: function (data) {
                    //alert(data.title);
                    /*window.location.href = "{{--{{ route('show-all-prescription')}}--}}";*/
                    //location.reload();

                    if(data.error !== ""){
                        $('body').notif({
                            title: 'Une Erreur est survenue',
                            content: data.error,
                            img: '{{asset('images/error-notif.png')}}',
                            cls: 'error1'
                        });
                    }

                    if(data.success !== ""){
                        $('body').notif({
                            title: 'Opération Réussie',
                            content: data.success,
                            img: '{{asset('images/success-notif.jpg')}}',
                            cls: 'success1'
                        });

                        setTimeout(function(){
                            window.location.reload();
                        }, 5000);
                    }
                },
                error: function () {
                    //alert("Error founded where user display document");
                    $('body').notif({
                        title: 'Une Erreur est survenue',
                        content: "Une erreur a été trouvé pendant le processus",
                        img: '{{asset('images/error-notif.png')}}',
                        cls: 'error1'
                    });
                    console.log('Error founded where user display document');
                },
                complete: function (){
                    fullLoading.hide();
                },
            });
        });

        $('.validation_signature').on('click', function (e) {
            e.preventDefault();
            let getDocumentId = $('.validate_file_access').attr('href');
            let validationSignature = $('.validation_signature').attr('title');

            $.ajax({
                url: "{{ route('admin.workflow-management.validateDocument') }}",
                method: 'POST',
                data: {
                    id: getDocumentId,
                    validationType: validationSignature
                },
                dataType: 'json',
                success: function (data) {
                    if(data.error !== ""){
                        $('body').notif({
                            title: 'Une Erreur est survenue',
                            content: data.error,
                            img: '{{asset('images/error-notif.png')}}',
                            cls: 'error1'
                        });
                    }

                    if(data.success !== ""){
                        $('body').notif({
                            title: 'Opération Réussie',
                            content: data.success,
                            img: '{{asset('images/success-notif.jpg')}}',
                            cls: 'success1'
                        });
                        location.reload();
                    }
                },
                error: function () {
                    $('body').notif({
                        title: 'Une Erreur est survenue',
                        content: "Une erreur a été trouvé pendant le processus",
                        img: '{{asset('images/error-notif.png')}}',
                        cls: 'error1'
                    });
                    console.log('Error founded where user display document');
                }
            });
        });

        $('.validation_paraphe').on('click', function (e) {
            e.preventDefault();
            let getDocumentId = $('.validate_file_access').attr('href');
            let validation_paraphe = $('.validation_paraphe').attr('title');

            $.ajax({
                url: "{{ route('admin.workflow-management.validateDocument') }}",
                method: 'POST',
                data: {
                    id: getDocumentId,
                    validationType: validation_paraphe
                },
                dataType: 'json',
                success: function (data) {
                    if(data.error !== ""){
                        $('body').notif({
                            title: 'Une Erreur est survenue',
                            content: data.error,
                            img: '{{asset('images/error-notif.png')}}',
                            cls: 'error1'
                        });
                    }

                    if(data.success !== ""){
                        $('body').notif({
                            title: 'Opération Réussie',
                            content: data.success,
                            img: '{{asset('images/success-notif.jpg')}}',
                            cls: 'success1'
                        });
                        location.reload();
                    }
                },
                error: function () {
                    $('body').notif({
                        title: 'Une Erreur est survenue',
                        content: "Une erreur a été trouvé pendant le processus",
                        img: '{{asset('images/error-notif.png')}}',
                        cls: 'error1'
                    });
                    console.log('Error founded where user display document');
                }
            });
        });

        $('.rejected').on('click', function (e) {
            e.preventDefault();
            let getDocumentId = $('.validate_file_access').attr('href');
            let rejected = $('.rejected').attr('title');

            $.ajax({
                url: "{{ route('admin.workflow-management.validateDocument') }}",
                method: 'POST',
                data: {
                    id: getDocumentId,
                    validationType: rejected
                },
                dataType: 'json',
                success: function (data) {

                    if(data.error !== ""){
                        $('body').notif({
                            title: 'Une Erreur est survenue',
                            content: data.error,
                            img: '{{asset('images/error-notif.png')}}',
                            cls: 'error1'
                        });
                    }

                    if(data.success !== ""){
                        $('body').notif({
                            title: 'Opération Réussie',
                            content: data.success,
                            img: '{{asset('images/success-notif.jpg')}}',
                            cls: 'success1'
                        });
                        location.reload();
                    }

                },
                error: function () {
                    $('body').notif({
                        title: 'Une Erreur est survenue',
                        content: "Une erreur a été trouvé pendant le processus",
                        img: '{{asset('images/error-notif.png')}}',
                        cls: 'error1'
                    });
                    console.log('Error founded where user display document');
                }
            });
        });

        $('.documentStorage').on('click', function (e) {
            let fullLoading = $('#cover-spin');
            e.preventDefault();
            let getId = $('.documentStorage').attr('href');
            let url = "{{ route('store-document') }}";
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    id: getId
                },
                dataType: 'json',
                beforeSend: function () {
                    fullLoading.show();
                },
                success: function (data) {
                    $('body').notif({
                        title: 'Opération Réussie',
                        content: data.status,
                        img: '{{asset('images/success-notif.jpg')}}',
                        cls: 'success1'
                    });

                    setTimeout(function(){
                        window.location.reload();
                    }, 5000);
                },
                error: function () {
                    fullLoading.hide();
                    $('body').notif({
                        title: 'Une Erreur est survenue',
                        content: "Link not found",
                        img: '{{asset('images/error-notif.png')}}',
                        cls: 'error1'
                    });
                    console.log('Link not found');
                },
                complete: function (){
                    fullLoading.hide();
                }
            });
        });
    });

    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [day, month, year].join('-');
    }

    function getUser(id = ''){
        let result;
        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url:"{{ route('users.user-find', '') }}"+"/"+id,
            dataType: 'json',
            async:false,
            success: function (data) {
                console.log(data);
                result = data.user.name;
            },error:function(){
                alert("une erreur a ete trouvé");
            },
        });
        return result;
    }


    $(function () {
        let dateType = $('.dateType');
        let filterActivity = $('#filter_activity');
        let detailDocumentUpdate = $('.table-full-width');
        let workflow = $('.workflow-v');
        let workflow_state = $('.workflow_state');

        //filter activity of user connected
        $('#filter_day, #filter_month, #filter_year').on('click', function (e) {

            let getParams;
            // Determine which filter was clicked based on its ID
            if ($(this).attr('id') === 'filter_day') {
                 getParams = $(this).attr('id');
                // Day filter clicked
                // Do something with the day value
            } else if ($(this).attr('id') === 'filter_month') {
                getParams = $(this).attr('id');
                // Month filter clicked
                // Do something with the month value
            } else if ($(this).attr('id') === 'filter_year') {
                getParams = $(this).attr('id');
                // Year filter clicked
                // Do something with the year value
            }
            e.preventDefault();
            dateType.empty();
            filterActivity.empty();
            $.ajax({
                url:"{{ route('filter-activity-by-date', '') }}"+"/"+getParams,
                method: 'GET',
                dataType: 'json',
                beforeSend: function (){
                    dateType.text("Encours...");
                    //fullLoading.show();
                },
                success: function (data) {
                    dateType.text(data.$date_type);
                    filterActivity.html(data.result);
                },
                error: function () {
                    console.log('Error founded where user display document');
                },
                complete: function (){
                },
            });
        });

        //filter activity of documents
        $('#filter_doc_day, #filter_doc_month, #filter_doc_year').on('click', function (e) {

            let getParams;
            if ($(this).attr('id') === 'filter_doc_day') {
                getParams = $(this).attr('id');
            } else if ($(this).attr('id') === 'filter_doc_month') {
                getParams = $(this).attr('id');
            } else if ($(this).attr('id') === 'filter_doc_year') {
                getParams = $(this).attr('id');
            }
            e.preventDefault();
            detailDocumentUpdate.empty();
            $.ajax({
                url:"{{ route('filter-document-by-date', '') }}"+"/"+getParams,
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if(data.result !== ""){
                        detailDocumentUpdate.html(data.result);
                    }
                },
                error: function () {
                    console.log('Error founded where user display document');
                },
            });
        });


        //filter activity of documents
        $('#filter_approved, #filter_pending, #filter_rejected').on('click', function (e) {

            let getParams;
            if ($(this).attr('id') === 'filter_approved') {
                getParams = $(this).attr('id');
            } else if ($(this).attr('id') === 'filter_pending') {
                getParams = $(this).attr('id');
            } else if ($(this).attr('id') === 'filter_rejected') {
                getParams = $(this).attr('id');
            }
            e.preventDefault();
            workflow.empty();
            workflow_state.empty();
            $.ajax({
                url:"{{ route('filter-workflow-by-status', '') }}"+"/"+getParams,
                method: 'GET',
                dataType: 'json',
                beforeSend: function (){
                    workflow_state.html('| ' + "{{ trans('global.loading') }}");
                },
                success: function (data) {
                    if(data.result !== ""){
                        workflow.html(data.result);
                    }

                    if(data.status==="filter_approved"){
                        workflow_state.html('| ' + "{{ trans('global.approved') }}");
                    } else if(data.status==="filter_pending"){
                        workflow_state.html('| ' + "{{ trans('global.waiting') }}");
                    } else{
                        workflow_state.html('| ' + "{{ trans('global.rejected') }}");
                    }
                },
                error: function () {
                    console.log('Error founded where user display document');
                },
            });
        });

        //update notifications after have opened
        /*$('#navbarDropdownNotifications').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            alert("dsfsdf");
            $.ajax({
                url:"",
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if(data.result !== ""){
                        //detailDocumentUpdate.html(data.result);
                    }
                },
                error: function () {
                    console.log('Error founded where user display document');
                },
            });
        });*/
    });

    //Recherche Ajax
    /*$(function () {
        $('#search_content').keyup(function () {
            search();
        });
        //fonction de verification du Nom en ajax
        function search() {
            let folderMediaBloc = $('#folder_media_bloc');
            let q =  $('#search_content').val();
            var retour = '';
            $.ajax({
                type: 'GET',
                url: "{{--{{ route('search', $folder) }}--}}"+"/" + q,
                dataType: 'json',
                success: function (data) {
                    if(data.resultat=='Aucun'){
                        $('#output_search').css({
                            'font-weight': 'bold',
                            'margin': 'initial',
                            'padding': 'initial',
                            'font-size': '65%'
                        }).html('Aucun résultat trouvé');
                        folderMediaBloc.empty();
                        /!*setTimeout(function () {
                            $('#output_search').hide();
                        }, 7000);*!/
                    } else {
                        if(data.compteur <= 1)
                            retour += data.compteur + ' résultat trouvé';
                        else
                            retour += data.compteur + ' résultats trouvés';

                        folderMediaBloc.html(data.resultat);
                        $('#output_search').css({
                            'font-weight': 'bold',
                            'margin': 'initial',
                            'padding': 'initial',
                            'font-size': '65%'
                        }).html(retour);
                    }
                }
            });
        }
    });*/

</script>

@livewireScripts
@stack('scripts')

{{--<script>
    import ExportPdf from '@ckeditor/ckeditor5-export-pdf/src/exportpdf';
    console.log(ExportPdf);

    ClassicEditor
        .create( document.querySelector( '#description' ), {
            plugins: [ ExportPdf,  ],
            toolbar: [
                'exportPdf', '|',
            ],
            exportPdf: {
                tokenUrl: 'https://example.com/cs-token-endpoint',
                stylesheets: [
                    './path/to/fonts.css',
                    'EDITOR_STYLES',
                    './path/to/style.css'
                ],
                fileName: 'my-file.pdf',
                converterOptions: {
                    format: 'A4',
                    margin_top: '20mm',
                    margin_bottom: '20mm',
                    margin_right: '12mm',
                    margin_left: '12mm',
                    page_orientation: 'portrait'
                }
            }
            /*cloudServices: {
                tokenUrl: 'https://example.com/cs-token-endpoint',
                uploadUrl: 'https://your-organization-id.cke-cs.com/easyimage/upload/'
            }*/
        })
        .catch( error => {
            console.error( error );
        } );
</script>--}}


<!--
	https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/ckbox.html
-->
{{--<script src="https://cdn.ckbox.io/CKBox/1.3.2/ckbox.js"></script>--}}
<!--
	The "super-build" of CKEditor 5 served via CDN contains a large set of plugins and multiple editor types.
	See https://ckeditor.com/docs/ckeditor5/latest/installation/getting-started/quick-start.html#running-a-full-featured-editor-from-cdn
-->

<!--
	Uncomment to load the Spanish translation
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/super-build/translations/es.js"></script>
-->
{{--<script>
    // This sample still does not showcase all CKEditor 5 features (!)
    // Visit https://ckeditor.com/docs/ckeditor5/latest/features/index.html to browse all the features.
    CKEDITOR.ClassicEditor.create( document.querySelector( '#editor' ), {
        // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
        toolbar: {
            items: [
                'ckbox', 'uploadImage', '|',
                'exportPDF','exportWord', '|',
                'comment', 'trackChanges', 'revisionHistory', '|',
                'findAndReplace', 'selectAll', '|',
                'bold', 'italic', 'strikethrough', 'underline', 'removeFormat', '|',
                'bulletedList', 'numberedList', 'todoList', '|',
                'outdent', 'indent', '|',
                'undo', 'redo',
                '-',
                'heading', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                'alignment', '|',
                'link', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                // Intentionally skipped buttons to keep the toolbar smaller, feel free to enable them:
                // 'code', 'subscript', 'superscript', 'textPartLanguage', '|',
                // ** To use source editing remember to disable real-time collaboration plugins **
                // 'sourceEditing'
            ],
            shouldNotGroupWhenFull: true
        },
        // Changing the language of the interface requires loading the language file using the <script> tag.
        // language: 'es',
        list: {
            properties: {
                styles: true,
                startIndex: true,
                reversed: true
            }
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
            ]
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
        fontFamily: {
            options: [
                'default',
                'Arial, Helvetica, sans-serif',
                'Courier New, Courier, monospace',
                'Georgia, serif',
                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif',
                'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif',
                'Verdana, Geneva, sans-serif'
            ],
            supportAllValues: true
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
        fontSize: {
            options: [ 10, 12, 14, 'default', 18, 20, 22 ],
            supportAllValues: true
        },
        // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
        // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
        // htmlSupport: {
        // 	allow: [
        // 		{
        // 			name: /.*/,
        // 			attributes: true,
        // 			classes: true,
        // 			styles: true
        // 		}
        // 	]
        // },
        // Be careful with enabling previews
        // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
        htmlEmbed: {
            showPreviews: true
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
        mention: {
            feeds: [
                {
                    marker: '@',
                    feed: [
                        '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                        '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                        '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                        '@sugar', '@sweet', '@topping', '@wafer'
                    ],
                    minimumCharacters: 1
                }
            ]
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
        placeholder: 'Welcome to CKEditor 5!',
        // Used by real-time collaboration
        cloudServices: {
            // Be careful - do not use the development token endpoint on production systems!
            tokenUrl: 'https://96306.cke-cs.com/token/dev/SPHxGoWfSaNh8dI3tN7ReXwEqGcqTQ5xn2Bb?limit=10',
            webSocketUrl: 'wss://96306.cke-cs.com/ws'
        },
        collaboration: {
            // Modify the channelId to simulate editing different documents
            // https://ckeditor.com/docs/ckeditor5/latest/features/collaboration/real-time-collaboration/real-time-collaboration-integration.html#the-channelid-configuration-property
            channelId: 'document-id-2'
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/collaboration/annotations/annotations-custom-configuration.html#sidebar-configuration
        sidebar: {
            container: document.querySelector( '#sidebar' )
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/collaboration/real-time-collaboration/users-in-real-time-collaboration.html#users-presence-list
        presenceList: {
            container: document.querySelector( '#presence-list-container' )
        },
        // Add configuration for the comments editor if the Comments plugin is added.
        // https://ckeditor.com/docs/ckeditor5/latest/features/collaboration/annotations/annotations-custom-configuration.html#comment-editor-configuration
        comments: {
            editorConfig: {
                extraPlugins: CKEDITOR.ClassicEditor.builtinPlugins.filter( plugin => {
                    // Use e.g. Ctrl+B in the comments editor to bold text.
                    return [ 'Bold', 'Italic', 'Underline', 'List', 'Autoformat', 'Mention' ].includes( plugin.pluginName );
                } ),
                // Combine mentions + Webhooks to notify users about new comments
                // https://ckeditor.com/docs/cs/latest/guides/webhooks/events.html
                mention: {
                    feeds: [
                        {
                            marker: '@',
                            feed: [
                                '@Baby Doe', '@Joe Doe', '@Jane Doe', '@Jane Roe', '@Richard Roe'
                            ],
                            minimumCharacters: 1
                        }
                    ]
                },
            }
        },
        // Do not include revision history configuration if you do not want to integrate it.
        // Remember to remove the 'revisionHistory' button from the toolbar in such a case.
        revisionHistory: {
            editorContainer: document.querySelector( '#editor-container' ),
            viewerContainer: document.querySelector( '#revision-viewer-container' ),
            viewerEditorElement: document.querySelector( '#revision-viewer-editor' ),
            viewerSidebarContainer: document.querySelector( '#revision-viewer-sidebar' ),
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/ckbox.html
        ckbox: {
            // Be careful - do not use the development token endpoint on production systems!
            tokenUrl: 'https://96306.cke-cs.com/token/dev/SPHxGoWfSaNh8dI3tN7ReXwEqGcqTQ5xn2Bb?limit=10'
        },
        // License key is required only by the Pagination plugin and non-realtime Comments/Track changes.
        licenseKey: 'vzY5hX/Gzzl7+ZL7oKYQBTmpVZN/z4yoW3tkERtzPZ2tZecyvp6c6MRPRA==',
        removePlugins: [
            // Before enabling Pagination plugin, make sure to provide proper configuration and add relevant buttons to the toolbar
            // https://ckeditor.com/docs/ckeditor5/latest/features/pagination/pagination.html
            'Pagination',
            // Intentionally disabled, file uploads are handled by CKBox
            'Base64UploadAdapter',
            // Intentionally disabled, file uploads are handled by CKBox
            'CKFinder',
            // Intentionally disabled, file uploads are handled by CKBox
            'EasyImage',
            // Requires additional license key
            'WProofreader',
            // Incompatible with real-time collaboration
            'SourceEditing',
            // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
            // from a local file system (file://) - load this site via HTTP server if you enable MathType
            'MathType'
            // If you would like to adjust enabled collaboration features:
            // 'RealTimeCollaborativeComments',
            // 'RealTimeCollaborativeTrackChanges',
            // 'RealTimeCollaborativeRevisionHistory',
            // 'PresenceList',
            // 'Comments',
            // 'TrackChanges',
            // 'TrackChangesData',
            // 'RevisionHistory',
        ]
    } )
        .then( editor => {
            window.editor = editor;

            // Example implementation to switch between different types of annotations according to the window size.
            // https://ckeditor.com/docs/ckeditor5/latest/features/collaboration/annotations/annotations-display-mode.html
            const annotationsUIs = editor.plugins.get( 'AnnotationsUIs' );
            const sidebarElement = document.querySelector( '.sidebar' );
            let currentWidth;

            function refreshDisplayMode() {
                // Check the window width to avoid the UI switching when the mobile keyboard shows up.
                if ( window.innerWidth === currentWidth ) {
                    return;
                }
                currentWidth = window.innerWidth;

                if ( currentWidth < 1000 ) {
                    sidebarElement.classList.remove( 'narrow' );
                    sidebarElement.classList.add( 'hidden' );
                    annotationsUIs.switchTo( 'inline' );
                }
                else if ( currentWidth < 1300 ) {
                    sidebarElement.classList.remove( 'hidden' );
                    sidebarElement.classList.add( 'narrow' );
                    annotationsUIs.switchTo( 'narrowSidebar' );
                }
                else {
                    sidebarElement.classList.remove( 'hidden', 'narrow' );
                    annotationsUIs.switchTo( 'wideSidebar' );
                }
            }

            editor.ui.view.listenTo( window, 'resize', refreshDisplayMode );
            refreshDisplayMode();

            return editor;
        } )
        .catch( error => {
            console.error( 'There was a problem initializing the editor.', error );
        } );
</script>--}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script>
    var route = "{{ route('autocomplete-search') }}";
    $('#search_content').typeahead({
        source: function (query, process) {
            return $.get(route, {
                query: query
            }, function (data) {
                return process(data);
            });
        }
    });
</script>

</body>
</html>
