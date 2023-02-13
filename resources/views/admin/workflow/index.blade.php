@extends('layouts.admin')
@section('content')
    @can('workflow_management_access_create')
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
                            {{ trans('cruds.workflow_management.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.workflow_management.sender') }}
                        </th>
                        <th>
                            {{ trans('cruds.workflow_management.receiver') }}
                        </th>
                        <th>
                            {{ trans('cruds.workflow_management.operation_state') }}
                        </th>
                        <th>
                            {{ trans('cruds.workflow_management.term') }}
                        </th>
                        <th>
                            {{ trans('cruds.workflow_management.priority') }}
                        </th>
                        <th>
                            {{ trans('cruds.workflow_management.visibility') }}
                        </th>
                        <th>
                            {{ trans('cruds.workflow_management.view_file') }}
                        </th>
                        <th>
                            {{ trans('cruds.workflow_management.message') }}
                        </th>
                        <th>
                            {{ trans('cruds.workflow_management.workflow_users') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($allMedia as $key => $media)

                        @foreach($media->operations as $key => $operation)
                            <tr data-entry-id="{{ $media->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $media->id ?? '' }}
                                </td>

                                <td>
                                    <span class="badge badge-success">{{ \App\Models\User::findOrFail($operation->user_id_sender)->name ?? '' }}</span>
                                </td>

                                <td>
                                    <span class="badge badge-primary">{{ \App\Models\User::findOrFail($operation->user_id_receiver)->name ?? '' }}</span>
                                </td>

                                <td>
                                    {{ $operation->operation_state ?? '' }}
                                </td>

                                <td>
                                    {{ now()->diffForHumans($operation->deadline) ?? '' }}
                                </td>
                                <td>
                                    {{ $operation->priority ?? '' }}
                                </td>
                                <td>
                                    {{ $media->status ?? '' }}
                                </td>
                                <td>
                                    <iframe src="{{ $media->getUrl() }}" frameborder="1"></iframe>
                                </td>
                                <td>
                                    {{ $operation->message ?? '' }}
                                </td>
                                <td>
                                    @foreach(json_decode($media->step_workflow)->step_workflow as $key => $id)
                                            <span class="badge badge-info"> {{ \App\Models\User::findOrFail($id)->name ?? '' }} </span>
                                    @endforeach
                                </td>

                                <td>
                                    @can('workflow_management_access_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.workflow-management.show', $media->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('workflow_management_access_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.folders_access.edit', $media->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('workflow_management_access_delete')
                                        <form action="{{ route('admin.workflow-management.destroy', $operation->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                        </form>
                                    @endcan
                                </td>

                            </tr>
                        @endforeach

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
            @can('workflow_management_access_delete')
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