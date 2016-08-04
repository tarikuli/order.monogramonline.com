<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Purchase Inventory Products</title>
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
			<li class = "active">Purchase Inventory Products</li>
		</ol>

		@include('includes.error_div')
		@include('includes.success_div')

			<a class = "btn btn-success btn-sm pull-right" href = "{{url('/purchasedinvproducts/create')}}">Create Purchase Inventory Products</a>

		@if(count($purchasedInvProducts) > 0)
			<h3 class = "page-header">
				Purchase Inventory Products
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>#</th>
					<th>Stock No</th>
					<th>Stock Name Discription</th>
					<th>Unit</th>
					<th>Unit Price</th>
					<th>Vendor Id</th>
					<th>Vendor Sku</th>
					<th>Lead Time Days</th>
					<th>Action</th>
				</tr>
				@foreach($purchasedInvProducts as $purchasedInvProduct)
					<tr data-id = "{{$purchasedInvProduct->id}}">
						<td>{{ $count++ }}</td>
						<td>{{ $purchasedInvProduct->stock_no }}</td>
						<td>
							{!! \App\Inventory::where('stock_no_unique', $purchasedInvProduct->stock_no)->take(1)->lists('stock_name_discription','id')->get($purchasedInvProduct->id)  !!}
						</td>
						<td>{{ $purchasedInvProduct->unit }}</td>
						<td>{{ $purchasedInvProduct->unit_price }}</td>
						<td>{{ $purchasedInvProduct->vendor_id }}</td>
						<td>{{ $purchasedInvProduct->vendor_sku }}</td>
						<td>{{ $purchasedInvProduct->lead_time_days }}</td>
						<td>
							{{-- <a href = "{{ url(sprintf("/purchasedinvproducts/%d", $purchasedInvProduct->id)) }}" data-toggle = "tooltip"
							   data-placement = "top"
							   title = "View this vendor"><i class = 'fa fa-eye text-primary'></i></a> --}}
							 <a href = "{{ url(sprintf("/purchasedinvproducts/%d/edit", $purchasedInvProduct->id)) }}" data-toggle = "tooltip"
							     data-placement = "top"
							     title = "View this vendor"><i class = 'fa fa-pencil-square-o text-success'></i></a>
							| <a href = "#" class = "delete" data-toggle = "tooltip" data-placement = "top"
							     title = "Delete this vendor"><i class = 'fa fa-times text-danger'></i></a>
						</td>
					</tr>
				@endforeach
			</table>
			{!! Form::open(['url' => url('/purchasedinvproducts/id'), 'method' => 'delete', 'id' => 'delete-purchasedinvproducts']) !!}
			{!! Form::close() !!}
			<div class = "col-xs-12 text-center">
				{!! $purchasedInvProducts->render() !!}
			</div>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No vendor found.</h3>
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
		};
		$("a.delete").on('click', function (event)
		{
			event.preventDefault();
			var id = $(this).closest('tr').attr('data-id');
			var action = confirm(message.delete);
			if ( action ) {
				var form = $("form#delete-purchasedinvproducts");
				var url = form.attr('action');
				form.attr('action', url.replace('id', id));
				form.submit();
			}
		});
	</script>
</body>
</html>