<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Station status</title>
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
			<li><a href = "{{url('stations/status')}}">Station status</a></li>
		</ol>
		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get', 'url' => url('stations/status'), 'id' => 'station-status-form']) !!}
			<div class = "form-group col-xs-5">
				<label for = "station_name">Select stations</label>
				{!! Form::select('station_name', $stations, $station_name, ['id'=>'station_name', 'class' => 'form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>
		@if(count($items) > 0)
			<h3 class = "page-header">
				Items on station {{ucfirst($station_name)}}
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Order#</th>
					<th>Order date</th>
					<th>Store id</th>
					<th>Customer</th>
					<th>State</th>
					<th>Description</th>
					<th>ID</th>
					<th>Option</th>
					<th>Qty.</th>
					<th>Batch</th>
					<th>Batch creation date</th>
					{{--<th>Station</th>--}}
				</tr>
				@foreach($items as $item)
					<tr data-id = "{{$item->id}}">
						<td><a href = "{{ url("orders/details/".$item->order_id) }}"
						       class = "btn btn-link">{{$item->order->short_order}}</a></td>
						<td>{{$item->order->order_date}}</td>
						<td>{{$item->store->store_name}}</td>
						<td><a href = "{{ url("customers/".$item->order->customer->id) }}" title = "This is customer id"
						       class = "btn btn-link">{{ !empty($item->order->customer->ship_full_name) ? $item->order->customer->ship_full_name : $item->order->customer->bill_full_name }}</a>
						</td>
						<td>{{$item->order->customer->ship_state}}</td>
						<td>{{$item->item_description}}</td>
						<td>{{$item->item_id}}</td>
						<td>{{\Monogram\Helper::jsonTransformer($item->item_option)}}</td>
						<td>{{$item->item_quantity}}</td>
						<td>{{$item->batch_number ?: 'N/A' }}</td>
						<td>{{$item->batch_creation_date ?: 'N/A'}}</td>
						{{--<td>
							@if(is_null($item->route))
								N/A
							@else
								{!! Form::select('routes', $item->route->stations_list->lists('station_description', 'station_name'), $item->station_name, ['class' => 'form-control']) !!}
							@endif
						</td>--}}
					</tr>
				@endforeach
			</table>
			<div class = "col-xs-12 text-center">
				{!! $items->appends($request->all())->render() !!}
			</div>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No item found.</h3>
				</div>
			</div>
		@endif
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$("select#station_name").on('change', function(){
			$("form#station-status-form").submit();
		});
	</script>
</body>
</html>