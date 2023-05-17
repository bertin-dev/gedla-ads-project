@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.workflow_management.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.workflow-management.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                        <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.fields.id') }}
                            </th>
                            <td>
                                {{ $getMediaAndUser->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.fields.users') }}
                            </th>
                            <td>
                                <span class="badge badge-info">{{ \App\Models\User::findOrFail($getMediaAndUser->user_id)->name ?? '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.operation_state') }}
                            </th>
                            <td>
                                {{ $getMediaAndUser->statut==0 ? trans('global.waiting') : trans('global.validate') }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.term') }}
                            </th>
                            <td>
                                {{ now()->diffForHumans($getMediaAndUser->deadline) ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.priority') }}
                            </th>
                            <td>
                                {{ $getMediaAndUser->media->priority ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.visibility') }}
                            </th>
                            <td>
                                {{ $getMediaAndUser->media->visibility ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.view_file') }}
                            </th>
                            <td>
                                <iframe src="{{ $getMediaAndUser->media->getUrl() }}" frameborder="1"></iframe>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.workflow_users') }}
                            </th>
                            <td>
                                <span class="badge badge-info"> {{ \App\Models\User::findOrFail($getMediaAndUser->user_id)->name ?? '' }} </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.workflow-management.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection
