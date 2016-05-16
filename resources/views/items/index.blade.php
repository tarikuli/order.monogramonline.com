<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Items list</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">

	<style>
		td {
			width: 1px;
			white-space: nowrap;
		}

		td.description {
			white-space: pre-wrap;
			word-wrap: break-word;
			max-width: 1px;
			width: 100%;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('items')}}">Order items list</a></li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get', 'url' => url('items'), 'id' => 'search-order']) !!}
			<div class = "form-group col-xs-3">
				<label for = "search_for_first">Search for 1</label>
				{!! Form::text('search_for_first', $request->get('search_for_first'), ['id'=>'search_for_first', 'class' => 'form-control', 'placeholder' => 'Comma delimited']) !!}
			</div>
			<div class = "form-group col-xs-3">
				<label for = "search_in_first">Search in 1</label>
				{!! Form::select('search_in_first', $search_in, $request->get('search_in_first'), ['id'=>'search_in_first', 'class' => 'form-control']) !!}
			</div>
			<div class = "form-group col-xs-3">
				<label for = "search_for_second">Search for 2</label>
				{!! Form::text('search_for_second', $request->get('search_for_second'), ['id'=>'search_for_second', 'class' => 'form-control', 'placeholder' => 'Comma delimited']) !!}
			</div>
			<div class = "form-group col-xs-3">
				<label for = "search_in_first">Search in 2</label>
				{!! Form::select('search_in_second', $search_in, $request->get('search_in_second'), ['id'=>'search_in_second', 'class' => 'form-control']) !!}
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
			<div class = "form-group col-xs-3">
				<label for = "tracking_date">Shipping date</label>
				<div class = 'input-group date' id = 'tracking_date_picker'>
					{!! Form::text('tracking_date', $request->get('tracking_date'), ['id'=>'tracking_date', 'class' => 'form-control', 'placeholder' => 'Enter shipping date']) !!}
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
		@if(count($items) > 0)
			<h3 class = "page-header">
				Items ({{ $items->total() }} items found / {{$items->currentPage()}} of {{$items->lastPage()}} pages)
				<span style = "font-size: 14px; padding-left: 10px;"
				      class = "text-info text-center">{{$unassigned}} items batch ready to create.</span>
				<a href = "{{url('logistics/sku_show?store_id=yhst-128796189915726&unassigned=1')}}"
				   style = "font-size: 14px; padding-left: 10px;">{{$unassignedProductCount}} products Routes not assigned yet.</a>
				<a class = "btn btn-success btn-sm" style = "float: right;"
				   href = "{{url('/items/batch')}}">Create batch preview</a>
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Order#</th>
					<th>Image</th>
					<th>Order date<br>Time</th>
					<th>Order status</th>
					<th>Trk#</th>
					<th>Shipping date</th>
					<th>Customer</th> 
					<th>State</th>
					<th>Description</th>
					<th>SKU</th>
					<th>Option</th>
					<th>Qty.</th>
					<th>Batch</th>
					<th>Batch creation date</th>
					<th>Station</th>
				</tr>
				@foreach($items as $item)
					<tr data-id = "{{$item->id}}">
						<td><a href = "{{ url("orders/details/".$item->order_id) }}" target = "_blank"
						       class = "btn btn-link">{{\Monogram\Helper::orderIdFormatter($item->order)}}</a><br>Y: {{$item->order->short_order}}
						</td>
						<td><img src = "{{$item->item_thumb}}" /></td>
						<td>{{ substr($item->order->order_date, 0, 10)}} <br> {{ substr($item->order->order_date, 10, 18) }} </td>
						<td>{!! \App\Status::where('is_deleted', 0)->lists('status_name','id')->get($item->order->order_status)  !!}</td>
						<td>{{$item->shipInfo ? ($item->shipInfo->tracking_number ?: "Not shipped") : "N/A"}}</td>
						<td>{{ $item->shipInfo ? $item->shipInfo->postmark_date : "N/A" }}

						<td>{{ substr($item->order->order_date, 0, 10)}}<br>{{ substr($item->order->order_date, 10, 18) }} </td>
						<td>{!! \App\Status::where('is_deleted', 0)->lists('status_name','id')->get($item->order->order_status)  !!}</td>
						<td>{{$item->shipInfo ? ($item->shipInfo->tracking_number ?: "Not shipped") : "N/A"}}</td>
						<td>{{ $item->shipInfo ? $item->shipInfo->postmark_date : "N/A" }}
						<td><a href = "{{ url("customers/".$item->order->customer->id) }}" title = "This is customer id"
						       class = "btn btn-link">{{ !empty($item->order->customer->ship_full_name) ? $item->order->customer->ship_full_name : $item->order->customer->bill_full_name }}</a>
						</td>
						<td>{{$item->order->customer->ship_state}}</td>
						<td class = "description">{{$item->item_description}}</td>
						<td>{{$item->child_sku}}</td>
						<td>{!! Form::textarea('opt', \Monogram\Helper::jsonTransformer($item->item_option), ['rows' => '3', 'cols' => '20', /*"style" => "border: none; width: 100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;"*/]) !!}</td>
						<td>{{$item->item_quantity}}</td>
						{{-- Add Batch Link --}}
						<td>
							@if($item->batch_number)
								<a href = "{{ url(sprintf('/batches/%d/%s', $item->batch_number, $item->station_name)) }}"
								   target = "_blank">{{$item->batch_number}}</a>
							@else
								N/A
							@endif
						</td>
						<td>{{$item->batch_creation_date ?: 'N/A'}}</td>
						<td>
							@if(is_null($item->route))
								N/A
							@elseif($item->item_order_status_2 == 3)
								Completed
							@else
								{!! Form::select('routes', $item->route->stations_list->lists('station_description', 'station_name'), $item->station_name, ['disabled' => 'disabled']) !!}
							@endif
						</td>
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
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
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
			$('#tracking_date_picker').datetimepicker(options);
		});
	</script>

</body>
</html>