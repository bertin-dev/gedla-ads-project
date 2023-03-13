@extends('layouts.admin')
@section('content')
    @can('user_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.users.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
                </a>


                <a class="btn btn-success" href="{{ route('admin.users.display-view-importation') }}">
                    {{ trans('global.import') }} {{ trans('cruds.user.title_singular') }}
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
                            {{ trans('cruds.archive.fields.document') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.archive_text') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.file_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.size') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.state') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.archive') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.visibility') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.version') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.created_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.created_at') }}
                        </th>
                        <th>
                            {{ trans('cruds.archive.fields.updated_at') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($medias as $key => $media)
                        <tr data-entry-id="{{ $media->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $media->id ?? '' }}
                            </td>
                            <td>
                                <iframe src="{{ $media->getUrl() ?? '' }}" frameborder="1"></iframe>
                            </td>
                            <td>
                                {{ $media->file_name ?? '' }}
                            </td>
                            <td>
                                {{ $media->mime_type ?? '' }}
                            </td>
                            <td>
                                {{ $media->size ?? '' }} KO
                            </td>
                            <td>
                                {{ $media->state ?? '' }}
                            </td>
                            <td>
                                {{ ($media->archived ? trans('global.yes') : trans('global.no')) ?? '' }}
                            </td>
                            <td>
                                {{ $media->status ?? '' }}
                            </td>
                            <td>
                                {{ $media->version ?? '' }}
                            </td>
                            <td>
                                {{ $media->created_by ?? '' }}
                                {{--@foreach($media->roles as $key => $item)
                                    <span class="badge badge-info">{{ $item->title }}</span>
                                @endforeach--}}
                            </td>
                            <td>
                                {{ $media->created_at ?? '' }}
                            </td>
                            <td>
                                {{ $media->updated_at ?? '' }}
                            </td>
                            <td>
                                @can('archive_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.archive.show', $media->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                {{--@can('user_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.users.edit', $media->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan--}}

                                @can('archive_delete')
                                    <form action="{{ route('admin.users.destroy', $media->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
            @can('user_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.users.massDestroy') }}",
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
            let table = $('.datatable-User:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection
