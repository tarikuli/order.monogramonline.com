<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Add purchase</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('/purchases')}}">Purchases</a></li>
			<li class = "active">Add purchase</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		{!! Form::open(['url' => url('/purchases'), 'method' => 'post','class'=>'form-horizontal','role'=>'form']) !!}

		<div class = 'form-group'>
			{!!Form::label('po_number','Purchase Order # :',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('po_number', null, ['id' => 'po_number','class' => 'form-control', 'placeholder' => 'Purchase Order #']) !!}
			</div>

			{!!Form::label('po_date','PO Date:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				<div class = 'input-group date' id = 'expidite_date'>
				{!! Form::text('po_date', null, ['id'=>'po_date', 'class' => 'form-control', 'placeholder' => 'PO date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>

		</div>

		<div class = 'form-group'>
			{!!Form::label('vendor_id','Vendor ID:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('vendor_id', null, ['id' => 'vendor_id','class' => 'form-control', 'placeholder' => 'Vendor ID']) !!}
			</div>

			{!!Form::label('vendor_name','Vendor Name:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('vendor_name', null, ['id' => 'vendor_name','class' => 'form-control', 'placeholder' => 'Vendor Name']) !!}
			</div>

			{!!Form::label('email','Email:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('email', null, ['id' => 'email','class' => 'form-control', 'placeholder' => 'Email']) !!}
			</div>

		</div>

		<div class = 'form-group'>
			{!!Form::label('zip_code','Zip Code:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('zip_code', null, ['id' => 'zip_code','class' => 'form-control', 'placeholder' => 'Zip Code']) !!}
			</div>

			{!!Form::label('state','State:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('state', null, ['id' => 'state','class' => 'form-control', 'placeholder' => 'State']) !!}
			</div>

			{!!Form::label('phone_number','Phone Number:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('phone_number', null, ['id' => 'phone_number','class' => 'form-control', 'placeholder' => 'Phone Number']) !!}
			</div>
		</div>

		<div class = "col-md-12">
				<div class = "col-md-4">
					{!! Form::label("product_code",'Product :',['class'=>'control-label col-xs-6'])!!}
				</div>
				<div class = "col-md-4">
					{!!Form::label("quantity",'Quantity :',['class'=>'control-label col-xs-6'])!!}
				</div>
				<div class = "col-md-4">
					{!!Form::label("price",'Price :',['class'=>'control-label col-xs-6'])!!}
				</div>
		</div>

		{{-- Code for add item Dynamically --}}
		@setvar($i = 0)
		<table>
		<tr>
			<td>purchase_id</td>
			<td>product_id</td>
			<td>stock_no</td>
			<td>vendor_sku</td>
			<td>quantity</td>
			<td>price</td>
			<td>sub_total</td>
			<td>receive_date</td>
			<td>receive_quantity</td>
			<td>balance_quantity</td>
			<td>{!! Form::button('Add new row',['class'=>'btn btn-success btn-block', 'id' => 'add-new-row']) !!}</td>
		</tr>
		<tr class="collection">
			<td >
				{!! Form::text("purchase_id[$i]", null, ['id' => "purchase_id", 'step' => 'any', 'class' => 'form-control']) !!}
			</td>
			<td >
				{!! Form::text("product_id[$i]", null, ['id' => "product_id", 'step' => 'any', 'class' => 'form-control']) !!}
			</td>
			<td >
				{!! Form::text("stock_no[$i]", null, ['id' => "stock_no", 'step' => 'any', 'class' => 'form-control']) !!}
			</td>
			<td >
				{!! Form::text("vendor_sku[$i]", null, ['id' => "vendor_sku", 'step' => 'any', 'class' => 'form-control']) !!}
			</td>
			<td >
				{!! Form::number("quantity[$i]", null, ['id' => "quantity", 'step' => 'any', 'class' => 'form-control']) !!}
			</td>
			<td >
				{!! Form::number("price[$i]", null, ['id' => "price", 'step' => 'any', 'class' => 'form-control']) !!}
			</td>
			<td >
				{!! Form::number("sub_total[$i]", null, ['id' => "sub_total", 'step' => 'any', 'class' => 'form-control']) !!}
			</td>
			<td >
				{!! Form::number("receive_date[$i]", null, ['id' => "receive_date", 'step' => 'any', 'class' => 'form-control']) !!}
			</td>
			<td >
				{!! Form::number("receive_quantity[$i]", null, ['id' => "receive_quantity", 'step' => 'any', 'class' => 'form-control']) !!}
			</td>
			<td >
				{!! Form::number("balance_quantity[$i]", null, ['id' => "balance_quantity", 'step' => 'any', 'class' => 'form-control']) !!}
			</td>
			<td></td>
		</tr>

		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td>Grand Total=</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>

		</table>

		<br><br>
		<div class = 'form-group'>
			<div class = "col-xs-2">
				{!! Form::submit('Add purchase',['class'=>'btn btn-primary btn-block']) !!}
			</div>
		</div>


		{!! Form::close() !!}
	</div>
	<script src = "//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>


	<script type = "text/javascript">

		var options = {
			format: "YYYY-MM-DD"
		};
		$(function ()
		{
			$('#po_date').datetimepicker(options);
		});

		$("button#add-new-row").on('click', function ()
		{
			var collection = $("tr.collection");
			var collection_text = $('<div />').append($(collection).eq(0).clone()).html();
			var new_row = collection_text.replace(/\[.?]/g, "[]");
			$(collection).last().after(new_row);
		});

// 		$("button#add-new-row").on('click', function(e) {
//             var $this = $(this),
//             $parentTR = $this.closest('tr');
// 	        $parentTR.clone().insertAfter($parentTR);
// 	    });

		$(document).on('click', 'a.remove-row', function (event)
		{
			event.preventDefault();
			$(this).closest('div.collection').remove();
		});


	</script>
</body>
</html>