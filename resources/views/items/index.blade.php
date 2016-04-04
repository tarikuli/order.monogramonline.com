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
				<a href = "{{url('products/unassigned')}}"
				   style = "font-size: 14px; padding-left: 10px;">{{$unassignedProductCount}} products Routes not assigned yet.</a>
				<a class = "btn btn-success btn-sm" style = "float: right;"
				   href = "{{url('/items/batch')}}">Create batch preview</a>
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Order#</th>
					<th>Image</th>
					<th>Order date</th>
					<th>Order status</th>
					<th>Item status</th>
					<th>Trk#</th>
					<th>Tracking date</th>
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
						       class = "btn btn-link">{{\Monogram\Helper::orderIdFormatter($item->order)}}</a></td>
						{{--<td>{!! \Monogram\Helper::getHtmlBarcode(sprintf("%s-%s", $item->order->short_order, $item->id)) !!}</td>--}}
						<td><img src = "{{$item->item_thumb}}" /></td>
						<td>{{substr($item->order->order_date, 0, 10)}}</td>
						<td>{!! Form::select('order_status', \App\Status::where('is_deleted', 0)->lists('status_name','id'), $item->order->order_status, ['id' => 'order_status_id','disabled' => 'disabled'])  !!}</td>
						<td>{!! Form::select('item_order_status_2', \Monogram\Helper::getItemOrderStatusArray(), $item->item_order_status_2, ['id' => 'item_order_status_2_id','disabled' => 'disabled'])  !!}</td>
						<td>{{$item->shipInfo ? ($item->shipInfo->tracking_number ?: "Not shipped") : "N/A"}}</td>
						<td>{{ $item->shipInfo ? $item->shipInfo->postmark_date : "N/A" }}
						<td><a href = "{{ url("customers/".$item->order->customer->id) }}" title = "This is customer id"
						       class = "btn btn-link">{{ !empty($item->order->customer->ship_full_name) ? $item->order->customer->ship_full_name : $item->order->customer->bill_full_name }}</a>
						</td>
						<td>{{$item->order->customer->ship_state}}</td>
						{{--<td>{{$item->item_description}}</td>--}}
						{{--<td>{!! Form::textarea('desc', $item->item_description, ['rows' => '2', 'cols' => '20']) !!}</td>--}}
						<td class = "description">{{$item->item_description}}</td>
						<td>{{$item->item_code}}</td>
						{{--<td>{{\Monogram\Helper::jsonTransformer($item->item_option)}}</td>--}}
						<td>{!! Form::textarea('opt', \Monogram\Helper::jsonTransformer($item->item_option), ['rows' => '3', 'cols' => '20', /*"style" => "border: none; width: 100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;"*/]) !!}</td>
						<td>{{$item->item_quantity}}</td>
						<td>{{$item->batch_number ?: 'N/A' }}</td>
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
		});
	</script>

</body>
</html>