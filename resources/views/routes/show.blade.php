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
		td {
			/*width: 1px;*/
			white-space: nowrap;
		}

		td.description {
			white-space: pre-wrap;
			word-wrap: break-word;
			max-width: 300px;
			min-width: 250px !important;
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
	<div class = "container" style="min-width: 1550px; margin-left: 10px;">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('/items/grouped')}}">Batch list</a></li>
			<li class = "active">Batch View</li>
		</ol>
		@include('includes.error_div')
		<div class = "col-xs-12">
			@if($items)
				<div class = "col-xs-8">
					<p>Batch: # <span>{{$batch_number}}</span></p>
					<a href = "{{url('exports/batch/'.$batch_number)}}">Export batch</a>
					<p>Batch creation date: <span>{{substr($items[0]->batch_creation_date, 0, 10)}}</span></p>

					{!! Form::open(['method'=>'post', 'id' => 'action_changer']) !!}
					{!! Form::hidden('action', null) !!}
					{!! Form::close() !!}

					<p>Status: {!! Form::select('status', $statuses, $items[0]->item_order_status, ['disabled' => 'disabled']) !!}</p>
					<p>Template:
						<a href = "{{url(sprintf("/templates/%d", $route->template->id))}}">{!! $route->template->template_name !!}</a>
					</p>

					<p>Route: <a href = "{{ url(sprintf("/batch_routes#%s", $route['batch_code'] )) }}"
											   target = "_blank">{{$route['batch_code']}}</a> / {{$route['batch_route_name']}} => {!! $stations !!}</p>
					<p>Department: {{ $department_name }}</p>

					{!! Form::open(['url' =>  url(sprintf("/items/%d", $batch_number)), 'method' => 'put', 'id' => 'chabgeBatchStation']) !!}
					{!! Form::hidden('current_station_name', $current_batch_station->station_name, ['id' => 'current_station_name']) !!}
					{!! Form::close() !!}

					<p>Current Station: {!! Form::select('station', $route['stations']->lists('station_description', 'station_name')->prepend('Select a station', ''), $items[0]->station_name, array('id' => 'station')) !!}</p>

				</div>
				<div class = "col-xs-4">
					{!! \Monogram\Helper::getHtmlBarcode($batch_number) !!}
					<br />
					<a href = "{{url(sprintf("prints/batches?batch_number[]=%s&station=%s", $batch_number, $current_batch_station->station_name))}}"
					   target = "_blank">Print batch</a>
					/
					<a href = "{{url(sprintf('prints/batch_packing?batch_number[]=%s&station=%s',$batch_number, $current_batch_station->station_name))}}"
					   target = "_blank">Print packing slip</a>
				</div>
				<div class = "col-xs-12">
					<table class = "table table-bordered" id = "batch-items-table">
						<thead>
						<tr>
							<th>
								<!-- <button type = "button" class = "btn btn-danger" id = "reject-all">Reject all</button>  -->
							</th>
							<th>SL#</th>
							<th>
								Order
								<br />
								Item barcode
							</th>
							<th>Image</th>
							<th>Date</th>
							<th>Qty.</th>
							<th>SKU</th>
							<th>Item name</th>
							<th>Options</th>
							<th></th>
							<!-- th>
								<button type = "button" class = "btn btn-success" id = "done-all">Done all</button>
							</th -->
						</tr>
						</thead>
						<tbody>
						@foreach($items as $item)
							<tr data-id = "{{$item->id}}">
								<td>
									<a href = "#" class = "btn btn-danger reject">Reject</a>
								</td>
								<td>{{$count++}}</td>
								<td>
									Order# <a href = "{{url(sprintf('/orders/details/%s', $item->order->order_id))}}"
									   target = "_blank">{{\Monogram\Helper::orderNameFormatter($item->order)}}</a>
									<br />
									{!! \Monogram\Helper::getHtmlBarcode(sprintf("%s", $item->id)) !!}
									<br />
									Item# {{$item->id}}
									<br />

									@if(!in_array ( $current_batch_station->station_name, \Monogram\Helper::$shippingStations ))
										<a href = "#" class = "move_to_shipping">Move 2 Shipping</a>
									@endif

								</td>
								<td><a href = "{{ $item->item_url }}" target = "_blank"><img
												src = "{{$item->item_thumb}}" /></a>
								</td>
								<td>{{substr($item->order->order_date, 0, 10)}}</td>
								<td>{{$item->item_quantity}}</td>
								<td>
									<a href = "{{ url(sprintf("logistics/sku_show?store_id=%s&search_for=%s&search_in=child_sku", $item->store_id, $item->child_sku)) }}"
									   target = "_blank">{{$item->child_sku}}</a>
								</td>
								<td align="left">
									{{$item->item_description}}
																		<br/>
									@if($item->supervisor_message)
										{{ $item->supervisor_message }}
										<br />
									@endif
									@if($item->tracking_number)

									<div style="color: red;">
									Don't Make this Item again.<BR>
										TRK# {{ $item->tracking_number }}
									</div>
									@endif
								</td>
								<td>{!! Form::textarea('nothing', \Monogram\Helper::jsonTransformer($item->item_option), ['rows' => '4', 'cols' => '20', /*"style" => "border: none; width: 100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;"*/]) !!}</td>
								<!-- td>
									<a href = "#" class = "btn btn-success done">Done</a>
								</td -->
								<td>
									<a href = "#" class = "btn btn-success complete">Complete</a>
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
						<div class = "form-group">
							{!! Form::select('reason_to_reject', $rejection_reasons?: [], null, ['id' => 'reason-to-reject', 'class' => 'form-control']) !!}
						</div>
						<div class = "form-group">
							{!! Form::textarea('message_to_reject', null, ['id' => 'message-to-reject', 'rows' => 2, 'class' => 'form-control', 'placeholder' => 'Write the message to reject']) !!}
						</div>
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

			// By Jewel 04-25-2016
			$("#station").on('change', function (event)
			{
				var value = $(this).val();

				if ( value === '' ) {
					alert("Not a valid batch");
					return;
				}

				var form = $("form#chabgeBatchStation");
				var formUrl = form.attr('action');

				var token = $(form).find('input[name="_token"]').val();
				var current_station_name = $(form).find('input[name="current_station_name"]').val();
// alert(formUrl);
// return false;
				$.ajax({
					method: 'PUT', url: formUrl, data: {
						_token: token, station_name: value, current_station_name: current_station_name,
					}, success: function (data, textStatus, xhr)
					{
						var route = (data && data.data && data.data.route) || '/items/grouped';
						location.href = route;
					}, error: function (xhr, textStatus, errorThrown)
					{
						//alert(errorThrown);
						alert('Could not update product route');
					}
				});
			});
		});


		$("a.move_to_shipping").on('click', function (event)
		{
			event.preventDefault();
			var value = $(this).closest('tr').attr('data-id');
			$("<input type='hidden' value='' />")
					.attr("name", "item_id")
					.attr("value", value)
					.appendTo($("form#station-action"));
			$("<input type='hidden' value='' />")
					.attr("name", "action")
					.attr("value", 'move_to_shipping')
					.appendTo($("form#station-action"));
			$("form#station-action").submit();
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
		});

		$("button#reject-all").on('click', function (event)
		{
			event.preventDefault();
			form = $("form#action_changer");
			var value = 'reject'
			$("input[name='action']").val(value);
			$("#rejection-modal").modal('show');
		});

		$("#do-reject").on('click', function ()
		{
			var rejection_message = $("#message-to-reject").val().trim();
			if ( !rejection_message ) {
				alert('Rejection message cannot be empty!');
				return false;
			}

			var rejection_reason = $("#reason-to-reject").val();
			if ( !rejection_reason || rejection_reason == 0 ) {
				alert('Rejection reason cannot be unselected!');
				return false;
			}

			$("<input type='hidden' value='' />")
					.attr("name", "rejection_reason")
					.attr("value", rejection_reason)
					.appendTo($(form));

			$("<input type='hidden' value='' />")
					.attr("name", "rejection_message")
					.attr("value", rejection_message)
					.appendTo($(form));

			$(form).submit();
		});


		$("a.complete").on('click', function (event){
			$(this).closest('tr').css("background-color", "#4E9563");
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