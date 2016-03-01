<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Purchase details</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
	@include('includes.header_menu')
	<div class = "container ">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('purchases')}}">Purchases</a></li>
			<li class = "active">View purchase</li>
		</ol>
		<div class = "col-xs-12">
			<h4 class = "page-header">Purchase details</h4>
			<table class = "table table-hover table-bordered">
				<tr class = "success">
					<td>Vendor name</td>
					<td>{{$purchase->vendor_details->vendor_name}}</td>
				</tr>
				<tr>
					<td>E-Mail</td>
					<td>{{$purchase->vendor_details->email}}</td>
				</tr>
				<tr class = "success">
					<td>Phone number</td>
					<td>{{ $purchase->vendor_details->phone_number}}</td>
				</tr>
				<tr>
					<td>LC Number</td>
					<td>{{$purchase->lc_number}}</td>
				</tr>
				<tr class = "success">
					<td>Insurance number</td>
					<td>{{$purchase->insurance_number}}</td>
				</tr>
			</table>
			@if($purchase->products)
				<table class = "table table-bordered">
					<tr>
						<th>Product name</th>
						<th>Quantity</th>
						<th>Price</th>
						<th>Sub total</th>
					</tr>
					@foreach($purchase->products as $product)
						<tr>
							<td>{{ $product->product_details->product_name }}</td>
							<td>{{ $product->quantity }}</td>
							<td>{{ $product->price }}</td>
							<td>{{ $product->sub_total }}</td>
						</tr>
					@endforeach
				</table>
			@endif
		</div>
		<div class = "col-xs-12" style = "margin-bottom: 30px;">
			{{--<div class = "col-xs-offset-1 col-xs-10" style = "margin-bottom: 10px;">
				<a href = "{{ url(sprintf("/purchases/%d/edit", $purchase->id)) }}" class = "btn btn-success btn-block">Edit
				                                                                                                        this
				                                                                                                        purchase</a>
			</div>--}}
			<div class = "col-xs-offset-1 col-xs-10" style = "margin-bottom: 10px;">
				<a href = "{{ url(sprintf("/prints/purchase/%d", $purchase->id)) }}"
				   class = "btn btn-success btn-block">
					Print purchase slip
				</a>
			</div>
			<div class = "col-xs-offset-1 col-xs-10">
				{!! Form::open(['url' => url(sprintf('/purchases/%d', $purchase->id)), 'method' => 'delete', 'id' => 'delete-vendor-form']) !!}
				{!! Form::submit('Delete purchase', ['class'=> 'btn btn-danger btn-block', 'id' => 'delete-vendor-btn']) !!}
				{!! Form::close() !!}
			</div>
		</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript">
		var message = {
			delete: 'Are you sure you want to delete?',
		};
		$("input#delete-vendor-btn").on('click', function (event)
		{
			event.preventDefault();
			var action = confirm(message.delete);
			if ( action ) {
				var form = $("form#delete-vendor-form");
				form.submit();
			}
		});
	</script>
</body>
</html>