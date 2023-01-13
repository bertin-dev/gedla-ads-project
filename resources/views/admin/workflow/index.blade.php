@extends('layouts.admin')
@section('content')
    @can('folder_access_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.workflow-management.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.workflow_management.new_workflow') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.workflow_management.new_workflow') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Folder">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.folder_access.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.folder_access.fields.folder_access') }}
                        </th>
                        <th>
                            {{ trans('cruds.folder_access.fields.user_access') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--@foreach($folderUser as $key => $folder)

                        <tr data-entry-id="{{ $folder->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $folder->id ?? '' }}
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $folder->name }}</span>
                            </td>
                            <td>
                                @foreach($folder->multiUsers as $key => $item)
                                    <span class="badge badge-info">{{ $item->name }}</span>
                                @endforeach
                            </td>

                            <td>
                                @can('folder_access_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.folders_access.show', $folder->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('folder_access_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.folders_access.edit', $folder->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                --}}{{--@can('folder_access_delete')
                                    <form action="{{ route('admin.folders_access.destroy', $folder->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan--}}{{--
                            </td>

                        </tr>
                    @endforeach--}}
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
            @can('folder_access_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.folders_access.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
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
                            data: { ids: ids, _method: 'DELETE' }})
                            .done(function () { location.reload() })
                    }
                }
            }
            dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [[ 1, 'desc' ]],
                pageLength: 100,
            });
            let table = $('.datatable-Folder:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection
