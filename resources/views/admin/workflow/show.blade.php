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
                @foreach($media->operations as $key => $operation)
                    <table class="table table-bordered table-striped">
                        <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.fields.id') }}
                            </th>
                            <td>
                                {{ $media->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.sender') }}
                            </th>
                            <td>
                                <span class="badge badge-info">{{ \App\Models\User::findOrFail($operation->user_id_sender)->name ?? '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.receiver') }}
                            </th>
                            <td>
                                <span class="badge badge-info">{{ \App\Models\User::findOrFail($operation->user_id_receiver)->name ?? '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.operation_state') }}
                            </th>
                            <td>
                                {{ $operation->operation_state }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.term') }}
                            </th>
                            <td>
                                {{ now()->diffForHumans($operation->deadline) ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.priority') }}
                            </th>
                            <td>
                                {{ $operation->priority ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.visibility') }}
                            </th>
                            <td>
                                {{ $operation->status ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.view_file') }}
                            </th>
                            <td>
                                <iframe src="{{ $media->getUrl() }}" frameborder="1"></iframe>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.message') }}
                            </th>
                            <td>
                                {{ $operation->message ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.workflow_management.workflow_users') }}
                            </th>
                            <td>
                                @foreach(json_decode($media->step_workflow) as $key => $id)
                                    <span class="badge badge-info"> {{ \App\Models\User::findOrFail($id->user_id)->name ?? '' }} </span>
                                @endforeach
                            </td>
                        </tr>
                        </tbody>
                    </table>
                @endforeach

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.workflow-management.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection
