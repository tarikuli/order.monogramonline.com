<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Station summary</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<style>
		td {
			width: 1px;
			white-space: nowrap;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li>Stations summery</li>
		</ol>

		@if(count($summaries) > 0)
			<h3 class = "page-header">Stations summary</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Station</th>
					<th># of lines</th>
					<th>Earliest batch creation date</th>
					<th>Earliest orderdate</th>
				</tr>
				@foreach($summaries as $summary)
					<tr>
						<td>{{$summary['station_name']}} - {{$summary['station_description']}}</td>
						<td>{{$summary['items_count']}}</td>
						<td>{{$summary['earliest_batch_creation_date']}}</td>
						<td>{{$summary['earliest_order_date']}}</td>
					</tr>
				@endforeach
			</table>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No station summary.</h3>
				</div>
			</div>
		@endif
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript"></script>
</body>
</html>