<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Product - {{$product->id_catalog}}</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('products')}}">Products</a></li>
			<li class = "active">View product</li>
		</ol>
		<div class = "col-xs-12">
			<h4 class = "page-header">Product details</h4>
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
						<table class = "table table-bordered">
							<tr>
								<td>Store id</td>
								<td>{{$product->store->store_name}}</td>
							</tr>
							<tr>
								<td>ID</td>
								<td>{{$product->id_catalog}}</td>
							</tr>
							<tr>
								<td>Model(SKU)</td>
								<td>{{$product->product_model}}</td>
							</tr>
							<tr>
								<td>UPC</td>
								<td>{{$product->product_upc}}</td>
							</tr>
							<tr>
								<td>ASIN</td>
								<td>{{$product->product_asin}}</td>
							</tr>
							<tr>
								<td>Brand</td>
								<td>{{$product->product_brand}}</td>
							</tr>
							<tr>
								<td>Availability</td>
								<td>{{$product->product_availability}}</td>
							</tr>
							<tr>
								<td>Product name</td>
								<td>{{$product->product_name}}</td>
							</tr>
							<tr>
								<td>Supplier</td>
								<td>{{$product->vendor ? $product->vendor->vendor_name : "N/A"}}</td>
							</tr>
							<tr>
								<td>Financial category</td>
								<td>{{$product->financial_category}}</td>
							</tr>
							<tr>
								<td>Ship weight</td>
								<td>{{$product->ship_weight}}</td>
							</tr>
							<tr>
								<td>Default cost</td>
								<td>{{$product->product_default_cost}}</td>
							</tr>
							<tr>
								<td>Product price</td>
								<td>{{$product->product_price}}</td>
							</tr>
							<tr>
								<td>Product sale price</td>
								<td>{{$product->product_sale_price}}</td>
							</tr>
							<tr>
								<td>Wholesale price</td>
								<td>{{$product->product_wholesale_price}}</td>
							</tr>
							<tr>
								<td>Taxable</td>
								<td>{{$product->is_taxable ? "Yes" : "No"}}</td>
							</tr>
							<tr>
								<td>Drop shipper</td>
								<td>{{$product->product_drop_shipper ? "Yes" : "No"}}</td>
							</tr>
							<tr>
								<td>Royalties Product</td>
								<td>{{$product->is_royalties ? "Yes" : "No"}}</td>
							</tr>
							<tr>
								<td>Royalty paid(%)</td>
								<td>{{$product->product_royalty_paid}}</td>
							</tr>
							<tr>
								<td>Route</td>
								<td>
									@if($product->batch_route)
										{{sprintf("%s(%s)", $product->batch_route->batch_route_name, $product->batch_route->batch_code)}}
									@else
										-
									@endif
								</td>
							</tr>
							<tr>
								<td>Height</td>
								<td>{{$product->height}}</td>
							</tr>
							<tr>
								<td>Width</td>
								<td>{{$product->width}}</td>
							</tr>
						</table>
					</div>
					<div role = "tabpanel" class = "tab-pane fade" id = "tab-description">
						<table class = "table table-bordered">
							<tr>
								<td>Product keywords</td>
								<td>{{$product->product_keywords}}</td>
							</tr>
							<tr>
								<td>Product description</td>
								<td>{{$product->product_description}}</td>
							</tr>
							<tr>
								<td>Product note</td>
								<td>{{$product->product_note}}</td>
							</tr>
						</table>
					</div>
					<div role = "tabpanel" class = "tab-pane fade" id = "tab-image">
						<table class = "table table-bordered">
							<tr>
								<td>Product URL</td>
								<td>
									<a href = "{{$product->product_url}}" target = "_blank">
										{{$product->product_url}}
									</a>
								</td>
							</tr>
							<tr>
								<td>Thumb / Insert image</td>
								<td>
									@if($product->product_thumb)
										<img src = "{{$product->product_thumb}}" />
									@else
										No image is set
									@endif
								</td>
							</tr>
							<tr>
								<td>Local image</td>
								<td>
									@if(count($product->images))
										@foreach($product->images as $image)
											<a href = "{{$image->path}}" target = "_blank"><img src = "{{$image->path}}"
											                                                    width = "70"
											                                                    height = "70"
											                                                    style = "margin-right: 10px; margin-bottom: 2px;"></a>
										@endforeach
									@else
										No local image available
									@endif
								</td>
							</tr>
							<tr>
								<td>Video</td>
								<td>
									@if($product->product_video)
										<a href = "{{$product->product_video}}" target = "_blank">
											{{$product->product_video}}
										</a>
									@else
										No video link available
									@endif
								</td>
							</tr>
						</table>
					</div>
					<div role = "tabpanel" class = "tab-pane fade" id = "tab-categories">
						<table class = "table table-bordered">
							<tr>
								<td>Product category</td>
								<td>{{$product->master_category ? $product->master_category->master_category_description : "N/A"}}</td>
							</tr>
							<tr>
								<td>Product production category</td>
								<td>{{$product->production_category ? $product->production_category->production_category_description : "N/A"}}</td>
							</tr>
							<tr>
								<td>Product sales category</td>
								<td>{{$product->sales_category ? $product->sales_category->sales_category_description : "N/A"}}</td>
							</tr>
							<tr>
								<td>Product production occasions</td>
								<td>{{$product->occasions->count() ? implode(", ", $product->occasions->lists('occasion_description')->all()) : "N/A" }}</td>
							</tr>
							<tr>
								<td>Product production collections</td>
								<td>{{$product->collections->count() ? implode(", ", $product->collections->lists('collection_description')->all()) : "N/A" }}</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class = "col-xs-12" style = "margin-bottom: 30px;">
			<div class = "col-xs-offset-1 col-xs-10" style = "margin-bottom: 10px;">
				<a href = "{{ url(sprintf("/products/%d/edit", $product->id)) }}"
				   class = "btn btn-success btn-block">Edit this
				                                       product</a>
			</div>
			<div class = "col-xs-offset-1 col-xs-10">
				{!! Form::open(['url' => url(sprintf('/products/%d', $product->id)), 'method' => 'delete', 'id' => 'delete-product-form']) !!}
				{!! Form::submit('Delete product', ['class'=> 'btn btn-danger btn-block', 'id' => 'delete-product-btn']) !!}
				{!! Form::close() !!}
			</div>
		</div>
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
	</script>
</body>
</html>