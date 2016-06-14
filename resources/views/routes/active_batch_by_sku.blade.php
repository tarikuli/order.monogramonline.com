<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Active batch by SKU group</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.3/css/bootstrap-select.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
	@include('includes.header_menu')
	<div class = "container" style="min-width: 1550px; margin-left: 10px;">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">Active batch by SKU group</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get']) !!}
			{{--<div class = "form-group col-xs-2">
				<label for = "batch">Batch#</label>
				{!! Form::text('batch', $request->get('batch'), ['id'=>'batch', 'class' => 'form-control', 'placeholder' => 'Search in batch']) !!}
			</div>--}}

			{{--<div class = "form-group col-xs-3">
				<label for = "route">Route</label>
				{!! Form::select('route', $routes, $request->get('route'), ['id'=>'route', 'class' => 'form-control']) !!}
			</div>--}}
			<div class = "form-group col-xs-3">
				<label for = "route">Station</label>
				{!! Form::select('station', $stations, $request->get('station', ''), ['id'=>'station', 'class' => 'form-control']) !!}
			</div>
			{{--<div class = "form-group col-xs-2">
				<label for = "status">Status</label>
				{!! Form::select('status', $statuses, $request->get('status'), ['id'=>'status', 'class' => 'form-control']) !!}
			</div>--}}
			<div class = "form-group col-xs-2">
				<label for = "" class = ""></label>
				{!! Form::submit('Search', ['id'=>'search', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-primary form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>
		<div class = "col-xs-12">
			@if(count($rows))
				<table class = "table">
					<tr>
						<th>Image</th>
						<th>SKU</th>
						<th>Name</th>
						<th>Quantity</th>
						<th>Min-Order date</th>
						<th>Route</th>
						<th># To</th>
						<th>Assign station</th>
						<th>Action</th>
					</tr>
					{{--{!! Form::open(['url' => url('prints/batches'), 'method' => 'get', 'id' => 'batch_list_form']) !!}--}}
					@foreach($rows as $row)
						<tr data-sku = "{{ $row['sku'] }}" id = "{{ $row['current_station_anchor'] }}">
							<td>
								<img src = "{{ $row['item_thumb'] }}"  height="42" width="42" />
							</td>
							<td>{{ $row['sku'] }}</td>
							<td class = "description">{{ $row['item_name'] }}</td>
							<td>{{ $row['item_count'] }}</td>
							<td>{{ $row['min_order_date'] }}</td>
							<td style = "min-width: 250px;">{{ $row['route'] }}</td>


							<td>
									{!! Form::open(['url' => url(sprintf("change_station_by_sku/%s", $row['redriec_sku']))]) !!}
									{!! Form::number('item_to_shift', $row['item_count'], ['class' => 'form-control', 'style' => 'min-width: 70px; max-width: 100px;']) !!}
							</td>
							<td>
									{!! Form::select('batch_stations', $row['batch_stations'], null, ['id'=>'batch_stations', 'class' => 'form-control', 'style' => 'min-width: 190px;']) !!}
							</td>
							<td>
									{!! Form::button('Update', ['id' => 'update_sku_station', 'class' => 'btn btn-success']) !!}
									{!! Form::hidden('current_station_name', $current_station_name, ['id' => 'current_station_name']) !!}
									{!! Form::close() !!}
							</td>


						</tr>
					@endforeach
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td>{{ $total_count }}</td>
						<td></td>
						<td></td>
					</tr>
					{{--<tr>
						<td colspan = "11">
							{!! Form::button('Select / Deselect all', ['id' => 'select_deselect', 'class' => 'btn btn-link']) !!}
							{!! Form::button('Print batches', ['id' => 'print_batches', 'class' => 'btn btn-link']) !!}
							{!! Form::button('Packing Slip', ['id' => 'packing_slip', 'class' => 'btn btn-link']) !!}
						</td>
					</tr>--}}
					{{--{!! Form::close() !!}--}}
				</table>
				<div class="col-md-12 text-center">
					{!! $pagination !!}
				</div>
			@else
				<div class = "alert alert-warning">No data is available.</div>
			@endif
		</div>
		<div class = "col-xs-12 text-center">
			{{--{!! $items->render() !!}--}}
		</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		$(function ()
		{
			$('[data-toggle="tooltip"]').tooltip();
		});
		$("button#print_batches").on('click', function (event)
		{
			var url = "{{ url('/prints/batches') }}";
			setFormUrlAndSubmit(url);
		});
		$("button#packing_slip").on('click', function (event)
		{
			var url = "{{ url('/prints/batch_packing') }}";
			setFormUrlAndSubmit(url);
		});

		function setFormUrlAndSubmit (url)
		{
			var form = $("form#batch_list_form");
			$(form).attr('action', url);
			$(form).submit();
		}
		var state = false;

		$("button#update_sku_station").on('click', function (){

			event.preventDefault();
			var selected = parseInt($(this).closest("tr").find("select#batch_stations").val());
			if ( selected !== 0 ) {
				$(this).closest("tr").find("form").submit();
			}else if ( selected == 0 ) {
				alert("Please Select station");
			}
		});

// 		$("select#batch_stations").on('change', function (event)
// 		{
// 			event.preventDefault();
// 			var selected = parseInt($(this).val());
// 			if ( selected !== 0 ) {
// 				$(this).closest('form').submit();
// 			}
// 		});

		$("input[name='item_to_shift']").on('keypress', function (event)
		{
			if ( event.keyCode == 13 ) {
				return false;
			}
		});
	</script>
</body>
</html>
