<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Add child sku</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<style>
		td {
			width: 1px;
			white-space: nowrap;
		}

		td.description {
			white-space: pre-wrap;
			word-wrap: break-word;
			max-width: 1px;
			width: 100%;
		}

		td textarea {
			border: none;
			width: auto;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">Add new child sku</li>
		</ol>
		@include('includes.error_div')
		<h3 class = "page-header">Add new child sku</h3>
		@setvar($i = 0)
		{!! Form::open(['url' => url('/logistics/add_child_sku'), 'method' => 'post', 'class' => 'form-horizontal']) !!}
		{!! Form::hidden("store_id", $store_id) !!}
		{!! Form::hidden("return_to", $returnTo) !!}
		<div class = "form-group">
			{!! Form::label('allow_mixing', "Batch route", ['class' => 'col-md-2 control-label']) !!}
			<div class = "col-sm-10">
				{!! Form::select('allow_mixing', \App\Product::$mixingStatues, 1, ['class'=> 'form-control', 'id' => 'allow_mixing']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!! Form::label('batch_route_id', "Batch route", ['class' => 'col-md-2 control-label']) !!}
			<div class = "col-sm-10">
				{!! Form::select('batch_route_id', $batch_routes, null, ['class'=> 'form-control', 'id' => 'batch_route_id']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!! Form::label('id_catalog', "ID", ['class' => 'col-md-2 control-label']) !!}
			<div class = "col-sm-10">
				{!! Form::text('id_catalog', null, ['class'=> 'form-control', 'id' => 'id_catalog']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!! Form::label('parent_sku', "Parent SKU", ['class' => 'col-md-2 control-label']) !!}
			<div class = "col-sm-10">
				{!! Form::text('parent_sku', null, ['class'=> 'form-control', 'id' => 'parent_sku']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!! Form::label('child_sku', "Child SKU", ['class' => 'col-md-2 control-label']) !!}
			<div class = "col-sm-10">
				{!! Form::text('child_sku', null, ['class'=> 'form-control', 'id' => 'child_sku']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!! Form::label('graphic_sku', "Graphic SKU", ['class' => 'col-md-2 control-label']) !!}
			<div class = "col-sm-10">
				{!! Form::text('graphic_sku', null, ['class'=> 'form-control', 'id' => 'graphic_sku']) !!}
			</div>
		</div>
		@foreach($parameters as $parameter)
			<div class = "form-group">
				@setvar($parameter_value = $parameter->parameter_value)
				{!! Form::label(\Monogram\Helper::textToHTMLFormName($parameter_value), ucfirst($parameter_value), ['class' => 'col-md-2 control-label']) !!}
				<div class = "col-sm-10">
					{!! Form::text(\Monogram\Helper::textToHTMLFormName($parameter_value), null, ['class'=> 'form-control', 'id' => \Monogram\Helper::textToHTMLFormName($parameter_value)]) !!}
				</div>
			</div>
			@setvar($i++)
		@endforeach
		<div class = "form-group">
			<div class = "col-sm-offset-2 col-sm-10">
				<button type = "submit" class = "btn btn-primary">Add new child sku</button>
			</div>
		</div>
		{!! Form::close() !!}
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		$(function ()
		{
			$('[data-toggle="tooltip"]').tooltip();
		});
		var message = {
			delete: 'Are you sure you want to delete?',
		};
		$(".delete-sku_converter").on('click', function (event)
		{
			event.preventDefault();
			var action = confirm(message.delete);
			if ( action ) {
				$(this).closest('form').submit();
			}
			//return false;
		});
	</script>
</body>
</html>