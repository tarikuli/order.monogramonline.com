<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Edit Vendor - {{$purchasedInvProducts->name}}</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

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
		@include('includes.success_div')

		{!! Form::open(['url' => url(sprintf("/purchasedinvproducts/%d", $purchasedInvProducts->id)), 'method' => 'put', 'files' => true,'class'=>'form-horizontal','role'=>'form']) !!}
		<div class = 'form-group'>
			{!!Form::label('code','Code #:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = 'col-xs-5'>
				{!! Form::text('code', $purchasedInvProducts->code, ['id' => 'code','class' => 'form-control']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('name','Name :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('name', $purchasedInvProducts->code, ['id' => 'name','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('unit','Unit :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('unit', $purchasedInvProducts->code, ['id' => 'unit','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = 'form-group'>
			{!!Form::label('price','Price: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::text('price', $purchasedInvProducts->code, ['id' => 'price','class'=>'form-control']) !!}
			</div>
		</div>

		<div class = "form-group">
			<div class = "col-xs-offset-4 col-xs-5">
				{!! Form::submit('Update Purchase Inventory Products',['class'=>'btn btn-primary btn-block']) !!}
			</div>
		</div>
		{!! Form::close() !!}

		{!! Form::open(['url' => url(sprintf('/vendors/%d', $purchasedInvProducts->id)), 'method' => 'delete', 'id' => 'delete-purchasedinvproducts-form', 'class'=>'form-horizontal','role'=>'form']) !!}
		<div class = "form-group">
			<div class = "col-xs-offset-4 col-xs-5">
				{!! Form::submit('Delete Purchase Inventory Products', ['class'=> 'btn btn-primary btn-block btn-danger', 'id' => 'delete-purchasedinvproducts-btn']) !!}
			</div>
		</div>
		{!! Form::close() !!}

	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript">
		var message = {
			delete: 'Are you sure you want to delete?',
		};
		$("input#purchasedinvproducts").on('click', function (event)
		{
			event.preventDefault();
			var action = confirm(message.delete);
			if ( action ) {
				var form = $("form#delete-purchasedinvproducts-form");
				form.submit();
			}
		});
	</script>
</body>
</html>