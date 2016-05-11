<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Edit sku data</title>
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
			<li class = "active">SKU Converter details</li>
		</ol>
		@include('includes.error_div')
		@if($options)
			<h3 class = "page-header">Edit</h3>
			@setvar($i = 0)
			{!! Form::open(['url' => url('/logistics/edit_sku_converter'), 'method' => 'put', 'class' => 'form-horizontal']) !!}
			{!! Form::hidden("store_id", $options->store_id) !!}
			{!! Form::hidden("unique_row_value", $options->unique_row_value) !!}
			{!! Form::hidden("return_to", $returnTo) !!}
			<div class = "form-group">
				{!! Form::label('allow_mixing', "Batch route", ['class' => 'col-md-2 control-label']) !!}
				<div class = "col-sm-10">
					{!! Form::select('allow_mixing', \App\Product::$mixingStatues, $options->allow_mixing, ['class'=> 'form-control', 'id' => 'allow_mixing']) !!}
				</div>
			</div>
			<div class = "form-group">
				{!! Form::label('batch_route_id', "Batch route", ['class' => 'col-md-2 control-label']) !!}
				<div class = "col-sm-10">
					{!! Form::select('batch_route_id', $batch_routes, $options->batch_route_id, ['class'=> 'form-control', 'id' => 'batch_route_id']) !!}
				</div>
			</div>
			<div class = "form-group">
				{!! Form::label('parent_sku', "Parent SKU", ['id' => 'parent_sku', 'class' => 'col-md-2 control-label']) !!}
				<div class = "col-sm-10">
					{!! Form::text('parent_sku', $options->parent_sku, ['class'=> 'form-control', 'id' => 'parent_sku']) !!}
				</div>
			</div>
			@setvar($decoded_options = json_decode($options->parameter_option, true))
			@foreach($parameters as $parameter)
				<div class = "form-group">
					@setvar($parameter_value = $parameter->parameter_value)
					{!! Form::label(\Monogram\Helper::textToHTMLFormName($parameter_value), ucfirst($parameter_value), ['class' => 'col-md-2 control-label']) !!}
					<div class = "col-sm-10">
						{!! Form::text(\Monogram\Helper::textToHTMLFormName($parameter_value), in_array($parameter_value, array_keys($decoded_options)) ? $decoded_options[$parameter_value] : null, ['class'=> 'form-control', 'id' => \Monogram\Helper::textToHTMLFormName($parameter_value)]) !!}
					</div>
				</div>
				@setvar($i++)
			@endforeach
			<div class = "form-group">
				<div class = "col-sm-offset-2 col-sm-10">
					<button type = "submit" class = "btn btn-primary">Update</button>
				</div>
			</div>
			{!! Form::close() !!}
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No sku converter parameter found.</h3>
				</div>
			</div>
		@endif
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