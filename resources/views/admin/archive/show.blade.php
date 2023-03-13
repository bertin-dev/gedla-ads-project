@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.archive.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.archive.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.id') }}
                        </th>
                        <td>
                            {{ $media->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.archive_text') }}
                        </th>
                        <td>
                            {{ $media->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.file_type') }}
                        </th>
                        <td>
                            {{ $media->mime_type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.size') }}
                        </th>
                        <td>
                            {{ $media->size }} ko
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.state') }}
                        </th>
                        <td>
                            {{ $media->state }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.archive') }}
                        </th>
                        <td>
                            {{ ($media->archived ? trans('global.yes') : trans('global.no')) ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.visibility') }}
                        </th>
                        <td>
                            {{ $media->status ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.version') }}
                        </th>
                        <td>
                            {{ $media->version }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.archive.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection
