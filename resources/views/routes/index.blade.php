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
			<li class = "active">Batch list</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get']) !!}
			<div class = "form-group col-xs-2">
				<label for = "batch">Batch#</label>
				{!! Form::text('batch', $request->get('batch'), ['id'=>'batch', 'class' => 'form-control', 'placeholder' => 'Search in batch']) !!}
			</div>
			<div class = "form-group col-xs-3">
				<label for = "route">Route</label>
				{!! Form::select('route', $routes, $request->get('route'), ['id'=>'route', 'class' => 'form-control']) !!}
			</div>
			<div class = "form-group col-xs-3">
				<label for = "route">Station</label>
				{!! Form::select('station', $stations, session('station', 'all'), ['id'=>'station', 'class' => 'form-control']) !!}
			</div>
			<div class = "form-group col-xs-2">
				<label for = "status">Status</label>
				{!! Form::select('status', $statuses, $request->get('status'), ['id'=>'status', 'class' => 'form-control']) !!}
			</div>
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
						<th>
							<img src = "{{url('/assets/images/spacer.gif')}}"
							     width = "50" height = "20" border = "0">
						</th>
						<th>Batch#</th>
						<th>View batch</th>
						<th>Batch creation date</th>
						<th>Route</th>
						<th>Lines</th>
						<th>Current station</th>
						<th>Current station since</th>
						<th>Next station</th>
						<th>Status</th>
						<th>MinOrderDate</th>
					</tr>
					{!! Form::open(['url' => url('prints/batches'), 'method' => 'get', 'id' => 'batch_list_form']) !!}
					@foreach($rows as $row)
						<tr>
							<td>
								<input type = "checkbox" name = "batch_number[]" class = "checkbox"
								       value = "{{$row['batch_number']}}" />
							</td>
							<td>
								<a href = "{{url(sprintf('batches/%d/%s',$row['batch_number'], $row['current_station_name']))}}">{{$row['batch_number']}}</a>
							</td>
							<td>
								<a href = "{{url(sprintf('batch_details/%d',$row['batch_number']))}}"
								   target = "_blank">View batch</a>
							</td>
							<td>{{$row['batch_creation_date']}}</td>
							<td><span data-toggle = "tooltip" data-placement = "top"
							          title = "{{$row['route_name']}}">{{$row['route_code']}}</span>
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
								      title = "{{ $row['current_station_description'] }}">{{ $row['current_station_name'] }}</span>
							</td>
							<td>{{ $row['batch_creation_date'] }}</td>
							<td><span data-toggle = "tooltip" data-placement = "top"
							          title = "{{ $row['next_station_description'] }}">{{ $row['next_station_name'] }}</span>
							</td>
							<td>{{$row['batch_status']}}</td>
							<td>{{$row['min_order_date']}}</td>
						</tr>
					@endforeach
					<tr>
						<td colspan = "11">
							{!! Form::button('Select / Deselect all', ['id' => 'select_deselect', 'class' => 'btn btn-link']) !!}
							{!! Form::button('Print batches', ['id' => 'print_batches', 'class' => 'btn btn-link']) !!}
							{!! Form::button('Packing Slip', ['id' => 'packing_slip', 'class' => 'btn btn-link']) !!}
							{!! Form::button('Release Batch', ['id' => 'release_batch', 'class' => 'btn btn-link']) !!}
						</td>
					</tr>
					{!! Form::close() !!}
				</table>
			@else
				<div class = "alert alert-warning">No batch is created.</div>
			@endif
		</div>
		<div class = "col-xs-12 text-center">
			{!! $items->render() !!}
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

		$("button#release_batch").on('click', function(event){
			event.preventDefault();
			var url = "{{ url('/items/release_batch') }}";
			setFormUrlAndSubmit(url);
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
	</script>
</body>
</html>
