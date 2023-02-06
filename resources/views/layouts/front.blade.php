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
{{--    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/bootstrap_4.1.3.css')}}">
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/@coreui/coreui@3.2/dist/css/coreui.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.0/css/perfect-scrollbar.min.css" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    @yield('styles')
</head>
<body class="c-app">

@include('partials.menu-user')

{{--<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a href="{{ route('projects.index') }}" class="nav-link">Projects</a>
                    </li>
                </ul>

                <ul class="navbar-nav ml-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
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

    <main class="py-4">
        @yield('content')
    </main>
</div>--}}



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

        <ul class="navbar-nav ml-auto">
            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
            @else
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>

    </header>

    <div class="c-body">
        <main class="c-main">

            {{--<div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard v1</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>--}}

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
{{--<script src="{{ asset('js/app.js') }}"></script>
<script src="{{asset('js/jquery.js')}}"></script>
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
<script src="{{asset('js/dropzone.min.js')}}"></script>--}}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
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

{{--<script src="//cdn.ckeditor.com/4.20.1/standard/ckeditor5.js"></script>
<script>
    $(document).ready(function() {
        $('.ckeditor').ckeditor();
    });

    /*CKEDITOR.replace('description', {
        filebrowserUploadUrl: "{{route('ckeditor.image-upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });*/
</script>--}}


<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(function () {
        $('.bd-example-modal-lg').on('show.bs.modal', function (e) {
            let id = $(e.relatedTarget).data('id');
            let url = $(e.relatedTarget).data('url');
            let name = $(e.relatedTarget).data('name');
            let size = $(e.relatedTarget).data('size');
            let version = $(e.relatedTarget).data('version');
            let itemType = $(e.relatedTarget).data('item_type');
            //let objects = $('#objectLink').data('name');
            $('.img_detail').attr({
                src: itemType,
                alt: name,
                title: name
            });
            $('.iframeFile').attr({
                src: url
            });
            $('.file-title').text('{{trans('global.file_details')}} : ' + name);
            $('.folder_id').text('{{ trans('global.folder') }} : ' + id + '');
            $('.folder_size').text('{{ trans('global.size') }} : ' + size + ' KO');
            $('.version').text('{{ trans('global.version') }} : ' + version);
            $('.myUrl').attr("href", url);
            $('.mediaDownload').attr("href", id);
            $('#media_id').attr("value", id);
            $('.document_id').attr("href", id);
            console.log(id);
            updateMediaTable(id);
        });


        $('.workflow_validate').click(function() {
            $('.workflow_form').toggle('slow');
        });


        //when user as seen Media, media tableupdate
        function updateMediaTable(id) {
            /*let url = "{{ route('admin.workflow-management.hasReadMedia', ":id") }}";
            url = url.replace(':id', id);*/
            $.ajax({
                url: "{{ route('admin.workflow-management.hasReadMedia') }}",
                method: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function (data) {
                    //alert(data.user[0]);
                    /*$('.menu').html(data.notification);

                    if (data.unseen_notification > 0) {
                        $('.count').html(data.unseen_notification);
                    }*/

                },
                error: function(){
                    alert("Error founded where user display document");
                    console.log('Error founded where user display document');
                }
            });
        }

    });

    $(function () {
        $('.mediaDownload').on('click', function (e) {
            e.preventDefault();
            let getId = $('.mediaDownload').attr('href');
            //alert(link);
            let url = "{{ route('parapheur.download') }}";
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
                    /*$('.success_download').css({
                    'display': 'block',
                    })*/
                    $('.success_download').text(data);
                },
                error: function (data) {
                    console.log('Link not found');
                }
            });
        });


        //When user has validate media
        $('.validate_file_access').on('click', function (e) {
            e.preventDefault();
            let getDocumentId = $('.validate_file_access').attr('href');

            $.ajax({
                url: "{{ route('admin.workflow-management.validateDocument') }}",
                method: 'POST',
                data: {
                    id: getDocumentId
                },
                dataType: 'json',
                success: function (data) {
                    alert(data.title);
                    /*window.location.href = "{{--{{ route('show-all-prescription')}}--}}";*/
                    location.reload();
                },
                error: function () {
                    alert("Error founded where user display document");
                    console.log('Error founded where user display document');
                }
            });
        });

    });

</script>


<script>
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
</script>

</body>
</html>
