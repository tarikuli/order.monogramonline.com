<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Batch view</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.3/css/bootstrap-select.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<style>
		table {
			table-layout: fixed;
			font-size: 12px;
		}

		td {
			width: auto;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('/items/grouped')}}">Batch list</a></li>
			<li class = "active">Batch View</li>
		</ol>
		<div class = "col-xs-12">
			@if($items)
				<div class = "col-xs-8">
					<p>Batch: # <span>{{$batch_number}}</span></p>
					<a href = "{{url('exports/batch/'.$batch_number)}}">Export batch</a>
					<p>Batch creation date: <span>{{substr($items[0]->batch_creation_date, 0, 10)}}</span></p>
					<div class = "col-xs-12">
						<p>Status: {!! Form::select('status', $statuses, $items[0]->item_order_status, []) !!}</p>
						{{--{!! Form::open(['url' => url(sprintf("batches/%d", $items[0]->batch_number)), 'method' => 'put', 'class' => 'form-horizontal']) !!}
						<p>Status: {!! Form::select('status', $statuses, $items[0]->item_order_status, []) !!}</p>
						{!! Form::submit('Change status', ['id' => 'change-status',]) !!}
						{!! Form::close() !!}--}}
					</div>
					<div class = "col-xs-12">
						<div class = "btn-group" role = "group" aria-label = "...">
							{{--<button type = "button" class = "btn btn-danger" id = "reject-all">Reject all</button>--}}
							{{--<button type = "button" class = "btn btn-success" id = "done-all">Done all</button>--}}
						</div>
						{!! Form::open(['method'=>'post', 'id' => 'action_changer']) !!}
						{!! Form::hidden('action', null) !!}
						{!! Form::close() !!}
					</div>
					<p>Route: {{$route['batch_code']}} / {{$route['batch_route_name']}} => {!! $stations !!}</p>
					<p>Department: {{ $department_name }}</p>
					<p>Current Station: {!! Form::select('station', $route['stations']->lists('station_description', 'station_name')->prepend('Select a station', ''), $items[0]->station_name, ['disabled' => 'disabled']) !!}</p>
					{{-- {!! Form::open(['url' => url(sprintf("batches/%d", $items[0]->batch_number)), 'method' => 'put', 'class' => 'form-horizontal']) !!}
					<p>Station: {!! Form::select('station', $route['stations']->lists('station_description', 'station_name')->prepend('Select a station', ''), $items[0]->station_name, []) !!}</p>
					{!! Form::submit('Change station', ['id' => 'change-status',]) !!}
					{!! Form::close() !!} --}}
				</div>
				<div class = "col-xs-4">
					{!! DNS1D::getBarcodeHTML($batch_number, "C39") !!}
				</div>
				<div class = "col-xs-12">
					<table class = "table table-bordered" id = "batch-items-table">
						<thead>
						<tr>
							<th>
								<button type = "button" class = "btn btn-danger" id = "reject-all">Reject all</button>
							</th>
							<th>SL#</th>
							<th>Order</th>
							<th>Date</th>
							<th>Qty.</th>
							<th>SKU</th>
							<th>Item name</th>
							<th>Options</th>
							<th>
								<button type = "button" class = "btn btn-success" id = "done-all">Done all</button>
							</th>
						</tr>
						</thead>
						<tbody>
						@foreach($items as $item)
							<tr data-id = "{{$item->id}}">
								<td><a href = "#" class = "btn btn-danger reject">Reject</a></td>
								<td>{{$count++}}</td>
								<td>
									<a href = "{{url('/orders/details/'.$item->order->order_id)}}">{{$item->order->short_order}}</a>
								</td>
								<td>{{substr($item->order->order_date, 0, 10)}}</td>
								<td>{{$item->item_quantity}}</td>
								<td>{{$item->item_code}}</td>
								<td>{{$item->item_description}}</td>
								<td>{{\Monogram\Helper::jsonTransformer($item->item_option)}}</td>
								<td>{{--<a href = "#" class = "btn btn-danger reject">Reject</a> |--}} <a href = "#"
								                                                                          class = "btn btn-success done">Done</a>
								</td>
							</tr>
						@endforeach
						</tbody>
						<tfoot>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td id = "item-quantity-in-total"></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						</tfoot>
					</table>
				</div>
				{!! Form::open(['url' => url('stations/change'), 'id' => 'station-action', 'method' => 'post']) !!}
				{!! Form::close() !!}
			@endif
		</div>
		<div class = "modal fade" id = "rejection-modal" tabindex = "-1" role = "dialog"
		     aria-labelledby = "myModalLabel">
			<div class = "modal-dialog" role = "document">
				<div class = "modal-content">
					<div class = "modal-header">
						<button type = "button" class = "close" data-dismiss = "modal" aria-label = "Close"><span
									aria-hidden = "true">&times;</span></button>
						<h4 class = "modal-title" id = "myModalLabel">Reason to reject?</h4>
					</div>
					<div class = "modal-body">
						{!! Form::textarea('reason_to_reject', null, ['id' => 'reason-to-reject', 'class' => 'form-control', 'placeholder' => 'Write the reason to reject']) !!}
					</div>
					<div class = "modal-footer">
						<button type = "button" class = "btn btn-default" data-dismiss = "modal">Close</button>
						<button type = "button" class = "btn btn-danger" id = "do-reject">Reject</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		var form = null;

		$(function ()
		{
			$('[data-toggle="tooltip"]').tooltip();

			var totalQuantity = 0;
			$("table#batch-items-table tbody tr").each(function ()
			{
				totalQuantity += parseInt($(this).find('td:eq(4)').text());
			});

			$("table#batch-items-table tfoot td#item-quantity-in-total").text("Total quantity: " + totalQuantity);
		});
		$("a.reject").on('click', function (event)
		{
			event.preventDefault();
			form = $("form#station-action");
			var value = $(this).closest('tr').attr('data-id');
			$("<input type='hidden' value='' />")
					.attr("name", "item_id")
					.attr("value", value)
					.appendTo($("form#station-action"));
			$("<input type='hidden' value='' />")
					.attr("name", "action")
					.attr("value", 'reject')
					.appendTo($("form#station-action"));

			$("#rejection-modal").modal('show');
			/*var answer = confirm('Are you sure to reject?');
			 if ( answer ) {
			 var value = $(this).closest('tr').attr('data-id');
			 $("<input type='hidden' value='' />")
			 .attr("name", "item_id")
			 .attr("value", value)
			 .appendTo($("form#station-action"));
			 $("<input type='hidden' value='' />")
			 .attr("name", "action")
			 .attr("value", 'reject')
			 .appendTo($("form#station-action"));
			 $("form#station-action").submit();
			 }*/
		});
		$("button#reject-all").on('click', function (event)
		{
			event.preventDefault();
			form = $("form#action_changer");
			var value = 'reject'
			$("input[name='action']").val(value);
			$("#rejection-modal").modal('show');
			/*var answer = confirm('Are you sure to reject this batch?');
			 if ( answer ) {
			 var value = 'reject'
			 $("input[name='action']").val(value);
			 $("form#action_changer").submit();
			 }*/
		});

		$("#do-reject").on('click', function ()
		{
			var rejection_message = $("#reason-to-reject").val().trim();
			if ( !rejection_message ) {
				alert('Rejection message cannot be empty!');
				return false;
			}
			$("<input type='hidden' value='' />")
					.attr("name", "rejection_message")
					.attr("value", rejection_message)
					.appendTo($(form));
			$(form).submit();
		});

		$("a.done").on('click', function (event)
		{
			event.preventDefault();
			var value = $(this).closest('tr').attr('data-id');
			$("<input type='hidden' value='' />")
					.attr("name", "item_id")
					.attr("value", value)
					.appendTo($("form#station-action"));
			$("<input type='hidden' value='' />")
					.attr("name", "action")
					.attr("value", 'done')
					.appendTo($("form#station-action"));
			$("form#station-action").submit();
		});
		$("button#done-all").on('click', function (event)
		{
			event.preventDefault();
			var value = 'done'
			$("input[name='action']").val(value);
			$("form#action_changer").submit();
		})
	</script>
</body>
</html>