@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.folder.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.folders_access.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.folder.fields.id') }}
                        </th>
                        <td>
                            {{ $folder->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.folder.fields.name') }}
                        </th>
                        <td>
                            {{ $folder->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.folder.fields.project') }}
                        </th>
                        <td>
                            {{ $folder->project->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.folder_access.fields.folder_access') }}
                        </th>
                        <td>
                            @foreach($folder->multiUsers as $key => $user)
                                <span class="badge badge-info">{{ $user->name ?? '' }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.folder.fields.created_by') }}
                        </th>
                        <td>
                            {{ $folder->userCreatedFolderBy->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.folder.fields.updated_by') }}
                        </th>
                        <td>
                            {{ $folder->userUpdatedFolderBy->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.folders_access.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
