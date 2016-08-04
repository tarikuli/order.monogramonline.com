<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Create Vendor</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('purchasedinvproducts')}}">Purchase Inventory Products</a></li>
			<li class = "active">Create Purchase Inventory Products</li>
		</ol>

		@include('includes.error_div')


		{!! Form::open(['url' => url('/purchasedinvproducts'), 'method' => 'post', 'files' => true,'class'=>'form-horizontal','role'=>'form']) !!}
		<div class = 'form-group'>
			{!!Form::label('stock_no','Stock No #:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = 'col-xs-5'>
				{!! Form::text('stock_no', null, ['id' => 'stock_no','class' => 'form-control']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('stock_name_discription','Name / Discription :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('stock_name_discription', null, ['id' => 'stock_name_discription','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('unit','Unit :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('unit', null, ['id' => 'unit','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('unit_price','Unit Price: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('unit_price', null, ['id' => 'unit_price','class'=>'form-control']) !!}
			</div>
		</div>

		<div class = 'form-group'>
			{!!Form::label('sku_weight','Sku Weight: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('sku_weight', null, ['id' => 'sku_weight','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('re_order_qty','Re-order QTY: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('re_order_qty', null, ['id' => 're_order_qty','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('min_reorder','Min Reorder: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('min_reorder', null, ['id' => 'min_reorder','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('adjustment','Adjustment: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('adjustment', null, ['id' => 'adjustment','class'=>'form-control']) !!}
			</div>
		</div>
		<hr size="100%">
		<div class = 'form-group'>
			{!!Form::label('vendor_id','Vendor Id: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('vendor_id', null, ['id' => 'vendor_id','class'=>'form-control']) !!}
			</div>
		</div>

		<div class = 'form-group'>
			{!!Form::label('vendor_sku','Vendor Sku: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('vendor_sku', null, ['id' => 'vendor_sku','class'=>'form-control']) !!}
			</div>
		</div>

		<div class = 'form-group'>
			{!!Form::label('vendor_sku_name','Sku Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('vendor_sku_name', null, ['id' => 'vendor_sku_name','class'=>'form-control']) !!}
			</div>
		</div>

		<div class = 'form-group'>
			{!!Form::label('lead_time_days','Lead Time Days: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('lead_time_days', null, ['id' => 'lead_time_days','class'=>'form-control']) !!}
			</div>
		</div>

		<div class = 'form-group'>
			<div class = "col-xs-offset-4 col-xs-5">
				{!! Form::submit('Create Purchase Inventory Products',['class'=>'btn btn-primary btn-block']) !!}
			</div>
		</div>
		{!! Form::close() !!}
	</div>
	<script src = "//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

	<script type = "text/javascript">
		$("#stock_no").on('blur', function (event)
		{
			stock_no= $("#stock_no").val();
			console.log($('input[name=_token]').val());
			urll = '/inventories/getuniquestock';

		});


	</script>
</body>
</html>