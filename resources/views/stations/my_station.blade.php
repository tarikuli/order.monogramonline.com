<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>My station</title>
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
			<li>My station</li>
		</ol>
		@if(count($items) > 0)
			<h3 class = "page-header">
				Items on : {{$station_description}}
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Order#</th>
					<th>Order date</th>
					<th>Description</th>
					<th>ID</th>
					<th>Option</th>
					<th>Qty.</th>
					<th>Batch</th>
					<th>Batch creation date</th>
					<th>Action</th>
				</tr>
				@foreach($items as $item)
					<tr data-id = "{{$item->id}}">
						<td>{{$item->order->short_order}}{{--<a href = "{{ url("orders/details/".$item->order_id) }}" class = "btn btn-link">{{$item->order->short_order}}</a>--}}</td>
						<td>{{$item->order->order_date}}</td>
						<td>{{$item->item_description}}</td>
						<td>{{$item->item_id}}</td>
						<td>{{\Monogram\Helper::jsonTransformer($item->item_option)}}</td>
						<td>{{$item->item_quantity}}</td>
						<td>{{$item->batch_number}}</td>
						<td>{{$item->batch_creation_date}}</td>
						<td>
							<button class = "btn btn-success forward">Forward</button>
							<button class = "btn btn-danger back">Back</button>
						</td>
					</tr>
				@endforeach
			</table>
			{!! Form::open(['url' => url('stations/change'), 'id' => 'station-action', 'method' => 'post']) !!}
			{!! Form::close() !!}
			<div class = "col-xs-12 text-center">
				{!! $items->render() !!}
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
	<script type = "text/javascript">
		$("button.forward").on('click', function ()
		{
			var value = $(this).closest('tr').attr('data-id');
			$("<input type='hidden' value='' />")
					.attr("name", "item_id")
					.attr("value", value)
					.appendTo($("form#station-action"));
			$("<input type='hidden' value='' />")
					.attr("name", "action")
					.attr("value", 'forward')
					.appendTo($("form#station-action"));
			$("form#station-action").submit();
		});

		$("button.back").on('click', function ()
		{
			var value = $(this).closest('tr').attr('data-id');

			$("<input type='hidden' value='' />")
					.attr("name", "item_id")
					.attr("value", value)
					.appendTo($("form#station-action"));
			$("<input type='hidden' value='' />")
					.attr("name", "action")
					.attr("value", 'back')
					.appendTo($("form#station-action"));
			$("form#station-action").submit();
		});
	</script>
</body>
</html>