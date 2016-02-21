<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Supervisor</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<style>
		td {
			width: 1px;
			white-space: nowrap;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('stations/supervisor')}}">Supervisor</a></li>
		</ol>
		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get']) !!}
			<div class = "col-xs-12">
				<div class = "form-group col-xs-2">
					<label for = "batch">Batch#</label>
					{!! Form::text('batch', $request->get('batch'), ['id'=>'batch', 'class' => 'form-control', 'placeholder' => 'Search in batch']) !!}
				</div>
				<div class = "form-group col-xs-3">
					<label for = "route">Route</label>
					{!! Form::select('route', $routes, $request->get('route'), ['id'=>'route', 'class' => 'form-control']) !!}
				</div>
				<div class = "form-group col-xs-3">
					<label for = "route">Station</label>
					{!! Form::select('station', $stations, $request->get('station'), ['id'=>'station', 'class' => 'form-control']) !!}
				</div>
				<div class = "form-group col-xs-2">
					<label for = "status">Status</label>
					{!! Form::select('status', $statuses, $request->get('status'), ['id'=>'status', 'class' => 'form-control']) !!}
				</div>
				<div class = "form-group col-xs-2">
					<label for = "option_text">Option text </label>
					{!! Form::text('option_text', str_replace("_", " ", $request->get('option_text')), ['id'=>'option_text', 'class' => 'form-control', 'placeholder' => 'Search in option text']) !!}
				</div>
			</div>
			<div class = "col-xs-12">
				<div class = "form-group col-xs-2">
					<label for = "order_id">order ids </label>
					{!! Form::text('order_id', $request->get('order_id'), ['id'=>'order_id', 'class' => 'form-control', 'placeholder' => 'Search in order id']) !!}
				</div>
				<div class = "form-group col-xs-2 pull-right">
					<label for = "" class = ""></label>
					{!! Form::submit('Search', ['id'=>'search', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-primary form-control']) !!}
				</div>
			</div>
			{!! Form::close() !!}
		</div>
		@if(count($items) > 0)
			<h3 class = "page-header">
				Items for supervision
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Order#</th>
					<th>Order date</th>
					<th>Store id</th>
					<th>SKU</th>
					<th>Qty.</th>
					<th>Batch</th>
					<th>Route</th>
					<th>From station</th>
					<th>Rejection message</th>
					<th>Batch creation date</th>
					<th>Station</th>
					<th>Order status</th>
					<th>Item status</th>
				</tr>
				@foreach($items as $item)
					<tr data-id = "{{$item->id}}" class = "text-center">
						<td><a href = "{{ url("orders/details/".$item->order_id) }}"
						       class = "btn btn-link">{{$item->order->short_order}}</a></td>
						<td>{{substr($item->order->order_date, 0, 10)}}</td>
						<td>{{$item->store->store_name}}</td>
						<td>{{$item->item_code}}</td>
						<td>{{$item->item_quantity}}</td>
						<td>{{$item->batch_number ?: "N/A" }}</td>
						<td>{{ $item->route ? $item->route->batch_route_name : "-"}}</td>
						<td>{{$item->previous_station ?: "-"}}</td>
						<td class = "text-center">{{$item->rejection_message ?: " - "}}</td>
						<td>{{$item->batch_creation_date ? substr($item->batch_creation_date, 0, 10) : "N/A"}}</td>
						<td>
							{!! Form::select('next_station', $item->route ? $item->route->stations_list->lists('station_description', 'station_name')->prepend('Select a next station', '') : [], $item->station_name, ['class' => 'next_station']) !!}
						</td>
						{{-- order_status = status from order_table --}}
						<td>{!! Form::select('order_status', \App\Status::where('is_deleted', 0)->lists('status_name','id'), $item->order->order_status, ['class' => 'order_status'])  !!}</td>
						{{-- Items status = order_item_status --}}
						<td>{!! Form::select('item_order_status_2', \Monogram\Helper::getItemOrderStatusArray(), $item->item_order_status_2, ['class' => 'item_order_status_2'])  !!}</td>
					</tr>
					<tr>
						<td colspan = "13" class = "text-center">
							@if($item->route)
								{{$item->route->batch_route_name}} => {!! \Monogram\Helper::routeThroughStations($item->route->id) !!}
							@endif
						</td>
					</tr>
				@endforeach
				{!! Form::open(['url' => url('stations/on_change_apply'), 'method' => 'post', 'id' => 'on-change-apply']) !!}
				{!! Form::close() !!}
			</table>
			<div class = "col-xs-12 text-center">
				{!! $items->render() !!}
			</div>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No item found to supervise.</h3>
				</div>
			</div>
		@endif
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		$("select.next_station").on('change', function ()
		{
			$(this).prop('disabled', 'disabled');
			var station_name = $(this).val();
			var item_id = $(this).closest('tr').attr('data-id');

			$("<input type='hidden' value='' />")
					.attr("name", "item_id")
					.attr("value", item_id)
					.appendTo($("form#on-change-apply"));
			$("<input type='hidden' value='' />")
					.attr("name", "station_name")
					.attr("value", station_name)
					.appendTo($("form#on-change-apply"));

			$("form#on-change-apply").submit();
		});

		$("select.order_status").on('change', function ()
		{
			$(this).prop('disabled', 'disabled');
			var order_status = $(this).val();
			var item_id = $(this).closest('tr').attr('data-id');

			$("<input type='hidden' value='' />")
					.attr("name", "item_id")
					.attr("value", item_id)
					.appendTo($("form#on-change-apply"));
			$("<input type='hidden' value='' />")
					.attr("name", "order_status")
					.attr("value", order_status)
					.appendTo($("form#on-change-apply"));
			$("form#on-change-apply").submit();
		});

		$("select.item_order_status_2").on('change', function ()
		{
			$(this).prop('disabled', 'disabled');
			var item_order_status_2 = $(this).val();
			var item_id = $(this).closest('tr').attr('data-id');

			$("<input type='hidden' value='' />")
					.attr("name", "item_id")
					.attr("value", item_id)
					.appendTo($("form#on-change-apply"));
			$("<input type='hidden' value='' />")
					.attr("name", "item_order_status_2")
					.attr("value", item_order_status_2)
					.appendTo($("form#on-change-apply"));
			$("form#on-change-apply").submit();
		});
	</script>
</body>
</html>