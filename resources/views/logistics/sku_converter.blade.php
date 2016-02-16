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
		<div class = "col-xs-12" style = "margin-bottom: 10px;">
			{!! Form::open(['method' => 'get', 'class' => "form-inline"]) !!}
			<div class = "form-group">
				<label for = "store_id">Store id</label>
				{!! Form::select('store_id', $stores, $store_id, ['id'=>'store_id', 'class' => 'form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>
		@if($store_id && $store_id != 'all')
			<div class = "col-xs-12">
				{!! Form::open(['url' => url(sprintf("logistics/%s/update", $store_id)), 'method' => 'put', 'class' => 'form-horizontal', 'id' => 'parameter-list-form']) !!}
				@if(count($parameters))
					@foreach($parameters as $parameter)
						<div class = "form-group">
							{!! Form::label(sprintf('parameter-%d', $index), "Parameter field", ['class'=> 'col-xs-2 control-label']) !!}
							<div class = "col-sm-6">
								<div class = "input-group">
									{!! Form::text('parameters[]', $parameter->parameter_value, ['class' => 'form-control parameter', 'id' => sprintf("parameter-%d", $index++), 'placeholder' => 'Parameter value']) !!}
									<span class = "input-group-addon" data-toggle = "tooltip" data-placement = "top"
									      title = "Remove" id = "addon-{{$index}}"><i
												class = "fa fa-times text-danger"></i> </span>
								</div>
							</div>
						</div>
					@endforeach
				@else
					<div class = "alert alert-warning">No parameter is set yet for this shop</div>
				@endif

				<div class = "form-group">
					<div class = "col-sm-offset-2 col-sm-10">
						<button type = "submit" class = "btn btn-success">Update</button>
						<button type = "button" class = "btn btn-info add-new-parameter">Add new parameter field
						</button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning">Select a shop to update the sku parameter.</div>
			</div>
		@endif
		<div class = "col-xs-12">
			<h3 class = "page-header" role = "button" data-toggle = "collapse" href = "#collapsible"
			    aria-expanded = "false" aria-controls = "collapsible">Create new SKU converter</h3>
			<div class = "collapse" id="collapsible">
				{!! Form::open(['url' => url('logistics/sku_converter'), 'id' => 'create-sku-converter', 'class' => 'form-horizontal']) !!}
				<div class = "form-group">
					{!! Form::label("store_id", "Store id", ['class'=> 'col-xs-2 control-label']) !!}
					<div class = "col-sm-6">
						{!! Form::text('store_id', null, ['class' => 'form-control', 'id' => 'store_id', 'placeholder' => 'Store id']) !!}
					</div>
				</div>
				<div class = "form-group">
					{!! Form::label("store_name", "Store name", ['class'=> 'col-xs-2 control-label']) !!}
					<div class = "col-sm-6">
						{!! Form::text('store_name', null, ['class' => 'form-control', 'id' => 'store_name', 'placeholder' => 'Store name']) !!}
					</div>
				</div>
				<div class = "form-group">
					{!! Form::label(sprintf('new-parameter-%d', $index), "Parameter field", ['class'=> 'col-xs-2 control-label']) !!}
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
		});
		$("button.add-new-parameter").on('click', function (event)
		{
			//var form = $("form#parameter-list-form");
			var form = $(this).closest('form');
			var row = '<div class="form-group">\
							<label for="parameter-INDEX_NUMBER" class="col-xs-2 control-label">Parameter field</label>\
							<div class="col-sm-6">\
								<div class="input-group">\
									<input class="form-control parameter" id="parameter-INDEX_NUMBER" placeholder="Parameter value" name="parameters[]" type="text">\
									<span class="input-group-addon" data-toggle="tooltip" data-placement="top" title="" id="addon-INDEX_NUMBER" data-original-title="Remove"><i class="fa fa-times text-danger"></i> </span>\
								</div>\
							</div>\
						</div>';

			var divs = $(form).find('div.form-group').not(':last');
			var number_of_divs = divs.length;
			var new_row = row.replace(/INDEX_NUMBER/g, (++number_of_divs).toString());
			$(form).find('div.form-group:last').before($(new_row));
		});
		$("select#store_id").on('change', function ()
		{
			$(this).closest('form').submit();
		});

		$("body").on('click', "span.input-group-addon", function ()
		{
			var answer = confirm('Are you sure to remove this parameter?');
			if ( answer ) {
				$(this).closest('div.form-group').remove();
			}
		});
	</script>
</body>
</html>