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

		.maxtdwidth {
			max-width: 160px;
			white-space: inherit;
			word-wrap: normal;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container" style = "margin-left: 50px;">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">SKU Converter details</li>
		</ol>
		<div class = "col-md-12">
			@include('includes.error_div')
			@include('includes.success_div')
		</div>
		<div class = "col-md-12">
			<div class = "panel panel-default">
				<div class = "panel-heading">Search</div>
				<div class = "panel-body">
					{!! Form::open(['method' => 'get', 'url' => $request->url(), 'class' => 'form-inline']) !!}
					{!! Form::hidden('store_id', $store_id) !!}
					<div class = "form-group">
						{!! Form::label('search_for', "Search for:", ['class' => 'control-label']) !!}
						{!! Form::text('search_for', $request->get('search_for'), ['id' => 'search_for', 'class' => 'form-control', 'placeholder' => "Search in selected field"]) !!}
					</div>
					<div class = "form-group">
						{!! Form::label('search_in', "Search in:", ['class' => 'control-label']) !!}
						{!! Form::select('search_in', $searchable, $request->get('search_in'), ['id' => 'search_in', 'class' => 'form-control']) !!}
					</div>

					<button type = "submit" class = "btn btn-success">Search</button>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
		<h3 class = "page-header">
			Parameters ({{ $options->total() }} items found / {{$options->currentPage()}} of {{$options->lastPage()}} pages)

			<a style = "margin-bottom:20px" class = "btn btn-success btn-sm pull-right"
			   href = "{{ url(sprintf("/logistics/add_child_sku?store_id=%s&return_to=%s", request('store_id'),$returnTo)) }}">Add new child sku</a>

		</h3>
		<div class = "row">
			<div class = "col-md-12">
				@if($parameters && (count($parameters->lists('parameter_value')) > 0))
					<table class = "table table-bordered">
						<tr>
							<th>Delete</th>
							<th>Allow Mixing</th>
							<th>Route</th>
							<th>ID</th>
							<th>Parent SKU</th>
							<th>Child SKU</th>
							<th>Graphic SKU</th>
							<th>Image</th>
							@foreach($parameters as $parameter)
								<th>{{$parameter->parameter_value}}</th>
							@endforeach
							<th>Edit</th>
						</tr>
						@foreach($options as $option)
							@setvar($decoded = json_decode($option->parameter_option, true))
							<tr>
								<td>
									{!! Form::open(['url' => url('/logistics/delete_sku/'.$option->unique_row_value), 'method' => 'delete']) !!}
									{!! Form::hidden('return_to', $returnTo) !!}
									{!! Form::submit('Delete', ['class' => 'btn btn-danger delete-sku_converter']) !!}
									{!! Form::close() !!}
								</td>
								<td>
									{!! Form::open(['url' => url('/logistics/update_parameter_option/'.$option->unique_row_value), 'method' => 'post']) !!}
									{!! Form::hidden('return_to', $returnTo) !!}
									{!! Form::select('allow_mixing', \App\Product::$mixingStatues, $option->allow_mixing, ['class' => 'form-control changeable', 'style' => 'min-width: 75px;']) !!}
									{!! Form::close() !!}
								</td>
								<td>
									{!! Form::open(['url' => url('/logistics/update_parameter_option/'.$option->unique_row_value), 'method' => 'post']) !!}
									{!! Form::hidden('return_to', $returnTo) !!}
									{!! Form::select('batch_route_id', $batch_routes, $option->batch_route_id, ['class' => 'form-control changeable', 'style' => 'width: 150px']) !!}
									{!! Form::close() !!}
									<br />
									@if($option->route && $option->route->template)
										Temp:
											<a href = "{{ url(sprintf("/templates/%s", $option->route->template->id)) }}"
											   target = "_blank">{{$option->route->template->template_name}}</a>
									@else
										<p>Temp:N/A</p>
									@endif
								</td>
								<td class = 'maxtdwidth'>
									<a href = "{{ url(sprintf("http://www.monogramonline.com/%s.html", $option->id_catalog)) }}"
									   target = "_blank">{{ $option->id_catalog }}</a>
								</td>
								<td class = 'maxtdwidth'>
									<a href = "{{url(sprintf("products?search_for=%s&search_in=product_model&product_sales_category=all&product_master_category=&category=0", $option->parent_sku)) }}"
									   target = "_blank">{{ $option->parent_sku }} </a>
								</td>
								<td class = 'maxtdwidth'>
									{{ $option->child_sku }}
								</td>
								<td class = 'maxtdwidth'> {{ $option->graphic_sku }} </td>
								<td>
									@if($option->product && $option->product->product_thumb)
										<img src = "{{ $option->product->product_thumb }}" width = "50"
										     height = "50" />
									@else
										N/A
									@endif
								</td>
								@foreach($parameters as $parameter)
									<td>
										@if(in_array($parameter->parameter_value, array_keys($decoded)))
											{{ $decoded[$parameter->parameter_value] }}
										@endif
									</td>
								@endforeach
								<td>
									<a href = "{{url(sprintf("/logistics/edit_sku_converter?store_id=%s&row=%s&return_to=%s", $option->store_id, $option->unique_row_value, $returnTo))}}">Edit</a>
								</td>
							</tr>
						@endforeach
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
		</div>
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

		$("select.changeable").on('change', function (event)
		{
			event.preventDefault();
			var form = $(this).closest('form');
			$(form).submit();
		});
	</script>
</body>
</html>