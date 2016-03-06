<!doctype html>
<!--suppress JSUnresolvedVariable -->
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Add product</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "/assets/css/bootstrap-horizon.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
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
			<li><a href = "{{url('products')}}">Products</a></li>
			<li class = "active">Create product</li>
		</ol>
		@include('includes.error_div')

		{!! Form::open(['url' => url('/products'), 'method' => 'post', 'class'=>'form-horizontal','role'=>'form']) !!}
		<div class = "form-group">
			{!!Form::label('store_id','Store id:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('store_id', $stores, null, ['id' => 'store_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('id_catalog','ID Catalog:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('id_catalog', null, ['id' => 'id_catalog','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('vendor_id','Vendor id:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('vendor_id', null, ['id' => 'vendor_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_name','Product name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_name', null, ['id' => 'product_name','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_model','Product model(SKU): ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_model', null, ['id' => 'product_model','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('ship_weight','Ship weight: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('ship_weight', null, ['id' => 'ship_weight','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_keywords','Product keywords: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::textarea('product_keywords', null, ['id' => 'product_keywords','class'=>'form-control', 'rows' => 2]) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_description','Product description: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::textarea('product_description', null, ['id' => 'product_description','class'=>'form-control', 'rows' => 2]) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_master_category','Product category: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			{!! Form::hidden('product_master_category', null, ['id' => 'product_master_category']) !!}
			<div class = "col-sm-8" style = "overflow: auto;">
				<div class = "row row-horizon">
					@include('master_categories.ajax_category_response')
				</div>
			</div>
		</div>
		{{--<div class = "form-group">
			{!!Form::label('product_category','Product sub category 1: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('product_category', $categories, null, ['id' => 'product_category','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_sub_category','Product sub category 2: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('product_sub_category', $sub_categories, null, ['id' => 'product_sub_category','class'=>'form-control']) !!}
			</div>
		</div>--}}
		<div class = "form-group">
			{!!Form::label('product_production_category','Product production category: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('product_production_category', $production_categories, null, ['id' => 'product_production_category','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_occasion','Product occasion',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('product_occasion', $product_occasions, null, ['id' => 'product_occasion','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_collection','Product collection',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('product_collection', $product_collections, null, ['id' => 'product_collection','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_price','Product price: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('product_price', null, ['id' => 'product_price','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_sale_price','Product sale price: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('product_sale_price', null, ['id' => 'product_sale_price','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_url','Product URL: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_url', null, ['id' => 'product_url','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_thumb','Thumb / Insert image: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_thumb', null, ['id' => 'product_thumb','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('is_taxable','Taxable: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('is_taxable', $is_taxable, null, ['id' => 'is_taxable', 'class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('batch_route_id','Route: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('batch_route_id', $batch_routes, null, ['id' => 'batch_route_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('height','Product height: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('height', null, ['id' => 'height','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('width','Product width: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('width', null, ['id' => 'width','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			<div class = "col-xs-offset-4 col-xs-5">
				{!! Form::submit('Create product',['class'=>'btn btn-primary btn-block']) !!}
			</div>
		</div>
		{{--<div class = "form-group">
			{!!Form::label('store_id','Store id:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('store_id', $stores, null, ['id' => 'store_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('id_catalog','ID Catalog:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('id_catalog', null, ['id' => 'id_catalog','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('vendor_id','Vendor id:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('vendor_id', null, ['id' => 'vendor_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_name','Product name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_name', null, ['id' => 'product_name','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('ship_weight','Ship weight: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('ship_weight', null, ['id' => 'ship_weight','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_sale_price','Sale price: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_sale_price', null, ['id' => 'product_sale_price','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('height','Product height: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('height', null, ['id' => 'height','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('weight','Product weight: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('weight', null, ['id' => 'weight','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_model','Product model: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_model', null, ['id' => 'product_model','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_keywords','Product keywords: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::textarea('product_keywords', null, ['id' => 'product_keywords','class'=>'form-control', 'rows' => 2]) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_description','Product description: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::textarea('product_description', null, ['id' => 'product_description','class'=>'form-control', 'rows' => 2]) !!}
			</div>
		</div>
		--}}{{--<div class = "form-group">
			{!!Form::label('product_category','Product category: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_category', null, ['id' => 'product_category','class'=>'form-control']) !!}
			</div>
		</div>--}}{{--
		<div class = "form-group">
			{!!Form::label('product_price','Product price: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::number('product_price', null, ['id' => 'product_price','class'=>'form-control', 'step' => 'any']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_url','Product URL: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_url', null, ['id' => 'product_url','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('product_thumb','Thumb / Insert image: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('product_thumb', null, ['id' => 'product_thumb','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('is_taxable','Taxable: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('is_taxable', $is_taxable, 1, ['id' => 'is_taxable', 'class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('master_category','Category: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('master_category', $master_categories, null, ['id' => 'category', 'class'=>'form-control']) !!}
				--}}{{--{!! Form::text('master_category', null, ['id' => 'master_category', 'class' => 'form-control']) !!}--}}{{--
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('category','Sub 1: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('category', $categories, null, ['id' => 'category', 'class'=>'form-control']) !!}
				--}}{{--{!! Form::text('category', null, ['id' => 'category', 'class' => 'form-control']) !!}--}}{{--
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('sub_category','Sub 2: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('sub_category', $sub_categories, null, ['id' => 'sub_category', 'class'=>'form-control']) !!}
				--}}{{--{!! Form::text('sub_category', null, ['id' => 'sub_category', 'class' => 'form-control']) !!}--}}{{--
			</div>
		</div>
		--}}{{--<div class = "form-group">
			{!!Form::label('sub_category','Sub category: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('sub_category', $sub_categories, null, ['id' => 'sub_category', 'class'=>'form-control']) !!}
			</div>
		</div>--}}{{--
		<div class = "form-group">
			{!!Form::label('batch_route_id','Route: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('batch_route_id', $batch_routes, null, ['id' => 'batch_route_id','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			<div class = "col-xs-offset-4 col-xs-5">
				{!! Form::submit('Add product',['class'=>'btn btn-primary btn-block']) !!}
			</div>
		</div>--}}
		{!! Form::close() !!}
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

	<script type="text/javascript">
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