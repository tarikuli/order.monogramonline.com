<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Orders list</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('orders/list')}}">Orders</a></li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "col-xs-12">
			{{--{!! Form::open(['method' => 'get', 'url' => url('orders/search'), 'id' => 'search-order']) !!}--}}
			{!! Form::open(['method' => 'get', 'url' => url('orders/list'), 'id' => 'search-order']) !!}
			<div class = "form-group col-xs-2">
				<label for = "store">Market/Store</label>
				{!! Form::select('store', $stores, $request->get('store'), ['id'=>'store', 'class' => 'form-control']) !!}
			</div>
			<div class = "form-group col-xs-2">
				<label for = "status">Status</label>
				{!! Form::select('status', $statuses, $request->get('status'), ['id'=>'status', 'class' => 'form-control']) !!}
			</div>
			<div class = "form-group col-xs-2">
				<label for = "shipping_method">Shipping method</label>
				{!! Form::select('shipping_method', $shipping_methods, $request->get('shipping_method'), ['id'=>'shipping_method', 'class' => 'form-control']) !!}
			</div>
			<div class = "form-group col-xs-2">
				<label for = "search_for">Search for</label>
				{!! Form::text('search_for', $request->get('search_for'), ['id'=>'search_for', 'class' => 'form-control', 'placeholder' => 'Comma delimited']) !!}
			</div>
			<div class = "form-group col-xs-2">
				<label for = "search_in">Search in</label>
				{!! Form::select('search_in', $search_in, $request->get('search_in'), ['id'=>'search_in', 'class' => 'form-control']) !!}
			</div>
			<br />
			<div class = "form-group col-xs-3">
				<label for = "start_date">Start date</label>
				<div class = 'input-group date' id = 'start_date_picker'>
					{!! Form::text('start_date', $request->get('start_date'), ['id'=>'start_date', 'class' => 'form-control', 'placeholder' => 'Enter start date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
			<div class = "form-group col-xs-3">
				<label for = "end_date">End date</label>
				<div class = 'input-group date' id = 'end_date_picker'>
					{!! Form::text('end_date', $request->get('end_date'), ['id'=>'end_date', 'class' => 'form-control', 'placeholder' => 'Enter end date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
			<div class = "form-group col-xs-2">
				<label for = "" class = ""></label>
				{!! Form::submit('Search', ['id'=>'search', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-primary form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>
		@if(count($orders) > 0)
			<h3 class = "page-header">
				Orders ({{ $orders->total() }} orders found / $ {{sprintf("%.2f", $money)}} / {{$orders->currentPage()}} of {{$orders->lastPage()}} pages)
				<a class = "btn btn-success btn-sm pull-right" href = "{{url('/orders/create')}}">Create order</a>
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Order#</th>
					<th>Store order#</th>
					<th>Customer#</th>
					<th>Barcode</th>
					<th>Name</th>
					<th>State/Country</th>
					<th>Item</th>
					<th>Order total</th>
					<th>Order date</th>
					<th>Ship method</th>
					<th>Tracking number</th>
					<th>Status</th>
				</tr>
				@foreach($orders as $order)
					<tr data-id = "{{$order->id}}">
						<td>
							<a href = "{{ url("orders/details/".$order->order_id) }}"
							   class = "btn btn-link">{{\Monogram\Helper::orderIdFormatter($order)}}</a>
						</td>
						<td>
							<a href = "{{ url("orders/details/".$order->order_id) }}"
							   class = "btn btn-link">
								{{\Monogram\Helper::orderNameFormatter($order)}}
							</a>
						</td>
						<td><a href = "{{ url("customers/" . ($order->customer ? $order->customer->id : "#")) }}"
						       title = "This is customer id"
						       class = "btn btn-link">{{$order->customer ? $order->customer->id : "Error"}}</a></td>
						<td>{!! \Monogram\Helper::getHtmlBarcode($order->short_order) !!}</td>
						<td>{{$order->customer ? $order->customer->ship_full_name : "#"}}</td>
						<td>{{$order->customer ? $order->customer->ship_state: "#"}}, {{$order->customer ? $order->customer->ship_country : "#"}}</td>
						<td>{{$order->item_count}}</td>
						<td><i class = "fa fa-usd"></i>{{$order->total}}</td>
						<td>{{substr($order->order_date, 0, 10)}}</td>
						<td>{{$order->customer ? $order->customer->shipping : "#"}}</td>
						<td>{!! Monogram\Helper::tracking_number_formatter($order->shippingInfo) ?: "N/A" !!}</td>
						{{--<td>{!! Form::select('status', $statuses, App\Status::find($order->order_status)->status_code) !!}</td>--}}
						<td>{!! $statuses->get(\App\Status::find($order->order_status)->status_code) !!}</td>
					</tr>
				@endforeach
			</table>
			<div class = "col-xs-12 text-center">
				{!! $orders->appends($request->all())->render() !!}
			</div>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No order found.</h3>
				</div>
			</div>
		@endif
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript"
	        src = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
	<script type = "text/javascript">
		var options = {
			format: "YYYY-MM-DD", maxDate: new Date()
		};
		$(function ()
		{
			$('#start_date_picker').datetimepicker(options);
			$('#end_date_picker').datetimepicker(options);
		});
	</script>
</body>
</html>