<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Batch details {{$batch_number}}</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.3/css/bootstrap-select.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
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

		td textarea {
			border: none;
			width: auto;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('/items/grouped')}}">Batch list</a></li>
			<li class = "active">Batch details</li>
		</ol>
		<div class = "col-xs-12">
			@if($items)
				<div class = "col-xs-8">
					<p>Batch: # <span>{{$batch_number}}</span></p>
					<a href = "{{url('exports/batch/'.$batch_number)}}">Export batch</a>
					<p>Batch creation date: <span>{{substr($items[0]->batch_creation_date, 0, 10)}}</span></p>
					{{--<div class = "col-xs-12"> </div>
					<div class = "col-xs-12">
						<div class = "btn-group" role = "group" aria-label = "...">
						</div>
						{!! Form::open(['method'=>'post', 'id' => 'action_changer']) !!}
						{!! Form::hidden('action', null) !!}
						{!! Form::close() !!}
					</div>--}}
					<p>Status: {!! Form::select('status', $statuses, $items[0]->item_order_status, ['disabled' => 'disabled']) !!}</p>
					<p>Template:
						<a href = "{{url(sprintf("/templates/%d", $route->template->id))}}">{!! $route->template->template_name !!}</a>
					</p>
					<p>Route: {{$route['batch_code']}} / {{$route['batch_route_name']}} => {!! $stations !!}</p>
					{{--<p>Department: {{ $department_name }}</p>--}}
					{{--<p>Current Station: {!! Form::select('station', $route['stations']->lists('station_description', 'station_name')->prepend('Select a station', ''), $items[0]->station_name, ['disabled' => 'disabled']) !!}</p>--}}
					{{-- {!! Form::open(['url' => url(sprintf("batches/%d", $items[0]->batch_number)), 'method' => 'put', 'class' => 'form-horizontal']) !!}
					<p>Station: {!! Form::select('station', $route['stations']->lists('station_description', 'station_name')->prepend('Select a station', ''), $items[0]->station_name, []) !!}</p>
					{!! Form::submit('Change station', ['id' => 'change-status',]) !!}
					{!! Form::close() !!} --}}
				</div>
				<div class = "col-xs-4">
					{!! \Monogram\Helper::getHtmlBarcode(sprintf("%s", $batch_number)) !!}
					<a href = "{{url('prints/batches?batch_number[]='.$batch_number)}}"
					   target = "_blank">Print batch</a>
				</div>
				<div class = "col-xs-12">
					<table class = "table table-bordered" id = "batch-items-table">
						<thead>
						<tr>
							<th>SL#</th>
							<th>
								Order
								<br />
								Item barcode
							</th>
							<th>Current station</th>
							{{--<th>Order</th>--}}
							<th>Image</th>
							<th>Order date</th>
							<th>Qty.</th>
							<th>SKU</th>
							<th>Item name</th>
							<th>Options</th>
						</tr>
						</thead>
						<tbody>
						@foreach($items as $item)
							<tr data-id = "{{$item->id}}">
								<td>{{$count++}}</td>
								<td>
									<a href = "{{url(sprintf('/orders/details/%s', $item->order->order_id))}}"
									   target = "_blank">{{$item->order->short_order}}</a> - {{$item->id}}
									{!! \Monogram\Helper::getHtmlBarcode(sprintf("%s-%s", $item->order->short_order, $item->id)) !!}
								</td>
								<td>
									@if($item->station_details)
										<a href = "{{ url(sprintf("/batches/%s/%s", $item->batch_number, $item->station_details->station_name)) }}"
										   data-toggle = "tooltip" data-placement = "top"
										   title = "{{ $item->station_details->station_description }}">{{$item->station_details->station_name}}</a>
									@else
										-
									@endif
								</td>
								{{--<td>
									<a href = "{{url('/orders/details/'.$item->order->order_id)}}">{{$item->order->short_order}}</a>
								</td>--}}
								{{--<td>{!! \Monogram\Helper::getHtmlBarcode(sprintf("%s", $item->order->short_order)) !!}</td>--}}
								{{--<td><img src = "{{$item->item_thumb}}" /></td>--}}
								<td><a href = "{{ $item->product->product_url }}" target = "_blank"><img
												src = "{{$item->item_thumb}}" /></a>
								<td>{{substr($item->order->order_date, 0, 10)}}</td>
								<td>{{$item->item_quantity}}</td>
								<td>{{$item->item_code}}</td>
								<td class = "description">{{$item->item_description}}</td>
								<td>{!! Form::textarea('nothing', \Monogram\Helper::jsonTransformer($item->item_option), ['rows' => '3', 'cols' => '20',]) !!}</td>
								{{--<td>{{\Monogram\Helper::jsonTransformer($item->item_option)}}</td>--}}
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
				totalQuantity += parseInt($(this).find('td:eq(5)').text());
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