<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>Create station</title>
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
    @include('includes.header_menu')
    <div class = "container">
        <ol class="breadcrumb">
            <li><a href="{{url('/')}}">Home</a></li>
            <li><a href="{{url('stations')}}">Stations</a></li>
            <li class="active">Create station</li>
        </ol>
        @if($errors->any())
            <div class = "col-xs-12">
                <div class = "alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {!! Form::open(['url' => url('/stations'), 'method' => 'post']) !!}
        <fieldset>
            <legend>Create new station</legend>
            <div class = "form-group col-xs-12">
                {!! Form::label('station_name', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
                <div class = "col-xs-4">
                    {!! Form::text('station_name', null, ['id' => 'station_name', 'class' => "form-control", 'placeholder' => "Enter station name"]) !!}
                </div>
            </div>
            <div class = "form-group col-xs-12">
                {!! Form::label('station_description', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
                <div class = "col-xs-4">
                    {!! Form::text('station_description', null, ['id' => 'station_description', 'class' => "form-control", 'placeholder' => "Enter station description"]) !!}
                </div>
            </div>
            <div class = "col-xs-12 apply-margin-top-bottom">
                <div class = "col-xs-offset-2 col-xs-4">
                    {!! Form::submit('Create station', ['class' => 'btn btn-primary btn-block']) !!}
                </div>
            </div>
        </fieldset>
        {!! Form::close() !!}
    </div>
</body>
</html>