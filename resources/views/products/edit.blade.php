<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Edit Product - {{ $product->id_catalog}}</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "/assets/css/bootstrap-horizon.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('products')}}">Products</a></li>
			<li class = "active">Edit product</li>
		</ol>
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

		{!! Form::open(['url' => url(sprintf("/products/%d", $product->id)), 'method' => 'put', 'class'=>'form-horizontal','role'=>'form']) !!}
		<div class = "form-group">
			{!!Form::label('store_id','Store id:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('store_id', $stores, $product->store_id, ['id' => 'store_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('id_catalog','ID Catalog:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('id_catalog', $product->id_catalog, ['id' => 'id_catalog','class'=>'form-control', 'readonly' => 'readonly']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('vendor_id','Vendor id:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('vendor_id', $product->vendor_id, ['id' => 'vendor_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_name','Product name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_name', $product->product_name, ['id' => 'product_name','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_model','Product model(SKU): ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_model', $product->product_model, ['id' => 'product_model','class'=>'form-control', 'readonly' => 'readonly']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('ship_weight','Ship weight: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('ship_weight', $product->ship_weight, ['id' => 'ship_weight','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_keywords','Product keywords: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::textarea('product_keywords', $product->product_keywords, ['id' => 'product_keywords','class'=>'form-control', 'rows' => 2]) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_description','Product description: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::textarea('product_description', $product->product_description, ['id' => 'product_description','class'=>'form-control', 'rows' => 2]) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_master_category','Product category: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			{!! Form::hidden('product_master_category', $product->product_master_category, ['id' => 'product_master_category']) !!}
			<div class = "col-sm-8" style = "overflow: auto;">
				<div class = "row row-horizon">
					@include('master_categories.ajax_category_response')
				</div>
			</div>
			{{--{!!Form::label('product_master_category','Product category: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('product_master_category', $master_categories, $product->product_master_category, ['id' => 'product_master_category','class'=>'form-control']) !!}
			</div>--}}
		</div>
		{{--<div class = "form-group">
			{!!Form::label('product_category','Product sub category 1: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('product_category', $categories, $product->product_category, ['id' => 'product_category','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_sub_category','Product sub category 2: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('product_sub_category', $sub_categories, $product->product_sub_category, ['id' => 'product_sub_category','class'=>'form-control']) !!}
			</div>
		</div>--}}
		<div class = "form-group">
			{!!Form::label('product_production_category','Product production category: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('product_production_category', $production_categories, $product->product_production_category, ['id' => 'product_production_category','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_price','Product price: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('product_price', $product->product_price, ['id' => 'product_price','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_sale_price','Product sale price: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('product_sale_price', $product->product_sale_price, ['id' => 'product_sale_price','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_url','Product URL: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_url', $product->product_url, ['id' => 'product_url','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_thumb','Thumb / Insert image: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_thumb', $product->product_thumb, ['id' => 'product_thumb','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('is_taxable','Taxable: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('is_taxable', $is_taxable, $product->is_taxable, ['id' => 'is_taxable', 'class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('batch_route_id','Route: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('batch_route_id', $batch_routes, $product->batch_route_id, ['id' => 'batch_route_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('height','Product height: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('height', $product->height, ['id' => 'height','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('width','Product width: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('width', $product->width, ['id' => 'width','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			<div class = "col-xs-offset-4 col-xs-5">
				{!! Form::submit('Update product',['class'=>'btn btn-primary btn-block']) !!}
			</div>
		</div>
		{!! Form::close() !!}

		{!! Form::open(['url' => url(sprintf('/products/%d', $product->id)), 'method' => 'delete', 'id' => 'delete-product-form', 'class'=>'form-horizontal','role'=>'form']) !!}
		<div class = "form-group">
			<div class = "col-xs-offset-4 col-xs-5">
				{!! Form::submit('Delete product', ['class'=> 'btn btn-danger btn-block', 'id' => 'delete-product-btn']) !!}
			</div>
		</div>
		{!! Form::close() !!}
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript">
		var message = {
			delete: 'Are you sure you want to delete?',
		};
		$("input#delete-product-btn").on('click', function (event)
		{
			event.preventDefault();
			var action = confirm(message.delete);
			if ( action ) {
				var form = $("form#delete-product-form");
				form.submit();
			}
		});
		$(document).on('change', "select.parent-selector", function (event)
		{
			var node = $(this);
			var selected_parent_category = parseInt($(this).val());
			delete_next(node);
			set_parent_category(parent_id);

			if ( !selected_parent_category ) {
				var parent_id = $(this).closest('div.col-sm-3').attr('data-parent');
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