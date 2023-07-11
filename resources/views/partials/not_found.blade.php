@if($page)
    <style>
        container1 {
            background-color: #000;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }

        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: #000;
        }

        .background:before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 400px;
            height: 400px;
            margin-top: -200px;
            margin-left: -200px;
            border-radius: 50%;
            box-shadow: 0 0 50px 50px #fff;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: #fff;
            padding-top: 50px;
        }

    </style>
    <div class="container1">
        <div class="background"></div>
        <div class="content">
            <h1>Page vide</h1>
            <p>Cette page ne contient aucun élément.</p>
        </div>
    </div>
@else
    <div class="container">
        <div class="alert alert-danger">
            <h1>{{ trans('global.not_found') }}</h1>
            <p>{{ trans('global.page_not_available') }}</p>
        </div>
    </div>
@endif



