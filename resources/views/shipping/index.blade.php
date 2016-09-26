@setvar($shipped = intval($request->get('shipped', 0)))
		<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Ships list - @if($shipped == 0) Not Shipped @elseif($shipped == 1 ) Shipped @else Not Shipped @endif</title>
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
			<li>Shipping list</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')

		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get', 'url' => url('shipping'), 'id' => 'search-order']) !!}
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
				{!! Form::hidden('shipped', $request->get('shipped','0'), ['id'=>'shipped']) !!}
			<div class = "form-group col-xs-2">
				<label for = "" class = ""></label>
				{!! Form::submit('Search', ['id'=>'search', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-primary form-control']) !!}
			</div>

			<div class = "form-group col-xs-2">
				<label for = "" class = ""></label>
				{!! Form::button('Reset', ['id'=>'reset', 'type' => 'reset', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-warning form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>

		<h3 class = "page-header">
			Shipping list @if(count($ships) > 0 ) ({{ $ships->total() }} items found / {{$ships->currentPage()}} of {{$ships->lastPage()}} pages) @endif
			<a href = "/items/waiting_for_another_item" style = "font-size: 12px;">Go to waiting for another items</a>
			<a href = "{{ url(sprintf("/shipping?shipped=0")) }}"
			   class = "btn btn-primary btn-sm @if($shipped != 1) disabled @endif"
			   style = "font-size: 12px;">{{ $tracking_number_not_assigned }} items yet not shipped</a>
			<a href = "{{ url(sprintf("/shipping?shipped=1")) }}"
			   class = "btn btn-success btn-sm @if($shipped == 1) disabled @endif"
			   style = "font-size: 12px;">{{ $tracking_number_assigned }} items are shipped</a>
		</h3>

		@if(count($ships) > 0)
			<table class = "table table-bordered">
				<tr>
					<th>Shipping<br/>Order<br/>number</th>
					<th>Mail class</th>
					<th>Batch</th>
					<th>Item id</th>
					<th>SKU</th>
					<th>Image</th>
					<th>Name</th>
					<th>Qty</th>
					<th>Tracking #</th>
					<th>Length</th>
					<th>Height</th>
					<th>Width</th>
					<th>Billed weight</th>
					<th>Actual weight</th>
					<th>Package shape</th>
					<th>Tracking type</th>
					<th>Name</th>
					<th>Company</th>
					<th>Address 1</th>
					<th>Address 2</th>
					<th>City</th>
					<th>State</th>
					<th>Postal code</th>
					<th>Country</th>
					<th>Email</th>
					<th>Phone</th>
				</tr>
				@foreach($ships->groupBy('unique_order_id') as $groupByUniqueOrderId)
					@setvar($count = $groupByUniqueOrderId->count())
					@setvar($ship = $groupByUniqueOrderId->first())
					<tr data-id = "{{ $ship->id }}" class = "text-center">
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">
							<a href = "{{url(sprintf("orders/details/%s", $ship->order_number))}}"
							   target = "_blank">{{ $ship->unique_order_id }}</a>
						</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->mail_class}}</td>
					@if($ship->item)
						<td>
							@if($ship->item->batch_number)
								<a href = "{{ url(sprintf("/batches/%d/%s", $ship->item->batch_number, $ship->item->station_name)) }}"
								   target = "_blank">{{ $ship->item->batch_number }}</a>
							@else
								{{ $ship->item->batch_number }}
							@endif
						</td>
						<td>{{ $ship->item->id }}</td>
						<td>
							<a href = "{{ url(sprintf("/products?search_for=%s&search_in=product_model", $ship->item->item_code)) }}"
							   target = "_blank">{{ $ship->item->item_code }}
							</a>
						</td>
						<td><img src = "{{ $ship->item->item_thumb }}" /></td>
						<td>{{ $ship->item->item_description }}</td>
						<td>{{ $ship->item->item_quantity }}</td>
						<td>
							@if($ship->item->tracking_number)
								{{ $ship->item->tracking_number }}
								{{ $ship->transaction_datetime }}
								<br>
								<a href = "{{ url(sprintf("/remove_shipping?tracking_numbers[]=%s&order_number=%s", $ship->item->tracking_number,$ship->order_number )) }}">Back to shipping</a>
							@else
								<br>
								{!! Form::text('tracking_number', $ship->item->tracking_number, ['class'=> 'form-control', 'id' => 'tracking_number', 'style' => 'min-width: 250px;']) !!}
								<a class = "update" href = "#" >Manual Tracking # Update</a>

								{!! Form::open(['url' => url('/shipping_update'), 'method' => 'put', 'id' => 'shipping_update']) !!}
								{!! Form::hidden('tracking_number_update', null, ['id' => 'tracking_number_update']) !!}
								{!! Form::hidden('order_number_update', $ship->order_number, ['id' => 'order_number_update']) !!}
								{!! Form::close() !!}
							@endif



						</td>
					@else
						<td colspan="7">
							<div style="color: red;">
								Contact with Shlomi, Some wrong operation happen here.
							</div>
						</td>
					@endif

						<td>{{$ship->length}}</td>
						<td>{{$ship->height}}</td>
						<td>{{$ship->width}}</td>
						<td>{{$ship->billed_weight}}</td>
						<td>{{$ship->actual_weight}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->package_shape}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->tracking_type}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->name}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->company}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->address1}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->address2}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->city}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->state_city}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->postal_code}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->country}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->email}}</td>
						<td rowspan = "{{ $count }}" style = "vertical-align: middle">{{$ship->phone}}</td>
					</tr>

					@foreach($groupByUniqueOrderId->slice(1) as $ship)
						<tr class = "text-center">
							<td>
								@if($ship->item->batch_number)
									<a href = "{{ url(sprintf("/batches/%d/%s", $ship->item->batch_number, $ship->item->station_name)) }}"
									   target = "_blank">{{ $ship->item->batch_number }}</a>
								@else
									{{ $ship->item->batch_number }}
								@endif
							</td>
							<td>{{ $ship->item->id }}</td>
							<td>
								<a href = "{{ url(sprintf("/products?search_for=%s&search_in=product_model", $ship->item->item_code)) }}"
								   target = "_blank">{{ $ship->item->item_code }}
								</a>
							</td>
							<td><img src = "{{ $ship->item->item_thumb }}" /></td>
							<td>{{ $ship->item->item_description }}</td>
							<td>{{ $ship->item->item_quantity }}</td>
							<td>
								{{--
								{!! Form::text('tracking_number', $ship->item->tracking_number, ['class'=> 'form-control', 'id' => 'tracking_number', 'style' => 'min-width: 250px;']) !!}
								<a href = "{{ url(sprintf("/update_tracking?u_item_id=%s", $ship->item->id  )) }}">Tracking # Update</a>
								--}}

								{{ $ship->item->tracking_number ?: "N/A" }}

							</td>
							<td>{{ $ship->length}}</td>
							<td>{{ $ship->height}}</td>
							<td>{{ $ship->width}}</td>
							<td>{{ $ship->billed_weight}}</td>
							<td>{{ $ship->actual_weight}}</td>
						</tr>
					@endforeach

				@endforeach
			</table>

			<div class = "col-xs-12 text-center">
				{!! $ships->appends($request->all())->render() !!}
			</div>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No ships found.</h3>
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


		$("a.update").on('click', function (event)
		{
			event.preventDefault();
			var tr = $(this).closest('tr');
			var id = tr.attr('data-id');
			var tracking_number_update = tr.find('input').eq(0).val();

			tr.find("input#tracking_number_update").val(tracking_number_update);

			var form = tr.find("form#shipping_update");
			var url = form.attr('action');

// 			console.log(form.attr('action', url.replace('id', id)));
// 			return false;

			form.attr('action', url.replace('id', id));
			form.submit();
			});


	</script>
</body>
</html>