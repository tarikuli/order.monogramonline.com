<!doctype html>
<!--suppress JSUnresolvedVariable -->
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Bulk batch update</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "/assets/css/bootstrap-horizon.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<style>
		.parent-selector {
			width: 200px;
			overflow: auto;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">Bulk Order Station Change</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')

		{!! Form::open(['url' => url('items/bulk_item_change'), 'method' => 'post', 'class'=>'form-horizontal', 'role'=>'form']) !!}
		<div class = "form-group">
			{!!Form::label('item_id','Item#',['class'=>'control-label col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::textarea('item_id', null, ['id' => 'item_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			<div class = "col-md-offset-2 col-md-2">
				{!! Form::submit('Change station',['class'=>'btn btn-primary']) !!}
			</div>
		</div>
		{!! Form::close() !!}
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

</body>
</html>