<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Edit purchase</title>
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
			<li class = "active">Edit purchase</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		{!! Form::open(['url' => url(sprintf("/purchases/%s", $purchase->po_number,null)), 'method' => 'put', 'files' => true,'class'=>'form-horizontal','role'=>'form']) !!}

		<div class = 'form-group'>
			{!!Form::label('po_number','Purchase Order # :',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('po_number', $purchase->po_number, ['id' => 'po_number','class' => 'form-control', 'placeholder' => 'Purchase Order #']) !!}
			</div>

			{!!Form::label('po_date','PO Date:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				<div class = 'input-group date' id = 'expidite_date'>
				{!! Form::text('po_date', $purchase->po_date, ['id'=>'po_date', 'class' => 'form-control', 'placeholder' => 'PO date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>

		</div>

		<div class = 'form-group'>
			{!!Form::label('vendor_id','Vendor ID:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('vendor_id', $purchase->vendor_details->id,  ['id' => 'vendor_id','class' => 'form-control', 'placeholder' => 'Vendor ID']) !!}
			</div>

			{!!Form::label('vendor_name','Vendor Name:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('vendor_name', $purchase->vendor_details->vendor_name,  ['id' => 'vendor_name','class' => 'form-control', 'placeholder' => 'Vendor Name']) !!}
			</div>

			{!!Form::label('email','Email:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('email',$purchase->vendor_details->email,  ['id' => 'email','class' => 'form-control', 'placeholder' => 'Email']) !!}
			</div>

		</div>


		<div class = 'form-group'>
			{!!Form::label('zip_code','Zip Code:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('zip_code', $purchase->vendor_details->zip_code,  ['id' => 'zip_code','class' => 'form-control', 'placeholder' => 'Zip Code']) !!}
			</div>

			{!!Form::label('state','State:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('state',$purchase->vendor_details->state,  ['id' => 'state','class' => 'form-control', 'placeholder' => 'State']) !!}
			</div>

			{!!Form::label('phone_number','Phone Number:',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-2'>
				{!! Form::text('phone_number', $purchase->vendor_details->phone_number,  ['id' => 'phone_number','class' => 'form-control', 'placeholder' => 'Phone Number']) !!}
			</div>
		</div>


		{{-- Code for add item Dynamically --}}
		@setvar($i = 0)
		@if($purchase->products)
			<table>
				<tr>
					<td>purchase_id</td>
					<td>vendor_sku</td>
					<td>product_id</td>
					<td>stock_no</td>
					<td>Sku_Name</td>
					<td>quantity</td>
					<td>price</td>
					<td>sub_total</td>
					<td>receive_date<br/>Ex.<br>2016-09-31</td>
					<td>receive_quantity</td>
					<td>balance_quantity</td>
					<td>{!! Form::button('Add new row',['class'=>'btn btn-success btn-block', 'id' => 'add-new-row']) !!}</td>
				</tr>
			@foreach($purchase->products as $product)
				<tr class="collection">
					<td >
						{!! Form::text("purchase_id[$i]", $product->purchase_id,  ['id' => "purchase_id", 'step' => 'any', 'class' => 'form-control', 'readonly']) !!}
					</td>
					<td >
						{!! Form::text("vendor_sku[$i]", $product->vendor_sku,  ['id' => "vendor_sku", 'step' => 'any', 'class' => 'form-control']) !!}
					</td>
					<td >
						{!! Form::text("product_id[$i]", $product->product_id, ['id' => "product_id", 'step' => 'any', 'class' => 'form-control', 'readonly']) !!}
					</td>
					<td >
						{!! Form::text("stock_no[$i]", $product->stock_no, ['id' => "stock_no", 'step' => 'any', 'class' => 'form-control', 'readonly']) !!}
					</td>
					<td >
						{!! Form::text("vendor_sku_name[$i]", $product->product_details->vendor_sku_name,  ['id' => "vendor_sku_name", 'step' => 'any', 'class' => 'form-control', 'readonly']) !!}
					</td>
					<td >
						{!! Form::number("quantity[$i]", $product->quantity, ['id' => "quantity", 'step' => 'any', 'class' => 'form-control']) !!}
					</td>
					<td >
						{!! Form::number("price[$i]", $product->price, ['id' => "price", 'step' => 'any', 'class' => 'form-control']) !!}
					</td>
					<td >
						{!! Form::number("sub_total[$i]", $product->sub_total, ['id' => "sub_total", 'step' => 'any', 'class' => 'form-control sub_total']) !!}
					</td>
					<td >
						{!! Form::text("receive_date[$i]", $product->receive_date, ['id' => "receive_date", 'step' => 'any', 'class' => 'form-control']) !!}

					</td>
					<td >
						{!! Form::number("receive_quantity[$i]", $product->receive_quantity, ['id' => "receive_quantity", 'step' => 'any', 'class' => 'form-control']) !!}
					</td>
					<td >
						{!! Form::number("balance_quantity[$i]", $product->balance_quantity, ['id' => "balance_quantity", 'step' => 'any', 'class' => 'form-control', 'readonly']) !!}
					</td>
					<td>
						<a href = "#" id = "delete" class = "delete" data-toggle = "tooltip" data-placement = "top" title = "Delete this purchase"><i class = 'fa fa-times text-danger'></i>Delete</a>
					</td>
				</tr>
				@setvar(++$i)
			@endforeach
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>Grand Total=</td>
					<td>{!! Form::number("grand_total", $purchase->grand_total,  ['id' => "grand_total", 'step' => 'any', 'class' => 'form-control grand_total']) !!}</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>

			</table>
		@endif
		<br><br>
		<div class = 'form-group'>
			<div class = "col-xs-2">
				{!! Form::submit('Update purchase',['class'=>'btn btn-primary btn-block']) !!}
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
			sumSubTotal();
		});


		$(document).on('click', 'a#delete', function (event)
		{
			event.preventDefault();
			$(this).closest('tr.collection').remove();
			sumSubTotal();
		});

		$(document).on('change', 'input#quantity', function ()
		{
// 			console.log( this.value );
			qntity = getNumber(this.value);
			price = getNumber($(this).closest('td').next().find('#price').val());
			$(this).closest('td').next().next().find('input').val( qntity * price );
			sumSubTotal();
		});

		$(document).on('change', 'input#price', function ()
		{
			//console.log( this.value );
			qntity = getNumber($(this).closest('td').prev().find('input').val());
			price = getNumber(this.value);
// 			console.log( qntity + "----" + price);
			$(this).closest('td').next().find('input').val( qntity * price );
			sumSubTotal();
		});

		function getNumber(number){
			number = parseFloat(number);
			var intRegex = /^\d+$/;
			var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

			if(intRegex.test(number) || floatRegex.test(number)) {
				return number;
			}else{
				return 0
			}
		}
		function sumSubTotal(){
			gTotal = 0;
			$("input.sub_total").each(function() {
				gTotal = gTotal+ getNumber(this.value);
			});
			$('#grand_total').val( gTotal );

			// Calculate balance quantity
			$("input#balance_quantity").each(function() {
				qntity = getNumber($(this).closest('td').prev().prev().prev().prev().prev().find('input').val());
				receive_quantity = getNumber($(this).closest('td').prev().find('input').val());
				balance_quantity = qntity - receive_quantity;
				$(this).val(balance_quantity);
			});
		}

		$(document).on('change', 'input#receive_date', function ()
		{
			if(checkValidDate( this.value ) === true){
// 				console.log( "Valide date" )
			}else{
// 				console.log( "Invalide Date" )
				alert(" Please insert correct YYYY-MM-DD\nDate format");
				$(this).val('');
				return false;
			}
		});

		function checkValidDate(date) {

			var dateFormat = "YYYY-MM-DD";
// 			console.log( date )
			return moment(date, dateFormat, true).isValid();
		}


		$("#vendor_id").on('blur', function (event)
		{
			vendor_id= $("#vendor_id").val();
			token = $('input[name=_token]').val();
// 			console.log(vendor_id +" ---- "+ token);
			route = '/purchases/getVendorById';

			$.ajax({
				url: route,
				headers: {'X-CSRF-TOKEN': token},
				type: 'POST',
				dataType: 'json',
				data: {vendor_id : vendor_id},
				success:function(response) {
// 			  		console.log( response );
					$("#email").val(response.email);
					$("#phone_number").val(response.phone_number);
					$("#state").val(response.state);
					$("#vendor_name").val(response.vendor_name);
					$("#zip_code").val(response.zip_code);
			    }
			 });

		});

		$(document).on('change', 'input#vendor_sku', function ()
		{
			vendor_id = $("#vendor_id").val();
			vendor_sku = this.value;
			token = $('input[name=_token]').val();
// 			console.log(vendor_id +" ---- "+ vendor_sku);
			route = '/purchases/purchased_inv_products';
			$.ajax({
				url: route,
				headers: {'X-CSRF-TOKEN': token},
				type: 'POST',
				dataType: 'json',
				data: {vendor_id : vendor_id, vendor_sku : vendor_sku},
				context: this,
				success:function(response) {
					//console.log( response );
					$(this).closest('td').next().find('input').val(response.product_id);
					$(this).closest('td').next().next().find('input').val(response.stock_no);
					$(this).closest('td').next().next().next().find('input').val(response.vendor_sku_name);
					$(this).closest('td').next().next().next().next().next().find('input').val(response.price);
			    }
			 });
		});

		// Check if empty
		$(document).on('click', 'form :submit', function (){	
			
			$("input#vendor_sku").each(function() {
				if($(this).val() == ""){
						alert("vendor_sku field empty");
					return false;
				}
			});

			$("input#stock_no").each(function() {
				if($(this).val() == ""){
						alert("stock_no field empty");
					return false;
				}
			});
			
		});		

		$(document).on('change', 'input#quantity', function (){
			//	receive_quantity = $(this).closest('tr').find('input#receive_quantity').val();
			// 	console.log(receive_quantity);
			sumSubTotal();
		});

		$(document).on('change', 'input#receive_quantity', function (){
			//	receive_quantity = $(this).closest('tr').find('input#receive_quantity').val();
			// 	console.log(receive_quantity);
			sumSubTotal();
		});
		

	</script>
</body>
</html>