@extends('layouts.admin')
@section('content')
    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Dashboard</div>
                        <div class="card-body">
                            <h1>{{ $chart1->options['chart_title'] }}</h1>
                            {!! $chart1->renderHtml() !!}
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <!--**********************************
        Content body end
    ***********************************-->
@endsection




@section('javascript')
    {!! $chart1->renderChartJsLibrary() !!}
    {!! $chart1->renderJs() !!}
@endsection

@section('scripts')
@parent
@endsection
