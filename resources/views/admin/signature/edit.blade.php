@extends('layouts.admin')
@section('content')

    <style>
        .kbw-signature { width: 100%; height: 200px;}
        #sig canvas{
            width: 100% !important;
            height: auto;
        }
    </style>

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.signature.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.signature.update", [$media->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label class="" for="signature64">{{ trans('cruds.signature.title') }} :</label>
                    <br/>
                    <div id="sig" ></div>
                    <br/>
                    <button id="clear" class="btn btn-danger btn-sm">{{ trans('global.clear_signature') }}</button>
                    <textarea id="signature64" name="signed" style="display: none">
                         {{ old('signed', $media->getUrl()) }}
                    </textarea>
                    @if($errors->has('signed'))
                        <div class="invalid-feedback">
                            {{ $errors->first('signed') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.signature.fields.signature_text_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="user_signature">{{ trans('cruds.signature.fields.owner') }}</label>
                    <select class="form-control select2 {{ $errors->has('user_signature') ? 'is-invalid' : '' }}" name="user_signature" id="user_signature" required>
                        @foreach($users as $id => $user)
                            <option value="{{ $id }}" {{ (old('user_signature') ? old('user_signature') :  $media->signedBy->id ?? '') == $id ? 'selected' : '' }}>{{ $user }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('user_signature'))
                        <div class="invalid-feedback">
                            {{ $errors->first('user_signature') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.signature.fields.signature_text_helper') }}</span>
                </div>

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>



@endsection

@section('scripts')
    <script>
        var sig = $('#sig').signature({syncField: '#signature64', syncFormat: 'PNG'});
        $('#clear').click(function(e) {
            e.preventDefault();
            sig.signature('clear');
            $("#signature64").val('');
        });
    </script>
@endsection
