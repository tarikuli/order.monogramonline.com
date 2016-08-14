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
				<tr>
					<td class = "success">Purchase Order # :</td>
					<td>{{$purchase->po_number}}</td>
					<td class = "success">PO Date:</td>
					<td>{{$purchase->po_date}}</td>
				</tr>

				<tr>
					<td class = "success">Vendor name</td>
					<td colspan="3">{{$purchase->vendor_details->vendor_name}}</td>
				</tr>
				<tr>
					<td class = "success">E-Mail</td>
					<td colspan="3">{{$purchase->vendor_details->email}}</td>
				</tr>
				<tr>
					<td class = "success">Phone number</td>
					<td colspan="3">{{ $purchase->vendor_details->phone_number}}</td>
				</tr>


				<tr>
					<td colspan="4">
						@if($purchase->products)
							<table class = "table table-bordered">
								<tr class = "success">
									<th>vendor_sku</th>
									<th>product_id</th>
									<th>stock_no</th>
									<th>Sku_Name</th>
									<th>quantity</th>
									<th>price</th>
									<th>sub_total</th>
									<th>receive_date</th>
									<th>receive_quantity</th>
									<th>balance_quantity</th>
								</tr>
								@foreach($purchase->products as $product)
									<tr >
										<td>{{ $product->vendor_sku }}</td>
										<td>{{ $product->product_id }}</td>
										<td>{{ $product->stock_no }}</td>
										<td>{{ $product->product_details->vendor_sku_name }}</td>
										<td>{{ $product->quantity }}</td>
										<td>{{ $product->price }}</td>
										<td align="right">{{ number_format($product->sub_total, 2, '.', '') }}</td>
										<td>{{ $product->receive_date }}</td>
										<td>{{ $product->receive_quantity }}</td>
										<td>{{ $product->balance_quantity }}</td>
									</tr>
								@endforeach
									<tr>
										<td colspan="6" align="right" class = "success"><b>Grand Total:</b></td>
										<td align="right"><b>{{ number_format($purchase->grand_total, 2, '.', '') }}</b></td>
										<td colspan="3"></td>
									</tr>
							</table>
						@endif
					</td>
				</tr>
			</table>

		</div>
		{{--
		<div class = "col-xs-12" style = "margin-bottom: 30px;">
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
		--}}
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