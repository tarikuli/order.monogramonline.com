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
		<div class="col-xs-12">
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
				{!! Form::select('station', $stations, $request->get('station'), ['id'=>'station', 'class' => 'form-control']) !!}
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
			@if(count($items))
				<table class = "table">
					<tr>
						<th>Batch#</th>
						<th>Batch creation date</th>
						<th>Route</th>
						<th>Lines</th>
						<th>Current station</th>
						<th>Current station since</th>
						<th>Next station</th>
						<th>Status</th>
						<th>MinOrderDate</th>
					</tr>
					@foreach($items as $item)
						<tr>
							<td>
								<a href = "{{url(sprintf('batches/%d/%s',$item->batch_number, $item->route->stations_list[0]->station_name))}}">{{$item->batch_number}}</a>
							</td>
							<td>{{substr($item->batch_creation_date, 0, 10)}}</td>
							<td><span data-toggle = "tooltip" data-placement = "top"
							          title = "{{$item->route->batch_route_name}}">{{$item->route->batch_code}}</span>
							</td>
							<td>{{count($item->groupedItems)}}</td>
							<td>@if( $item->route->stations_list && isset($item->route->stations_list[0]) ) <span
										data-toggle = "tooltip" data-placement = "top"
										title = "{{ $item->route->stations_list[0]->station_description }}">{{$item->route->stations_list[0]->station_name }}</span> @endif
							</td>
							<td>{{substr($item->batch_creation_date, 0, 10)}}</td>
							<td>@if( $item->route->stations_list && isset($item->route->stations_list[1]) ) <span
										data-toggle = "tooltip" data-placement = "top"
										title = "{{ $item->route->stations_list[1]->station_description }}">{{ $item->route->stations_list[1]->station_name }}</span> @endif
							</td>
							<td>{{$statuses[$item->item_order_status]}}</td>
							<td>{{substr($item->order->order_date, 0, 10)}}</td>
						</tr>
					@endforeach
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
	</script>
</body>
</html>