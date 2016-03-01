<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>Create Vendor</title>
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
</head>
<body>
    @include('includes.header_menu')
    <div class = "container">
        <ol class="breadcrumb">
            <li><a href="{{url('/')}}">Home</a></li>
            <li><a href="{{url('vendors')}}">Vendors</a></li>
            <li class="active">Create vendor</li>
        </ol>

	    @include('includes.error_div')


        {!! Form::open(['url' => url('/vendors'), 'method' => 'post','class'=>'form-horizontal','role'=>'form']) !!}

        <div class = 'form-group'>
            {!!Form::label('vendor_name','Vendor name :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = 'col-xs-5'>
                {!! Form::text('vendor_name', null, ['id' => 'vendor_name','class' => 'form-control']) !!}
            </div>
        </div>
        <div class = 'form-group'>
            {!!Form::label('email','Email :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::email('email', null, ['id' => 'email','class' => 'form-control']) !!}
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
            {!!Form::label('phone_number','Phone number :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('phone_number', null, ['id' => 'phone_number','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = 'form-group'>
            {!!Form::label('country','Country: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('country', null, ['id' => 'country','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = 'form-group'>
            <div class = "col-xs-offset-4 col-xs-5">
                {!! Form::submit('Create vendor',['class'=>'btn btn-primary btn-block']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>
    <script src = "//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>