<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Waiting for another items</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
	<style>
		.blank_row {
			height: 30px !important; /* Overwrite any previous rules */
			background-color: #FFFFFF;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li>Waiting for another items</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')

		@if(count($items) > 0)
			<h3 class = "page-header">
				Waiting for another items
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Order number</th>
					<th>Item id</th>
					<th>Item SL#</th>
					<th>Batch</th>
					<th>SKU</th>
					<th>Image</th>
					<th>Name</th>
					<th>Qty</th>
					<th>Current station</th>
					<th>Checkbox</th>
					{{--<th>Partial Shipping</th>--}}
				</tr>
				{{-- All the orders are grabbed where, having more than 1 items in an order --}}
				@setvar($grand_total = 0)
				@setvar($has_table_row = false)
				@foreach($items as $current)
					@setvar($count = 1)
					@setvar($sub_total = 0)
					@setvar($multiple_item_rows = \Monogram\Helper::getAllOrdersFromOrderId($current->order_id))
					@setvar($order_id = null)
					@if($multiple_item_rows)
						@setvar($order = $multiple_item_rows->first()->order)
						@setvar($has_table_row = true)
						<tr class = "blank_row">
							<td colspan = "10"></td>
						</tr>
						@foreach( $multiple_item_rows as $item)
							@setvar($sub_total += $item->item_quantity)
							<tr data-item-id = "{{ $item->id }}">
								<td>
									<a href = "{{ url(sprintf("/orders/details/%s", $item->order_id)) }}"
									   target = "_blank">{{ \Monogram\Helper::orderNameFormatter($item->order) }}</a>
								</td>
								<td>{{ $item->id }}</td>
								<td>{{ $count++ }}</td>
								<td>
									@if($item->station_name)
										<a href = "{{ url(sprintf("/batches/%d/%s", $item->batch_number, $item->station_name)) }}"
										   target = "_blank">{{ $item->batch_number }}</a>
									@else
										{{ $item->batch_number }}
									@endif
								</td>
								<td>
									<a href = "{{ url(sprintf("/products?search_for=%s&search_in=product_model", $item->item_code)) }}"
									   target = "_blank">{{$item->item_code}}</a>
								</td>
								<td><img src = "{{ $item->item_thumb }}" /></td>
								<td>{{ $item->item_description }}</td>
								<td>{{ $item->item_quantity }}</td>
								<td>{{ $item->station_name }}</td>
								<td class = "text-center">
									@if($item->reached_shipping_station)
										{!! Form::checkbox("partial-shipping-checkbox", $item->id, false, ['class' => 'partial-shipping-checkbox']) !!}
									@endif
								</td>
								{{--<td>{{ "" }}</td>--}}
							</tr>
						@endforeach
						@setvar($grand_total += $sub_total)
						<tr>
							<td>Shipping unique id</td>
							<td>{{ \Monogram\Helper::generateShippingUniqueId( $order ) }}</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>Sub total</td>
							<td>{{ $sub_total }}</td>
							<td></td>
							<td><a class = "btn btn-success push-shipping" href = "#">Push shipping</a></td>
							{{--<td></td>--}}
						</tr>
					@endif
				@endforeach
				@if($has_table_row)
					<tr class = "blank_row">
						<td colspan = "10"></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>Grand total</td>
						<td>{{ $grand_total }}</td>
						<td></td>
						<td></td>
						{{--<td></td>--}}
					</tr>
				@endif
			</table>

			<div class = "col-xs-12 text-center">
				{{--{!! $ships->appends($request->all())->render() !!}--}}
			</div>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No items moved to shipping found.</h3>
				</div>
			</div>
		@endif
		{!! Form::open(['method' => 'post', 'url' => '/items/partial_shipping', 'id' => 'partial-shipping-form']) !!}
		{!! Form::close() !!}
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
		var form = null;
		var post_form = false;
		function createHiddenInput (value)
		{
			var input = "<input type='hidden' name='item_id[]' value='" + value + "' />";
			form.append(input);
			post_form = true;
		}
		$("a.push-shipping").on('click', function (event)
		{
			event.preventDefault();
			form = $("form#partial-shipping-form");
			$(".partial-shipping-checkbox").each(function ()
			{
				if ( $(this).prop('checked') ) {
					var value = $(this).val();
					createHiddenInput(value);
				}
			});

			if ( post_form ) {
				form.submit();
			} else {
				alert("Select few checkbox to post.")
			}
		});
	</script>

</body>
</html>