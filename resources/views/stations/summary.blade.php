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
	<link type = "text/css" rel = "stylesheet"
	      href = "https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
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
			<li>Active (unshipped) Batches</li>
		</ol>

			<h3 class = "page-header">Not Started & Active (unshipped) Batches By Stations summary</h3>

		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get', 'url' => url('summary'), 'id' => 'search-order']) !!}
			<div class = "form-group col-xs-3">
				<label for = "start_date">Order Start date</label>
				<div class = 'input-group date' id = 'start_date_picker'>
					{!! Form::text('start_date', $request->get('start_date'), ['id'=>'start_date', 'class' => 'form-control', 'placeholder' => 'Enter start date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
			<div class = "form-group col-xs-3">
				<label for = "end_date">Order End date</label>
				<div class = 'input-group date' id = 'end_date_picker'>
					{!! Form::text('end_date', $request->get('end_date'), ['id'=>'end_date', 'class' => 'form-control', 'placeholder' => 'Enter end date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
			<div class = "form-group col-xs-3">
				<label for = "search" class = "">1 minute required for load.</label>
				{!! Form::submit('Search', ['id'=>'search', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-primary form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>	
			
			@if(count($summaries) > 0)
		<table id="summary_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
				<th>Station</th>
				<th>Descrption</th>
				<th>#of lines</th>
				<th>#of items</th>
				<th>Earliest order date</th>
				<th>Earliest Scan date</th>
				<th>Active SKUs</th>
            </tr>
        </thead>
        <tbody>
			@foreach($summaries as $summary)
				<tr>
					<td>
						<a href = "{{url(sprintf("/items/grouped?station=%s&start_date=%s&end_date=%s", $summary['station_id'],$start_date,$end_date))}}">{{$summary['station_name']}}</a>
					</td>
					<td>
						{{$summary['station_description']}}
					</td>
					<td align="right">{{ number_format($summary['lines_count'],0) }}</td>
					<td align="right">{{ number_format($summary['items_count'],0) }}</td>
					<td>{{$summary['earliest_order_date']}}</td>
					<td>{{$summary['earliest_batch_creation_date']}}</td>
					<td><a href = "{{$summary['link']}}" target = "_blank">View active sku</a></td>
				</tr>
			@endforeach
        </tbody>
        
        <tfoot>
			<tr>
				<td></td>
				<td align="right">Totals:</td>
				<td align="right">{{ number_format($total_lines, 0) }}</td>
				<td align="right">{{ number_format($total_items, 0) }}</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
        </tfoot>
    </table>
			<a href = "{{url('summary/export')}}">Export Item Table</a>
			
			

			
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No station summary.</h3>
				</div>
			</div>
		@endif
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.12.3.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
	<script type = "text/javascript" src = "https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script type = "text/javascript" src = "https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
	
	
	<script type = "text/javascript">
		var options = {
			format: "YYYY-MM-DD", maxDate: new Date()
		};
		$(function ()
		{
			$('#start_date').datetimepicker(options);
			$('#end_date').datetimepicker(options);
		});

		$('#summary_table').DataTable({
			"paging":   false,
		});

	</script>


</body>
</html>