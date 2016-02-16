<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>Create User</title>
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
</head>
<body>
    @include('includes.header_menu')
    <div class = "container">
        <ol class="breadcrumb">
            <li><a href="{{url('/')}}">Home</a></li>
            <li><a href="{{url('users')}}">Users</a></li>
            <li class="active">Create user</li>
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


        {!! Form::open(['url' => url('/users'), 'method' => 'post','class'=>'form-horizontal','role'=>'form']) !!}

        <div class = 'form-group'>
            {!!Form::label('username','Username :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = 'col-xs-5'>
                {!! Form::text('username', null, ['id' => 'username','class' => 'form-control']) !!}
            </div>
        </div>
        <div class = 'form-group'>
            {!!Form::label('email','Email :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::email('email', null, ['id' => 'email','class' => 'form-control']) !!}
            </div>
        </div>
        <div class = 'form-group'>
            {!!Form::label('password','Password :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::password('password', ['id' => 'password','class' => 'form-control']) !!}
            </div>
        </div>
        <div class = 'form-group'>
            {!!Form::label('vendor_id','Role :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::select('role', $roles, null, ['id' => 'vendor_id','class' => 'form-control']) !!}
            </div>
        </div>
        <div class = 'form-group'>
            {!!Form::label('vendor_id','Vendor ID :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('vendor_id', null, ['id' => 'vendor_id','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = 'form-group'>
            {!!Form::label('zip_code','Zip Code :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('zip_code', null, ['id' => 'zip_code','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = 'form-group'>
            {!!Form::label('state','State :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('state', null, ['id' => 'state','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = 'form-group'>
            <div class = "col-xs-offset-4 col-xs-5">
                {!! Form::submit('Create User',['class'=>'btn btn-primary btn-block']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>
    <script src = "//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>