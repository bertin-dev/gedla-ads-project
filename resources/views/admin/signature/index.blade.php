@extends('layouts.admin')
@section('content')

        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                @can('signature_access_create')
                    <a class="btn btn-success" href="{{ route('admin.signature.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.signature.title') }}
                    </a>
                @endcan

                    @can('signature_access_create')
                        <a class="btn btn-success" href="{{ route('signature.upload') }}">
                            {{ trans('global.import') }} {{ trans('cruds.signature.title') }} {{ trans('global.or') }} {{ trans('cruds.parapheur.title') }}
                        </a>
                    @endcan
            </div>
        </div>

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.signature.title') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Folder">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.signature.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.signature.fields.signature_text') }}
                        </th>
                        <th>
                            {{ trans('cruds.parapheur.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.signature.fields.owner') }}
                        </th>
                        <th>
                            {{ trans('cruds.signature.fields.created_by') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($allMediaWithSignature as $key => $mediaSigned)

                        <tr data-entry-id="{{ $mediaSigned->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $mediaSigned->id ?? '' }}
                            </td>
                            <td>
                                @if($mediaSigned->collection_name=="signature")
                                    <img class="img-thumbnail" src="{{ $mediaSigned->getUrl() }}"
                                         alt="{{$mediaSigned->name}}" title="{{$mediaSigned->name}}">
                                @endif
                            </td>
                            <td>
                                @if($mediaSigned->collection_name=="paraphe")
                                    <img class="img-thumbnail" src="{{ $mediaSigned->getUrl() }}"
                                         alt="{{$mediaSigned->name}}" title="{{$mediaSigned->name}}">
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $mediaSigned->signedBy->name }}</span>
                            </td>

                            <td>
                                <span class="badge badge-info">{{ $mediaSigned->createdBy->name }}</span>
                            </td>

                            <td>
                                @can('signature_access_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.signature.show', $mediaSigned->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('signature_access_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.signature.edit', $mediaSigned->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('signature_access_delete')
                                    <form action="{{ route('admin.signature.destroy', $mediaSigned->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
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
            @can('signature_access_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.signature.massDestroy') }}",
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
