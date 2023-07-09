@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.signature.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.signature.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.signature.fields.id') }}
                        </th>
                        <td>
                            {{ $media->id ?? ''}}
                        </td>
                    </tr>
                    @if($media->collection_name=="signature")
                        <tr>
                            <th>
                                {{ trans('cruds.signature.fields.signature_text') }}
                            </th>
                            <td>
                                <img class="img-thumbnail" src="{{ $media->getUrl() }}" alt="{{$media->name}}"
                                     title="{{$media->name}}">
                            </td>
                        </tr>
                    @endif
                    @if($media->collection_name=="paraphe")
                        <tr>
                            <th>
                                {{ trans('cruds.parapheur.title') }}
                            </th>
                            <td>
                                <img class="img-thumbnail" src="{{ $media->getUrl() }}" alt="{{$media->name}}"
                                     title="{{$media->name}}">
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th>
                            {{ trans('cruds.signature.fields.owner') }}
                        </th>
                        <td>
                            {{ $media->signedBy->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.signature.fields.created_by') }}
                        </th>
                        <td>
                            {{ $media->createdBy->name ?? '' }}
                        </td>
                    </tr>


                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.signature.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection
