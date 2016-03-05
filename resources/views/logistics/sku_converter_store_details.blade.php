<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>SKU Converter - details</title>
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
		@if($parameters)
			<h3 class = "page-header">
				Parameters
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Delete</th>
					@foreach($parameters as $parameter)
						<th>{{$parameter->parameter_value}}</th>
					@endforeach
					<th>Edit</th>
				</tr>
				@foreach($options->chunk(count($parameters->lists('parameter_value'))) as $option_array)
					<tr>
						@foreach($option_array as $option)
							<td>{{$option->parameter_option}}</td>
						@endforeach
					</tr>
				@endforeach
			</table>
			{!! Form::open(['url' => url('/orders/id'), 'method' => 'delete', 'id' => 'delete-order']) !!}
			{!! Form::close() !!}
			<div class = "col-xs-12 text-center">
				{!! $options->appends($request->all())->render() !!}
			</div>
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
		$("a.delete").on('click', function (event)
		{
			event.preventDefault();
			var id = $(this).closest('tr').attr('data-id');
			var action = confirm(message.delete);
			if ( action ) {
				var form = $("form#delete-order");
				var url = form.attr('action');
				form.attr('action', url.replace('id', id));
				form.submit();
			}
		});
	</script>
</body>
</html>