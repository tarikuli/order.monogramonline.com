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
	<link type = "text/css" rel = "stylesheet"
	      href = "/assets/css/bootstrap-horizon.css" />
	<style>
		.parent-selector{
			width: 135px;
			overflow: auto;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('/products')}}">Products</a></li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "col-xs-12">
			<a class = "btn btn-warning pull-right" role = "button" data-toggle = "collapse" href = "#collapse"
			   aria-expanded = "false" aria-controls = "collapse">
				Export / Import products
			</a>
		</div>
		<div class = "col-xs-12" style = "margin-top: 10px;">
			<div class = "collapse" id = "collapse">
				<div class = "col-xs-6">
					{!! Form::open(['url' => url('products/import'), 'files' => true, 'id' => 'importer']) !!}
					<div class = "form-group">
						{!! Form::file('csv_file', ['required' => 'required', 'class' => 'form-control', 'accept' => '.csv']) !!}
					</div>
					<div class = "form-group">
						{!! Form::submit('Import', ['class' => 'btn btn-info']) !!}
					</div>
					{!! Form::close() !!}
				</div>
				<div class = "col-xs-6">
					<a class = "btn btn-info pull-right" href = "{{url('products/export')}}">Export products</a>
				</div>
			</div>
		</div>
		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get', 'id' => 'search-order']) !!}
			<div class = "col-md-12">
				<div class = "form-group col-xs-3">
					<label for = "id_catalog">Search in ID Catalog</label>
					{!! Form::text('id_catalog', $request->get('id_catalog'), ['id'=>'id_catalog', 'class' => 'form-control', 'placeholder' => 'Search in ID Catalog']) !!}
				</div>
				<div class = "form-group col-xs-3">
					<label for = "product_model">Search in SKU</label>
					{!! Form::text('product_model', $request->get('product_model'), ['id'=>'product_model', 'class' => 'form-control', 'placeholder' => 'Search in product SKU']) !!}
				</div>
				<div class = "form-group col-xs-3">
					<label for = "product_name">Search in name</label>
					{!! Form::text('product_name', $request->get('product_name'), ['id'=>'product_name', 'class' => 'form-control', 'placeholder' => 'Search in product name']) !!}
				</div>
				<div class = "form-group col-xs-3">
					<label for = "route">Search in route</label>
					{!! Form::select('route', $searchInRoutes, $request->get('route') ?: 'all', ['id'=>'route', 'class' => 'form-control']) !!}
				</div>
			</div>
			<div class = "col-md-12">
				<div class = "form-group col-xs-3">
					<label for = "product_production_category">Search in production category</label>
					{!! Form::select('product_production_category', $production_categories, $request->get('product_production_category') ?: 'all', ['id'=>'product_production_category', 'class' => 'form-control']) !!}
				</div>
				<div class = "form-group col-xs-9">
					<label for = "product_master_category">Search in category</label>
					{!! Form::hidden('product_master_category', null, ['id' => 'product_master_category']) !!}
					<div class = "col-sm-12" style = "overflow: auto;">
						<div class = "row row-horizon">
							@include('master_categories.ajax_category_response')
						</div>
					</div>
					{{--{!! Form::select('product_master_category', $product_master_category, $request->get('product_master_category') ?: 'all', ['id'=>'product_master_category', 'class' => 'form-control']) !!}--}}
				</div>
			</div>

			<div class = "form-group col-xs-2">
				<label for = "" class = ""></label>
				{!! Form::submit('Search', ['id'=>'search', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-primary form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>
		@if(count($products) > 0)
			<h3 class = "page-header">
				Products ({{ $products->total() }} items found / {{$products->currentPage()}} of {{$products->lastPage()}} pages)
				<a style = "margin-bottom:20px" class = "btn btn-success btn-sm pull-right"
				   href = "{{url('/products/create')}}">Create product</a>
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Remove</th>
					<th>#</th>
					<th>ID Catalog</th>
					<th>SKU</th>
					<th>Product name</th>
					<th>Image</th>
					<th>Route code</th>
					<th>Action</th>
				</tr>
				@foreach($products as $product)
					<tr data-id = "{{$product->id}}" class = "text-center">
						<td>
							<a href = "#" class = "delete" data-toggle = "tooltip" data-placement = "top"
							   title = "Delete this product"><i class = 'fa fa-times text-danger'></i></a>
						</td>
						<td>{{ $count++ }}</td>
						<td>{{ $product->id_catalog }}</td>
						<td class = "text-center">{{ $product->product_model ? $product->product_model : '-' }}</td>
						<td>{{ $product->product_name }}</td>
						<td><img src = "{{ $product->product_thumb }}" width = "50" height = "50" /></td>
						<td>{!! Form::select('batch_route_id', $batch_routes, $product->batch_route_id, ['class' => 'form-control changable']) !!}</td>
						<td>
							<a href = "{{ url(sprintf("/products/%d", $product->id)) }}" data-toggle = "tooltip"
							   data-placement = "top"
							   title = "View this product"><i class = 'fa fa-eye text-warning'></i></a>
							| <a href = "{{ url(sprintf("/products/%d/edit", $product->id)) }}" data-toggle = "tooltip"
							     data-placement = "top"
							     title = "Edit this product"><i class = 'fa fa-pencil-square-o text-success'></i></a>
						</td>
					</tr>
				@endforeach
			</table>
			{!! Form::open(['url' => url('/products/id'), 'method' => 'delete', 'id' => 'delete-product']) !!}
			{!! Form::close() !!}

			{!! Form::open(['url' => url('/products/id'), 'method' => 'put', 'id' => 'update-product']) !!}
			{!! Form::close() !!}
			<div class = "col-xs-12 text-center">
				{!! $products->appends($request->all())->render() !!}
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
		$("a.update").on('click', function (event)
		{
			event.preventDefault();
			var id = $(this).closest('tr').attr('data-id');
			var value = $(this).closest('tr').find('select').val();
			if ( value == "null" ) {
				alert(message.error);
				return;
			}
			var action = confirm(message.update);
			if ( action ) {
				var form = $("form#update-product");
				var url = form.attr('action');
				form.attr('action', url.replace('id', id));
				$("<input type='hidden' value='' />")
						.attr("name", "batch_route_id")
						.attr("value", value)
						.appendTo($("form#update-product"));
				form.submit();
			}
		});

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
					_token: token, batch_route_id: value, update_batch: true,
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
		$(document).on('change', "select.parent-selector", function (event)
		{
			var node = $(this);
			var selected_parent_category = parseInt($(this).val());
			delete_next(node);

			if ( !selected_parent_category ) {
				var parent_id = $(this).closest('div.col-sm-3').attr('data-parent');
				set_parent_category(parent_id);
				return false;
			}

			set_parent_category(selected_parent_category);
			ajax_performer(selected_parent_category, node);
		});

		function delete_next (node)
		{
			$(node).closest('div.col-sm-3').nextAll().each(function ()
			{
				$(this).remove();
			});
		}

		function set_parent_category (val)
		{
			$("#product_master_category").val(val);
		}

		function set_select_form_data (node, data)
		{
			$(node).closest('div.col-sm-3').after(data);
		}

		function ajax_performer (category_id, node)
		{
			var url = "/master_categories/get_next/" + category_id;
			var method = "GET";
			$.ajax({
				method: method, url: url, success: function (data, status, xhr)
				{
					var select_form_data = data.select_form_data;
					set_select_form_data(node, select_form_data);
				}, error: function (xhr, status, error)
				{
					alert("Something went wrong!!");
				}
			});
		}
	</script>
</body>
</html>