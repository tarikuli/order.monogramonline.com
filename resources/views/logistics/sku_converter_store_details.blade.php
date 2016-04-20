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
		@include('includes.error_div')
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
					{{--<th>Edit</th>--}}
				</tr>
				@if($parameters->lists('parameter_value'))
					@foreach($options->chunk(count($parameters->lists('parameter_value'))) as $option_array)
						<tr>
							<td>
								@setvar($value = $option_array->first())
								{!! Form::open(['url' => url('/logistics/delete_sku/'.$value->unique_row_value), 'method' => 'delete']) !!}
								{!! Form::submit('Delete', ['class' => 'btn btn-danger delete-sku_converter']) !!}
								{!! Form::close() !!}
							</td>
							@foreach($option_array as $option)
								<td>{{$option->parameter_option}}</td>
							@endforeach
							{{--<td>
								<a href = "{{url(sprintf("/logistics/edit_sku_converter?store_id=%s&row=%s", $value->store_id, $value->unique_row_value))}}">Edit</a>
							</td>--}}
						</tr>
					@endforeach
				@endif
			</table>
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