<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Batch list</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.3/css/bootstrap-select.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="/assets/css/chosen.min.css">
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
	<div class = "container" style="min-width: 1550px; margin-left: 10px;">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">Batch list</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get']) !!}
			<div class = "form-group col-xs-3">
				<label for = "route">Route</label>
				{!! Form::select('route', $routes, $request->get('route'), ['id'=>'route', 'class' => 'form-control chosen_txt']) !!}
			</div>
			<div class = "form-group col-xs-3">
				<label for = "route">Station</label>
				{!! Form::select('station', $stationsList, session('station', 'all'), ['id'=>'station', 'class' => 'form-control chosen_txt']) !!}
			</div>
			<div class = "form-group col-xs-3">
				<label for = "start_date">Last Scan Start date</label>
				<div class = 'input-group date' id = 'start_date_picker'>
					{!! Form::text('start_date', $request->get('start_date'), ['id'=>'start_date', 'class' => 'form-control', 'placeholder' => 'Enter start date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
			<div class = "form-group col-xs-3">
				<label for = "end_date">Last Scan End date</label>
				<div class = 'input-group date' id = 'end_date_picker'>
					{!! Form::text('end_date', $request->get('end_date'), ['id'=>'end_date', 'class' => 'form-control', 'placeholder' => 'Enter end date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
			<div class = "form-group col-xs-3">
				<label for = "batch">Batch#</label>
				{!! Form::text('batch', $request->get('batch'), ['id'=>'batch', 'class' => 'form-control', 'placeholder' => 'Search in batch']) !!}
			</div>
			<div class = "form-group col-xs-2">
				<label for = "status">Status</label>
				{!! Form::select('status', $statuses, $request->get('status'), ['id'=>'status', 'class' => 'form-control']) !!}
			</div>
			
			
			<div class = "form-group col-xs-3">
				<label for = "order_start_date">Order Start date</label>
				<div class = 'input-group date' id = 'order_start_date'>
					{!! Form::text('order_start_date', $request->get('order_start_date'), ['id'=>'order_start_date', 'class' => 'form-control', 'placeholder' => 'Order start date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
			<div class = "form-group col-xs-3">
				<label for = "order_end_date">Order End date</label>
				<div class = 'input-group date' id = 'order_end_date'>
					{!! Form::text('order_end_date', $request->get('order_end_date'), ['id'=>'order_end_date', 'class' => 'form-control', 'placeholder' => 'Order end date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
			<div class = "form-group col-xs-2">
				<label for = "" class = ""></label>
				{!! Form::submit('Search', ['id'=>'search', 'style' => 'margin-top: 5px;', 'class' => 'btn btn-primary form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>
		<div class = "col-xs-12">
			@if(count($rows))
				<h4 class = "page-header">
					Total# {{ $items->total() }} Batches and {{ $itemsTotalQty }} Lines found out of {{$items->lastPage()}} pages / Page# {{$items->currentPage()}} Total {{ $total_itemss }} Lines found 
				</h4>
				<table id="summary_table" class="table" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th style="width:15px;">
							<img src = "{{url('/assets/images/spacer.gif')}}"
							     width = "50" height = "20" border = "0">
						</th>
						<th>Batch#</th>
						<th>MinOrderDate</th>
						<th>Batch creation date</th>
						<th>Route</th>
						<th style="width:30px;">Lines</th>
						<th style="width:200px;">Current station</th>
						<th>Last Scan Date</th>
						<th>Image</th>
						<th style="width:250px;">Child SKU</th>
						<th>Status</th>
						<th>Batch details</th>
					</tr>
				</thead>
				 <tbody>
					{!! Form::open(['url' => url('prints/batches'), 'method' => 'get', 'id' => 'batch_list_form']) !!}
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					@foreach($rows as $row)
						<tr>
							<td>
								<input type = "checkbox" name = "batch_number[]" class = "checkbox"
								       value = "{{$row['batch_number_c_box']}}" />
							</td>
							<td>
								<a href = "{{url(sprintf('batches/%d/%s',$row['batch_number'], $row['current_station_name']))}}">{{$row['batch_number']}}</a>
							</td>
							<td>{{$row['min_order_date']}}</td>
							<td>{{$row['batch_creation_date']}}</td>
							<td>
								<span data-toggle = "tooltip" data-placement = "top" title = "{{$row['route_name']}}">{{$row['route_code']}}</span>
							</td>
							<td>
								@if($row['current_station_item_count'] == $row['lines'])
									{{$row['current_station_item_count']}}
								@else
									{{$row['current_station_item_count']}} / {{$row['lines']}}
								@endif
							</td>
							<td>
								<span data-toggle = "tooltip" data-placement = "top"
								      title = "{{ $row['current_station_description'] }}">{{ $row['current_station_name'] }}<br>{{ $row['current_station_description'] }}</span>
							</td>
							<td>{{ $row['current_station_since'] }}</td>
							<td>
							  <span data-toggle = "tooltip" data-placement = "top"
							          title = "{{ $row['child_sku'] }}">
							  	<img src = "{{ $row['item_thumb'] }}" width = "70" height = "70" />
							  </span>

							</td>
							<td>{{ $row['child_sku'] }}</td>
							<td>{{$row['batch_status']}}</td>
							<td>
								<a href = "{{url(sprintf('batch_details/%d',$row['batch_number']))}}"
								   target = "_blank">Batch details</a>
							</td>
						</tr>
					@endforeach
					<tbody>
					<tfoot>
					<tr>
						<td colspan = "12">
							{!! Form::button('Select / Deselect all', ['id' => 'select_deselect', 'class' => 'btn btn-link']) !!}
							{!! Form::button('Bulk Export', ['id' => 'export_batches', 'class' => 'btn btn-link']) !!}
							{!! Form::button('Print batches', ['id' => 'print_batches', 'class' => 'btn btn-link']) !!}
							{!! Form::button('Packing Slip', ['id' => 'packing_slip', 'class' => 'btn btn-link']) !!}
							{!! Form::button('Small Packing Slip', ['id' => 'small_packing_slip', 'class' => 'btn btn-link']) !!}
							@if(auth()->user()->roles->first()->id == 1)
								{!! Form::select('station_change', $stationsList, 'all', ['id'=>'station_change', 'class' => 'chosen_txt']) !!}
								{!! Form::button('Change Station', ['id' => 'change_station', 'class' => 'btn btn-link']) !!}
								{!! Form::button('Release Batch', ['id' => 'release_batch', 'class' => 'btn btn-link']) !!}
							@endif
						</td>
					</tr>
					</tfoot>
					{!! Form::close() !!}

				</table>
			@else
				<div class = "alert alert-warning">No batch is created.</div>
			@endif
		</div>
		<div class = "col-xs-12 text-center">
			{!! $items->appends(request()->all())->render() !!}
		</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.12.3.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script type = "text/javascript"
	        src = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
	<script type = "text/javascript" src = "https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script type = "text/javascript" src = "https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
	<script type = "text/javascript" src = "/assets/js/chosen.jquery.min.js"></script>	
	<script type = "text/javascript">
		var options = {
			format: "YYYY-MM-DD", maxDate: new Date()
		};
		$(function ()
		{
			$('#start_date_picker').datetimepicker(options);
			$('#end_date_picker').datetimepicker(options);
			$('#tracking_date_picker').datetimepicker(options);
			$('#order_start_date').datetimepicker(options);
			$('#order_end_date').datetimepicker(options);
			
		});
	</script>
	<script type = "text/javascript">
// 		$('#summary_table').DataTable({
// 			"paging":   false,
// 		});
		$(function ()
		{
			$('[data-toggle="tooltip"]').tooltip();
		});
		$("button#print_batches").on('click', function (event)
		{
			var url = "{{ url('/prints/batches') }}";
			setFormUrlAndSubmit(url);
		});

		$("button#export_batches").on('click', function (event)
		{
			var url = "{{ url('/exports/batchbulk') }}";
			setFormUrlAndSubmit(url);
		});

		$("button#packing_slip").on('click', function (event)
		{
			var url = "{{ url('/prints/batch_packing') }}";
			setFormUrlAndSubmit(url);
		});

		$("button#small_packing_slip").on('click', function (event)
		{
			var url = "{{ url('/prints/batch_packing_small') }}";
			setFormUrlAndSubmit(url);
		});

		$("button#release_batch").on('click', function (event)
		{
			event.preventDefault();
			var url = "{{ url('/items/release_batch') }}";
			setFormUrlAndSubmit(url);
		});

		$("button#change_station").on('click', function (event)
		{
			event.preventDefault();
			var url = "{{ url('/stations/bulk') }}";
			var form = $("form#batch_list_form");
			$(form).attr('action', url);
			$(form).attr('method', 'post');
			$(form).append('<input type="hidden" name="from_batch_list" value="from_batch_list" />');
			$(form).submit();
		});
		

		function setFormUrlAndSubmit (url)
		{
			var form = $("form#batch_list_form");
			$(form).attr('action', url);
			$(form).submit();
		}
		var state = false;

		$("button#select_deselect").on('click', function ()
		{
			state = !state;
			$(".checkbox").prop('checked', state);
		});

		$(".chosen_txt").chosen();

	</script>
</body>
</html>
