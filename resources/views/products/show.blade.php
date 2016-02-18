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
		<div class = "col-xs-offset-1 col-xs-10 col-xs-offset-1">
			<h4 class = "page-header">Product details</h4>
			<table class = "table table-hover table-bordered">
				<tr class = "success">
					<td>Store Id</td>
					<td>{{$product->store_id}}</td>
				</tr>
				<tr>
					<td>ID Catalog</td>
					<td>{{$product->id_catalog}}</td>
				</tr>
				<tr class = "success">
					<td>Product name</td>
					<td>{{$product->product_name}}</td>
				</tr>
				<tr>
					<td>SKU</td>
					<td>{{$product->product_model}}</td>
				</tr>
				<tr class = "success">
					<td>Product keywords</td>
					<td>{{$product->product_keywords}}</td>
				</tr>
				<tr>
					<td>Product description</td>
					<td>{{$product->product_description}}</td>
				</tr>
				<tr class = "success">
					<td>Product master category</td>
					<td>{{$product->master_category ? $product->master_category->master_category_description : ""}}</td>
				</tr>
				<tr>
					<td>Product category</td>
					<td>{{$product->category ? $product->category->category_description : ""}}</td>
				</tr>
				<tr class = "success">
					<td>Product sub category</td>
					<td>{{$product->sub_category ? $product->sub_category->sub_category_description : ""}}</td>
				</tr>
				<tr>
					<td>Product price</td>
					<td>{{$product->product_price}}</td>
				</tr>
				<tr class = "success">
					<td>URL</td>
					<td><a href = "{{$product->product_url}}">{{$product->product_url}}</a></td>
				</tr>
				<tr>
					<td>Image</td>
					<td><img src = "{{$product->product_thumb}}" width = "100" height = "100" /></td>
				</tr>
				<tr class = "success">
					<td>Batch route</td>
					<td>{{$product->batch_route ? $product->batch_route->batch_code : "N/A"}}</td>
				</tr>
				<tr>
					<td>Taxable</td>
					<td>{{$product->is_taxable ? "Yes" : "No"}}</td>
				</tr>
			</table>
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