<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Add purchase</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
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
		{!! Form::open(['url' => url('/purchases'), 'method' => 'post','class'=>'form-horizontal','role'=>'form']) !!}

		<div class = 'form-group'>
			{!!Form::label('vendor_id','Vendor :',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-5'>
				{!! Form::select('vendor_id', $vendors, null, ['id' => 'vendor_id','class' => 'form-control']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('lc_number','LC Number :',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-5'>
				{!! Form::text('lc_number', null, ['id' => 'lc_number','class' => 'form-control', 'placeholder' => 'LC Number']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('insurance_number','Insurance Number :',['class'=>'control-label col-xs-2'])!!}
			<div class = 'col-xs-5'>
				{!! Form::text('insurance_number', null, ['id' => 'insurance_number','class' => 'form-control', 'placeholder' => 'Insurance Number']) !!}
			</div>
		</div>

		@setvar($i = 0)
		@if(is_array($request->old('product_code')))
			@foreach($request->old('product_code') as $product)
				<div class = 'form-group collection'>
					<div class = "col-md-2">
						<a class = "btn btn-link">Remove this row</a>
					</div>
					<div class = "col-md-offset-2 col-md-10">
						<div class = "row">
							<div class = "col-md-4">
								{!!Form::label("product_code[$i]",'Product :',['class'=>'control-label col-xs-6'])!!}
								{!! Form::select("product_code[$i]", $products, null, ['id' => "", 'step' => 'any', 'class' => 'form-control']) !!}
							</div>
							<div class = "col-md-4">
								{!!Form::label("quantity[$i]",'Quantity :',['class'=>'control-label col-xs-6'])!!}
								{!! Form::number("quantity[$i]", null, ['id' => "", 'step' => 'any', 'class' => 'form-control']) !!}
							</div>
							<div class = "col-md-4">
								{!!Form::label("price[$i]",'Price :',['class'=>'control-label col-xs-6'])!!}
								{!! Form::number("price[$i]", null, ['id' => "", 'step' => 'any', 'class' => 'form-control']) !!}
							</div>
						</div>
					</div>
				</div>
				@setvar(++$i)
			@endforeach
		@else
			<div class = 'form-group collection'>
				<div class = "col-md-2">
					<a class = "btn btn-link remove-row">Remove this row</a>
				</div>
				<div class = "col-md-10">
					<div class = "row">
						<div class = "col-md-4">
							{!! Form::label("product_code[$i]",'Product :',['class'=>'control-label col-xs-6'])!!}
							{!! Form::select("product_code[$i]", $products, null, ['id' => "", 'step' => 'any', 'class' => 'form-control']) !!}
						</div>
						<div class = "col-md-4">
							{!!Form::label("quantity[$i]",'Quantity :',['class'=>'control-label col-xs-6'])!!}
							{!! Form::number("quantity[$i]", null, ['id' => "", 'step' => 'any', 'class' => 'form-control']) !!}
						</div>
						<div class = "col-md-4">
							{!!Form::label("price[$i]",'Price :',['class'=>'control-label col-xs-6'])!!}
							{!! Form::number("price[$i]", null, ['id' => "", 'step' => 'any', 'class' => 'form-control']) !!}
						</div>
					</div>
				</div>
			</div>
		@endif

		<div class = 'form-group'>
			<div class = "col-xs-offset-2 col-xs-5">
				{!! Form::button('Add new row',['class'=>'btn btn-success btn-block', 'id' => 'add-new-row']) !!}
				{!! Form::submit('Add purchase',['class'=>'btn btn-primary btn-block']) !!}
			</div>
		</div>

		{!! Form::close() !!}
	</div>
	<script src = "//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		$("button#add-new-row").on('click', function ()
		{
			var collection = $("div.collection");
			var collection_text = $('<div />').append($(collection).eq(0).clone()).html();
			var new_row = collection_text.replace(/\[.?]/g, "[]");
			$(collection).last().after(new_row);
		});
		$(document).on('click', 'a.remove-row', function (event)
		{
			event.preventDefault();
			$(this).closest('div.collection').remove();
		});
	</script>
</body>
</html>