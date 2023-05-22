@extends('layouts.admin')
@section('content')
    @can('archive_add_access')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.archive.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.archive.title') }}
                </a>
            </div>
        </div>
    @endcan

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.archive.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{!! \Session::get('success') !!}</li>
                    </ul>
                </div>
            @endif
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-User">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.archive_text') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.document') }}
                        </th>

                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($gedFiles as $key => $media)
                        <tr data-entry-id="{{ $key }}">
                            <td>

                            </td>
                            <td>
                                {{ $key ?? '' }}
                            </td>

                            <td>
                                {{ $media ?? '' }}
                            </td>
                            <td>
                                <embed src="{{ asset('storage/archives/'.$media) }}" frameborder="1">
                            </td>
                            <td>
                                @can('archive_show')
                                    <a class="btn btn-xs btn-primary" href="{{ asset('storage/archives/'.$media) }}" target="_blank">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('unarchive_document_access')
                                        <form method="POST" action="{{ route('admin.archive.restore', ['file_name' => $media]) }}"
                                              onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                              style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-outline-warning">{{ trans('global.unarchive') }}</button>
                                        </form>
                                @endcan

                                {{--@can('archive_delete')
                                    <form action="#" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                          style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="media" value="{{$media}}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger"
                                               value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan--}}

                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



@endsection
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('user_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.users.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({selected: true}).nodes(), function (entry) {
                        return $(entry).data('entry-id')
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')

                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: {ids: ids, _method: 'DELETE'}
                        })
                            .done(function () {
                                location.reload()
                            })
                    }
                }
            }
            dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 100,
            });
            let table = $('.datatable-User:not(.ajaxTable)').DataTable({buttons: dtButtons})
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection
