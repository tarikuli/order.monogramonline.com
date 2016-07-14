<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Set store options to SKU conversion parameters</title>
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
			<li class = "active">Set store options to SKU conversion parameters</li>
		</ol>
		@if($errors->any())
			<div class = "alert alert-danger">
				<ul>
					@foreach($errors->all() as $error)
						<li>{!! $error !!}</li>
					@endforeach
				</ul>
			</div>
		@endif
		@if(session('success'))
			<div class = "alert alert-success">{!! session('success') !!}</div>
		@endif
		<div class = "col-md-6 col-md-offset-3" style = "margin-bottom: 10px;">
			{!! Form::open(['method' => 'get',]) !!}
			<div class = "form-group">
				<label for = "store_id">Store</label>
				{!! Form::select('store_id', $stores, $store_id, ['id'=>'store_id', 'class' => 'form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>
		@if($store_id && $store_id != 'all')
			<div class = "col-md-6 col-md-offset-3">
				{!! Form::open(['url' => url(sprintf("logistics/%s/update", $store_id)), 'method' => 'put', 'class' => 'form-horizontal', 'id' => 'parameter-list-form']) !!}
				@unless(count($parameters))
					<div class = "alert alert-warning">No parameter is set yet for this shop</div>
				@endunless
				<table class = "table table-bordered" id = "parameters-table">
					<thead>
					<tr>
						<th class = "text-center">Parameters</th>
					</tr>
					</thead>
					<tbody id = "parameters-table-body">
					@if(count($parameters))
						@foreach($parameters as $parameter)
							<tr>
								<td>
									<div class = "input-group">
										{!! Form::text('parameters[]', $parameter->parameter_value, ['class' => 'form-control parameter', 'id' => sprintf("parameter-%d", $index++), 'placeholder' => 'Parameter value']) !!}
										<div class = "input-group-btn">
											<button type = "button" class = "btn btn-default move-up"
											        data-toggle = "tooltip"
											        data-placement = "top" title = "Move up">
												<span class = "fa fa-caret-up"></span>
											</button>
											<button type = "button" class = "btn btn-default move-down"
											        data-toggle = "tooltip"
											        data-placement = "top" title = "Move down">
												<span class = "fa fa-caret-down"></span>
											</button>
											<button type = "button" class = "btn btn-default removable"
											        data-toggle = "tooltip"
											        data-placement = "top" title = "Remove" id = "addon-{{$index}}">
												<span class = "fa fa-times text-danger"></span>
											</button>
										</div>
									</div>
								</td>
							</tr>
						@endforeach
					@endif
					</tbody>
					<tfoot>
					<tr class = "text-right">
						<td>
							<button type = "button" class = "btn btn-primary btn-sm add-new-parameter-to-table">
								Add new parameter
							</button>
						</td>
					</tr>
					</tfoot>
				</table>
				<div class = "form-group">
					<div class = "col-sm-6">
						<button type = "submit" class = "btn btn-success">Update</button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		@else
			<div class = "col-md-12">
				<div class = "alert alert-warning">Select a shop to update the sku parameter.</div>
			</div>
		@endif
		<div class = "col-md-12">
			<h3 class = "page-header" role = "button" data-toggle = "collapse" href = "#collapsible"
			    aria-expanded = "false" aria-controls = "collapsible">Create new SKU converter</h3>
			<div class = "collapse" id = "collapsible">
				{!! Form::open(['url' => url('logistics/sku_converter'), 'id' => 'create-sku-converter', 'class' => 'form-horizontal']) !!}
				<div class = "form-group">
					{!! Form::label("store_id", "Store id", ['class'=> 'col-md-2 control-label']) !!}
					<div class = "col-sm-6">
						{!! Form::text('store_id', null, ['class' => 'form-control', 'id' => 'store_id', 'placeholder' => 'Store id']) !!}
					</div>
				</div>
				<div class = "form-group">
					{!! Form::label("store_name", "Store name", ['class'=> 'col-md-2 control-label']) !!}
					<div class = "col-sm-6">
						{!! Form::text('store_name', null, ['class' => 'form-control', 'id' => 'store_name', 'placeholder' => 'Store name']) !!}
					</div>
				</div>
				<div class = "form-group">
					{!! Form::label(sprintf('new-parameter-%d', $index), "Parameter field", ['class'=> 'col-md-2 control-label']) !!}
					<div class = "col-sm-6">
						<div class = "input-group">
							{!! Form::text('parameters[]', null, ['class' => 'form-control parameter', 'id' => sprintf("parameter-%d", $index++), 'placeholder' => 'Parameter value']) !!}
							<span class = "input-group-addon" data-toggle = "tooltip" data-placement = "top"
							      title = "Remove" id = "addon-{{$index}}"><i
										class = "fa fa-times text-danger"></i> </span>
						</div>
					</div>
				</div>
				<div class = "form-group">
					<div class = "col-sm-offset-2 col-sm-10">
						<button type = "submit" class = "btn btn-success">Create</button>
						<button type = "button" class = "btn btn-info add-new-parameter">Add new parameter field
						</button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		$(function ()
		{
			$("body").tooltip({selector: '[data-toggle="tooltip"]'});
			table_row_repositioning_method();
		});
		function add_new_row (position)
		{
			var row = '<tr>\
						<td>\
							<div class="input-group">\
								<input class="form-control parameter" id="parameter-INDEX_NUMBER" placeholder="Parameter value" name="parameters[]" type="text">\
								<div class="input-group-btn">\
									<button type="button" class="btn btn-default move-up" data-toggle="tooltip" data-placement="top" title="Move up">\
										<span class="fa fa-caret-up"></span>\
									</button>\
									<button type="button" class="btn btn-default move-down" data-toggle="tooltip" data-placement="top" title="Move down">\
										<span class="fa fa-caret-down"></span>\
									</button>\
									<button type="button" class="btn btn-default removable" data-toggle="tooltip" data-placement="top" title="Remove" id="addon-19">\
										<span class="fa fa-times text-danger"></span>\
									</button>\
								</div>\
							</div>\
						</td>\
					</tr>';
			if ( $(position).length ) {
				$(position).after($(row));
			} else {
				var parent = $("table#parameters-table tbody#parameters-table-body");
				$(parent).append($(row));
			}

			table_row_repositioning_method();
		}
		function table_row_repositioning_method ()
		{
			$("tbody#parameters-table-body tr").each(function ()
			{
				var has_next = $(this).next().length ? true : false;
				var has_previous = $(this).prev().length ? true : false;

				if ( has_next ) {
					$(this).find('button.move-down').show();
				} else {
					$(this).find('button.move-down').hide();
				}

				if ( has_previous ) {
					$(this).find('button.move-up').show();
				} else {
					$(this).find('button.move-up').hide();
				}
			});
		}
		$("button.add-new-parameter-to-table").on('click', function (event)
		{
			var tr = $("table tbody#parameters-table-body tr:last");
			if ( !tr ) {
				tr = $("tbody#draggable-table-rows");
			}
			add_new_row(tr);
		});
		$("select#store_id").on('change', function ()
		{
			$(this).closest('form').submit();
		});
		$("body").on('click', 'button.move-up', function (event)
		{
			var current_row = $(this).closest('tr');
			var previous_row = current_row.prev();
			if ( previous_row.length ) {
				previous_row.before(current_row);
			}
			table_row_repositioning_method();
		});
		$("body").on('click', 'button.move-down', function (event)
		{
			var current_row = $(this).closest('tr');
			var next_row = current_row.next();
			if ( next_row.length ) {
				next_row.after(current_row);
			}
			table_row_repositioning_method();
		});

		$("body").on('click', "button.removable", function ()
		{
			var answer = confirm('Are you sure to remove this parameter?');
			if ( answer ) {
				$(this).closest('tr').remove();
			}
			table_row_repositioning_method();
		});
	</script>
</body>
</html>