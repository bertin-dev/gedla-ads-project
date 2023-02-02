@extends('layouts.front')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Upload File</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                            <form method="post" action="{{route('post-upload-file')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="description"><strong>{{trans('global.description')}} :</strong></label>
                                    <textarea id="description" class="form-control" name="description">
                                        {!!  $fileRead ?? '' !!}
                                    </textarea>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-success btn-sm">{{trans('global.save')}}</button>
                                </div>
                            </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
