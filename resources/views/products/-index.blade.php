<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Products</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('/products')}}">Products</a></li>
		</ol>
		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get', 'id' => 'search-order']) !!}
			<div class = "form-group col-xs-4">
				<label for = "id_catalog">Search in id catalog</label>
				{!! Form::text('id_catalog', $request->get('id_catalog'), ['id'=>'id_catalog', 'class' => 'form-control', 'placeholder' => 'Search in id catalog']) !!}
			</div>
			<div class = "form-group col-xs-4">
				<label for = "product_model">Search in model</label>
				{!! Form::text('product_model', $request->get('product_model'), ['id'=>'product_model', 'class' => 'form-control', 'placeholder' => 'Search in product model']) !!}
			</div>
			<div class = "form-group col-xs-4">
				<label for = "product_name">Search in name</label>
				{!! Form::text('product_name', $request->get('product_name'), ['id'=>'product_name', 'class' => 'form-control', 'placeholder' => 'Search in product name']) !!}
			</div>
			<div class = "form-group col-xs-4">
				<label for = "route">Search in route</label>
				{!! Form::select('route', $searchInRoutes, $request->get('route')?: 0, ['id'=>'route', 'class' => 'form-control']) !!}
			</div>
			<div class = "form-group col-xs-4">
				<label for = "category">Search in Categories</label>
				{!! Form::select('category', $categories, $request->get('category')?: 0, ['id'=>'category', 'class' => 'form-control']) !!}
			</div>
			<div class = "form-group col-xs-4">
				<label for = "sub_category">Search in sub categories</label>
				{!! Form::select('sub_category', $sub_categories, $request->get('sub_category')?: 0, ['id'=>'sub_category', 'class' => 'form-control']) !!}
			</div>
			<div class = "form-group col-xs-2">
				<label for = "" class = ""></label>
				{!! Form::submit('Search', ['id'=>'search', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-primary form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>
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
		@if(Session::has('success'))
			<div class = "col-xs-12">
				<div class = "alert alert-success">
					{!! Session::get('success') !!}
				</div>
			</div>
		@endif
		@if(count($products) > 0)
			<h3 class = "page-header">
				Products
				<a class = "btn btn-success btn-sm pull-right" href = "{{url('/products/create')}}">Create product</a>
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>#</th>
					<th>ID Catalog</th>
					<th>Model</th>
					<th>Product name</th>
					<th>Image</th>
					<th>Batch code</th>
					<th>Action</th>
				</tr>
				@foreach($products as $product)
					<tr data-id = "{{$product->id}}" class = "text-center">
						<td>{{ $count++ }}</td>
						<td>{{ $product->id_catalog }}</td>
						<td class = "text-center">{{ $product->product_model ? $product->product_model : '-' }}</td>
						<td>{{ $product->product_name }}</td>
						<td><img src = "{{ $product->product_thumb }}" width = "50" height = "50" /></td>
						<td>{!! Form::select('batch_route_id', $batch_routes, $product->batch_route_id, ['class' => 'form-control changable']) !!}</td>
						<td>
							{{--<a href = "#" data-toggle = "tooltip" class = "update"
							   data-placement = "top"
							   title = "Update batch route"><i class = 'fa fa-check text-primary'></i></a> |--}}
							<a href = "{{ url(sprintf("/products/%d", $product->id)) }}" data-toggle = "tooltip"
							   data-placement = "top"
							   title = "View this product"><i class = 'fa fa-eye text-warning'></i></a>
							| <a href = "{{ url(sprintf("/products/%d/edit", $product->id)) }}" data-toggle = "tooltip"
							     data-placement = "top"
							     title = "Edit this product"><i class = 'fa fa-pencil-square-o text-success'></i></a>
							| <a href = "#" class = "delete" data-toggle = "tooltip" data-placement = "top"
							     title = "Delete this product"><i class = 'fa fa-times text-danger'></i></a>
						</td>
					</tr>
				@endforeach
			</table>
			{!! Form::open(['url' => url('/products/id'), 'method' => 'delete', 'id' => 'delete-product']) !!}
			{!! Form::close() !!}

			{!! Form::open(['url' => url('/products/id'), 'method' => 'put', 'id' => 'update-product']) !!}
			{!! Form::close() !!}
			<div class = "col-xs-12 text-center">
				{!! $products->render() !!}
			</div>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No product found.</h3>
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
			update: 'Are you sure you want to update?',
			error: "You've not selected any route value to update",
		};
		$("select.changable").on('change', function ()
		{
			var value = $(this).val();
			if ( value == "null" ) {
				alert("Not a valid batch");
				return;
			}
			var id = $(this).closest('tr').attr('data-id');

			var form = $("form#update-product");
			var formUrl = form.attr('action');
			formUrl = formUrl.replace('id', id);

			var token = $(form).find('input[name="_token"]').val();
			console.log(token);
			$.ajax({
				method: 'PUT', url: formUrl, data: {
					_token: token, batch_route_id: value,
				}, success: function (data, textStatus, xhr)
				{

				}, error: function (xhr, textStatus, errorThrown)
				{
					alert('Could not update product route');
				}
			});
		});
		$("a.delete").on('click', function (event)
		{
			event.preventDefault();
			var id = $(this).closest('tr').attr('data-id');
			var action = confirm(message.delete);
			if ( action ) {
				var form = $("form#delete-product");
				var url = form.attr('action');
				form.attr('action', url.replace('id', id));
				form.submit();
			}
		});
		$(document).ready(function ()
		{
			setTimeout(function ()
			{
				$("div.alert-success").parent('div').remove();
			}, 2000);
		});
	</script>
</body>
</html>