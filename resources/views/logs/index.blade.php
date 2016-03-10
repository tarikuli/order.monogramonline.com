<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Station Logs</title>
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
			<li class = "active">Logs</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		{{-- Search criteria --}}
		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get', 'url' => url('logs'), 'id' => 'search-station-log']) !!}
			<div class = "form-group col-xs-3">
				<label for = "route">Station</label>
				{!! Form::select('station', $stations, $request->get('station'), ['id'=>'station', 'class' => 'form-control']) !!}
			</div>
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
			<div class = "form-group col-xs-2">
				<label for = "" class = ""></label>
				{!! Form::submit('Search', ['id'=>'search', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-primary form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>
		{{-- Search criteria ends --}}
		@if(count($logs) > 0)
			<h3 class = "page-header">
				Logs ({{ $logs->total() }} items found / {{$logs->currentPage()}} of {{$logs->lastPage()}} pages)
			</h3>
			<div class = "col-xs-12">
				<table class = "table table-bordered">
					<tr>
						<th>#</th>
						<th>Station name</th>
						<th>Items</th>
					</tr>
					@foreach($logs as $log)
						<tr data-id = "{{$log->id}}">
							<td>{{ $count++ }}</td>
							<td>
								@if($log->station)
									{{$log->station->station_description}}
								@else
									-
								@endif
							</td>
							<td>
								{{$log->item_count}}
							</td>
						</tr>
					@endforeach
				</table>
			</div>
			<div class = "col-xs-12 text-center">
				{!! $logs->render() !!}
			</div>

		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No station log found.</h3>
				</div>
			</div>
		@endif
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
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
	</script>
</body>
</html>