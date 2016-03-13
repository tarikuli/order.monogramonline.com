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
		<div class = "col-md-12">
			<!-- Nav tabs -->
			<ul class = "nav nav-tabs" role = "tablist">
				<li role = "presentation" class = "active">
					<a href = "#tab-info" aria-controls = "info" role = "tab" data-toggle = "tab">Product info</a>
				</li>
				<li role = "presentation">
					<a href = "#tab-description" aria-controls = "description" role = "tab"
					   data-toggle = "tab">Description</a>
				</li>
				<li role = "presentation">
					<a href = "#tab-image" aria-controls = "image" role = "tab" data-toggle = "tab">Image / URL</a>
				</li>
				<li role = "presentation">
					<a href = "#tab-categories" aria-controls = "categories" role = "tab"
					   data-toggle = "tab">All categories</a>
				</li>
			</ul>

			<!-- Tab panes -->
			<div class = "tab-content" style = "margin-top: 20px;">
				<div role = "tabpanel" class = "tab-pane fade in active" id = "tab-info">
					<div class = "form-group">
						{!!Form::label('store_id','Store id:',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::select('store_id', $stores, $product->store_id, ['id' => 'store_id','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('id_catalog','ID:',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('id_catalog', $product->id_catalog, ['id' => 'id_catalog','class'=>'form-control', 'readonly' => 'readonly']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_model','Model(SKU): ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('product_model', $product->product_model, ['id' => 'product_model','class'=>'form-control', 'readonly' => 'readonly']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_upc','UPC: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('product_upc', $product->product_upc, ['id' => 'product_upc','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_asin','ASIN: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('product_asin', $product->product_asin, ['id' => 'product_asin','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_brand','Brand: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('product_brand', $product->product_brand, ['id' => 'product_brand','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_availability','Availability: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('product_availability', $product->product_availability, ['id' => 'product_availability','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_name','Product name: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('product_name', $product->product_name, ['id' => 'product_name','class'=>'form-control']) !!}
						</div>
					</div>

					<hr />

					<div class = "form-group">
						{!!Form::label('vendor_id','Supplier: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::select('vendor_id', $vendors, $product->vendor_id, ['id' => 'vendor_id','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('financial_category','Financial category: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::select('financial_category', $financial_categories, $product->financial_category, ['id' => 'financial_category','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('ship_weight','Ship weight: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::number('ship_weight', $product->ship_weight, ['id' => 'ship_weight','class'=>'form-control', 'step' => 'any']) !!}
						</div>
					</div>
					{{--<div class = "form-group">
						{!!Form::label('kit_bundle','Kit/Bundle: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::number('kit_bundle', null, ['id' => 'kit_bundle','class'=>'form-control', 'step' => 'any']) !!}
						</div>
					</div>--}}
					<div class = "form-group">
						{!!Form::label('product_default_cost','Default cost: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::number('product_default_cost', $product->product_default_cost, ['id' => 'product_default_cost','class'=>'form-control', 'step' => 'any']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_price','Product price: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::number('product_price', $product->product_price, ['id' => 'product_price','class'=>'form-control', 'step' => 'any']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_sale_price','Product sale price: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::number('product_sale_price', $product->product_sale_price, ['id' => 'product_sale_price','class'=>'form-control', 'step' => 'any']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_wholesale_price','Wholesale price: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::number('product_wholesale_price', $product->product_wholesale_price, ['id' => 'product_wholesale_price','class'=>'form-control', 'step' => 'any']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('is_taxable','Taxable: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::select('is_taxable', $is_taxable, $product->is_taxable, ['id' => 'is_taxable', 'class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('is_deleted','Delete?: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::select('is_deleted', $is_taxable, $product->is_deleted, ['id' => 'is_deleted', 'class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('is_royalties','Royalties Product?: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::select('is_royalties', $is_taxable, $product->is_royalties, ['id' => 'is_royalties', 'class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_royalty_paid','Royalty paid(%):',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::number('product_royalty_paid', $product->product_royalty_paid, ['id' => 'product_royalty_paid','class'=>'form-control', 'step' => 'any']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('batch_route_id','Route: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::select('batch_route_id', $batch_routes, $product->batch_route_id, ['id' => 'batch_route_id','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('height','Height: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::number('height', $product->height, ['id' => 'height','class'=>'form-control', 'step' => 'any']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('width','Width: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::number('width', $product->width, ['id' => 'width','class'=>'form-control', 'step' => 'any']) !!}
						</div>
					</div>
				</div>
				<div role = "tabpanel" class = "tab-pane fade" id = "tab-description">
					<div class = "form-group">
						{!!Form::label('product_keywords','Product keywords: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-10">
							{!! Form::textarea('product_keywords', $product->product_keywords, ['id' => 'product_keywords','class'=>'form-control', 'rows' => 8]) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_description','Product description: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-10">
							{!! Form::textarea('product_description', $product->product_description, ['id' => 'product_description','class'=>'form-control', 'rows' => 8]) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_note','Product note: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-10">
							{!! Form::textarea('product_note', $product->product_note, ['id' => 'product_note','class'=>'form-control', 'rows' => 8]) !!}
						</div>
					</div>
				</div>
				<div role = "tabpanel" class = "tab-pane fade" id = "tab-image">
					<div class = "form-group">
						{!!Form::label('product_url','Product URL: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('product_url', $product->product_url, ['id' => 'product_url','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_thumb','Thumb / Insert image: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('product_thumb', $product->product_thumb, ['id' => 'product_thumb','class'=>'form-control']) !!}
							@if($product->product_thumb)
								<img src = "{{$product->product_thumb}}">
							@endif
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_video','Video: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('product_video', $product->product_video, ['id' => 'product_video','class'=>'form-control']) !!}
						</div>
					</div>
				</div>
				<div role = "tabpanel" class = "tab-pane fade" id = "tab-categories">
					<div class = "form-group">
						{!!Form::label('product_master_category','Product category: ',['class'=>'control-label col-xs-2'])!!}
						{!! Form::hidden('product_master_category', $product->product_master_category, ['id' => 'product_master_category']) !!}
						<div class = "col-sm-8" style = "overflow: auto;">
							<div class = "row row-horizon">
								@include('master_categories.ajax_category_response')
							</div>
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_production_category','Product production category: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::select('product_production_category', $production_categories, $product->product_production_category, ['id' => 'product_production_category','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_sales_category','Product sales category: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::select('product_sales_category', $sales_categories, $product->product_sales_category, ['id' => 'product_sales_category','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_occasion','Product occasion',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{{--{!! Form::select('product_occasion', $product_occasions, null, ['id' => 'product_occasion','class'=>'form-control']) !!}--}}
							{!! \Monogram\Helper::scrollableCheckbox('product_occasion[]', $product_occasions, $product->occasions()->lists('occasion_id')->all()) !!}
							{{--{!! Form::select('product_occasion[]', $product_occasions, $product->occasions()->lists('occasion_id')->all(), ['id' => 'product_occasion','class'=>'form-control', 'multiple' => 'multiple']) !!}--}}
						</div>
					</div>
					<div class = "form-group">
						{!!Form::label('product_collection','Product collection',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-5">
							{{--{!! Form::select('product_collection', $product_collections, null, ['id' => 'product_collection','class'=>'form-control']) !!}--}}
							{!! \Monogram\Helper::scrollableCheckbox('product_collection[]', $product_occasions, $product->collections()->lists('collection_id')->all()) !!}
							{{--{!! Form::select('product_collection[]', $product_collections, $product->collections()->lists('collection_id')->all(), ['id' => 'product_collection','class'=>'form-control', 'multiple' => 'multiple']) !!}--}}
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class = "form-group">
			<div class = "col-md-2 pull-right">
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
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
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