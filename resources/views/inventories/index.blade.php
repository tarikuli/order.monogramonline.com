<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Inventories</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
	<style>
		td {
			width: 1px;
			white-space: nowrap;
		}

		li {
			font-size: 10px;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('inventories')}}" class = "active">Inventories</a></li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "row">
			<div class = "col-md-7">
				<div class = "row">
					<div class = "col-md-12">
						{!! Form::open(['url' => url('imports/inventory'), 'files' => true]) !!}
						{!! Form::hidden('todo', null, ['id' => 'todo']) !!}
						<legend>Import inventory file</legend>
						<div class = "form-group" style="display:none;">
							{!! Form::select('inventory_index', $inventory_indexes, 0, ['id' => 'inventory_index', 'class' => 'form-control']) !!}
						</div>
						<div class = "form-group">
							{!! Form::file('attached_csv', ['id' => 'attached_csv', 'class' => 'form-control', 'accept' => '.csv']) !!}
						</div>
						<div class = "form-group">
							{!! Form::button('Validate file', ['class' => 'btn btn-sm btn-default', 'id' => 'validate-file']) !!}
							{!! Form::button('Upload file', ['class' => 'btn btn-sm btn-success', 'id' => 'upload-file']) !!}
						</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
			<div class = "col-md-5">
				<div class = "row">
					<div class = "col-md-12">
						{!! Form::open(['url' => url('exports/inventory'), 'method' => 'get']) !!}
						<legend>Export inventory file</legend>
						<div class = "form-group" style="display:none;">
							{!! Form::label('export_inventory_index', 'Filter export by shipper =>', ['class' => 'control-label']) !!}
							{!! Form::select('export_inventory_index', $inventory_indexes, 0, ['id' => 'export_inventory_index', 'class' => 'form-control']) !!}
						</div>
						<div class = "form-group">
							{!! Form::submit('Export', ['class' => 'btn btn-sm btn-primary']) !!}
						</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
		<div class = "col-xs-12">
			@if(count($inventories))
				<table class = "table table-bordered" id = "batch-items-table">
					<tr>
						<th>stock_no_unique</th>
						<th>stock_name_discription</th>
						<th>sku_weight</th>
						<th>re_order_qty</th>
						<th>min_reorder</th>
						<th>adjustment</th>
						<th>qty_on_hand</th>
						<th>qty_alloc</th>
						<th>qty_exp</th>
						<th>qty_av</th>
						<th>upc</th>
						<th>wh_bin</th>
						<th>warehouse</th>
						<th>Action</th>
					</tr>
					@foreach($inventories as $inventorie)
						<tr data-sku = "{{ $inventorie['stock_no_unique'] }}" id = "{{ $inventorie['id'] }}">
							
								<td> {{ $inventorie['stock_no_unique'] }} </td>
								<td> {{ $inventorie['stock_name_discription'] }} </td>
								<td> {{ $inventorie['sku_weight'] }} </td>
								<td>
								     {!! Form::open(['url' => url(sprintf("updateinventorie/%s", $inventorie['id']))]) !!} 
									 {!! Form::number('re_order_qty', $inventorie['re_order_qty'] , ['style'=>'width:70px;margin-right:25px']) !!}
								</td>
								<td> 
									 {!! Form::number('min_reorder', $inventorie['min_reorder'] , ['style'=>'width:70px;margin-right:25px']) !!}
								</td>
								<td> 
									 {!! Form::number('adjustment', $inventorie['adjustment'] , ['style'=>'width:70px;margin-right:25px']) !!}
								</td>
								<td> {{ $inventorie['qty_on_hand'] }} </td>																																
								<td> {{ $inventorie['qty_alloc'] }} </td>
								<td> {{ $inventorie['qty_exp'] }} </td>
								<td> {{ $inventorie['qty_av'] }} </td>
								<td> {{ $inventorie['upc'] }} </td>
								<td> {{ $inventorie['wh_bin'] }} </td>
								<td> {{ $inventorie['warehouse'] }} </td>
								<td>  
									 {!! Form::button('Update', ['id' => 'inventorie_id', 'class' => 'btn btn-success']) !!}
									 {!! Form::close() !!}
								</td>
						
						</tr>
					@endforeach
				</table>
				<div class="col-md-12 text-center">
					 {!! $inventories->render() !!}
				</div>
			@else
				<div class = "alert alert-warning">No data is available.</div>
			@endif
		</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	{{--<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>--}}
	<script type = "text/javascript">
		$("#validate-file").on('click', function ()
		{
			$("#todo").val('validate');
			$(this).closest('form').submit();
		});
		$("#upload-file").on('click', function ()
		{
			$("#todo").val('upload');
			$(this).closest('form').submit();
		});

		$("button#inventorie_id").on('click', function (){
			event.preventDefault();
// 			var selected = parseInt($(this).closest("tr").find("select#batch_stations").val());
// 			if ( selected !== 0 ) {
				$(this).closest("tr").find("form").submit();
// 			}else if ( selected == 0 ) {
// 				alert("Please Select station");
// 			}
		});
		
	</script>

</body>
</html>