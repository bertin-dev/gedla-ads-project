@extends('layouts.front')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Create new folder</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('folders.store') }}">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ request('parent_id') }}" />
                            <input type="hidden" name="project_id" value="{{ request('project_id') }}" />

                            <div class="form-group">
                                <label for="name" class="required">{{trans('global.name')}}</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="desc">{{trans('global.description')}}</label>
                                <textarea name="desc" id="desc" cols="30" rows="10" class="form-control @error('desc') is-invalid @enderror">{{ old('desc') }}</textarea>
                                @error('desc')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="required" for="user_access">{{ trans('cruds.folder_access.fields.user_access') }}</label>
                                <div style="padding-bottom: 4px">
                                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                                </div>
                                <select class="form-control select2 {{ $errors->has('user_access') ? 'is-invalid' : '' }}" name="user_access[]" id="user_access" multiple required>
                                    @foreach($users as $id => $user)
                                        <option value="{{ $id }}" {{ (in_array($id, old('user_access', [])) || $id==auth()->id()) ? 'selected' : '' }}>{{ $user }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('user_access'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('user_access') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.folder_access.fields.user_access_helper') }}</span>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
