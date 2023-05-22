@extends('layouts.admin')
@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.archive.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.archive.fields.document') }} {{ trans('global.list') }}
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
                            {{ trans('cruds.archive.fields.size') }}
                        </th>

                        <th>
                            {{ trans('cruds.archive.fields.created_at') }}
                        </th>

                        <th>
                            &nbsp;
                        </th>

                        <th>
                            {{ trans('cruds.archive.fields.document') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($medias as $key => $media)
                        <tr data-entry-id="{{ $key }}">
                            <td>

                            </td>
                            <td>
                                {{ $key ?? '' }}
                            </td>

                            <td>
                                {{ strtoupper(substr($media->file_name, 14)) ?? '' }}
                            </td>

                            <td>
                                {{ $media->size ?? '' }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($media->created_at)->diffForHumans() ?? '' }}
                            </td>

                            <td>
                                @can('archive_show')
                                    <a class="btn btn-xs btn-primary" href="{{$media->getUrl()}}" target="_blank">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('archive_store_access')
                                    <form method="POST" action="{{ route('admin.archive.store', ['id' => $media->id, 'user' => 'admin']) }}"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                          style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline-warning">{{ trans('global.archive_document') }}</button>
                                    </form>
                                @endcan

                            </td>

                            <td>
                                <embed src="{{ $media->getUrl() }}" frameborder="1">
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
            @can('archive_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.archive.destroy', 1) }}",
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
