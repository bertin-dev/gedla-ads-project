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
                            {{ trans('cruds.workflow_management.view_file') }}
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
                            {{ trans('cruds.workflow_management.workflow_users') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($allMediaWithValidationStep as $key => $media)

                        @foreach($media->validationSteps as $key => $validationStep)
                            <tr data-entry-id="{{ $media->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $media->id ?? '' }}
                                </td>

                                <td>
                                    {{ $media->file_name ?? '' }}
                                </td>

                                <td>
                                    <span class="badge badge-success">{{ \App\Models\User::findOrFail($validationStep->user_id)->name ?? '' }}</span>
                                </td>

                                <td>
                                    {{--<span class="badge badge-primary">{{ \App\Models\User::findOrFail($operation->user_id_receiver)->name ?? '' }}</span>--}}
                                </td>

                                <td>
                                    {{ $validationStep->statut==0 ? trans('global.waiting') : trans('global.validate') }}
                                </td>

                                <td>
                                    {{ now()->diffForHumans($validationStep->deadline) ?? '' }}
                                </td>
                                <td>
                                    {{ $media->priority ?? '' }}
                                </td>
                                <td>
                                    {{ $media->visibility ?? '' }}
                                </td>
                                <td>
                                    <embed src="{{ $media->getUrl() }}" frameborder="1">
                                </td>
                                <td>
                                    @foreach($media->validationSteps as $key => $jsonItem)
                                            <span class="badge {{ $jsonItem->statut==1 ? "badge-success" : ($jsonItem->state==0 ? "badge-info" : "badge-warning")  }}">
                                                {{ \App\Models\User::findOrFail($jsonItem->user_id)->name ?? '' }}
                                            </span>
                                    @endforeach
                                </td>

                                <td>
                                    @can('workflow_management_access_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.workflow-management.show', $validationStep->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    {{--@can('workflow_management_access_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.workflow-management.edit', $validationStep->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan--}}

                                    @can('workflow_management_access_delete')
                                        <form action="{{ route('admin.workflow-management.destroy', $validationStep->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
