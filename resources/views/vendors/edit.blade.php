<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>Edit Vendor - {{$vendor->vendor_name}}</title>
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
            <li><a href="{{url('vendors')}}">Vendors</a></li>
            <li class="active">Edit vendor</li>
        </ol>

	    @include('includes.error_div')
	    @include('includes.success_div')

        {!! Form::open(['url' => url(sprintf("/vendors/%d", $vendor->id)), 'method' => 'put','class'=>'form-horizontal','role'=>'form']) !!}
        <div class = "form-group">
            {!!Form::label('vendor_name','vendor_name :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('vendor_name', $vendor->vendor_name, ['id' => 'vendor_name','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('email','Email :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::email('email', null, ['id' => 'email', 'placeholder' => 'Insertion will update the email','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('zip_code','Zip Code :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('zip_code', $vendor->zip_code, ['id' => 'zip_code','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('state','State :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('state', $vendor->state, ['id' => 'state','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('phone_number','Phone number :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('phone_number', $vendor->phone_number, ['id' => 'state','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            <div class = "col-xs-offset-4 col-xs-5">
                {!! Form::submit('Update this vendor',['class'=>'btn btn-primary btn-block']) !!}
            </div>
        </div>
        {!! Form::close() !!}

        {!! Form::open(['url' => url(sprintf('/vendors/%d', $vendor->id)), 'method' => 'delete', 'id' => 'delete-vendor-form', 'class'=>'form-horizontal','role'=>'form']) !!}
        <div class = "form-group">
            <div class = "col-xs-offset-4 col-xs-5">
                {!! Form::submit('Delete vendor', ['class'=> 'btn btn-primary btn-block btn-danger', 'id' => 'delete-vendor-btn']) !!}
            </div>
        </div>
        {!! Form::close() !!}

    </div>
    <script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script type = "text/javascript">
        var message = {
            delete: 'Are you sure you want to delete?',
        };
        $("input#delete-vendor-btn").on('click', function (event)
        {
            event.preventDefault();
            var action = confirm(message.delete);
            if ( action ) {
                var form = $("form#delete-vendor-form");
                form.submit();
            }
        });
    </script>
</body>
</html>