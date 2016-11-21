<!doctype html>
<!--suppress JSUnresolvedVariable -->
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Move to Shipping Station by Order Number</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "/assets/css/bootstrap-horizon.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<style type="text/css">
	.alert-danger{
			font-size: 34px;
	}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">Move to Shipping Station by Order Number</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')

		
		@if(count($errors->all()) > 0)
				<audio controls autoplay="autoplay" >
			  <source src="/assets/sound/beeperror.wav" type="audio/ogg">
				Your browser does not support the audio element.
			</audio>
		@endif
        
		{!! Form::open(['url' => url('stations/itemshippingstationchange'), 'method' => 'post', 'class'=>'form-horizontal', 'role'=>'form']) !!}
		<div class = "form-group">
			{!!Form::label('unique_order_id','Shipping Order#',['class'=>'control-label col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('unique_order_id', null, ['id' => 'unique_order_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			<div class = "col-md-offset-2 col-md-2">
				{!! Form::submit('Move To Shipping',['class'=>'btn btn-primary']) !!}
			</div>
			
			<div class = "col-md-offset-2 col-md-2">
				{!! Form::button('Clear',['id' => 'clear', 'class'=>'btn btn-danger']) !!}
			</div>
		</div>
		{!! Form::close() !!}
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

	<script type = "text/javascript">
			
		$("#unique_order_id").focus();

		$("#clear").on('click', function (event)
		{
			$("#unique_order_id").val("");
			$("#unique_order_id").focus();
		});
		
	</script>
</body>
</html>