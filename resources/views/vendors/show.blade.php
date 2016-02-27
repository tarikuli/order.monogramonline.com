<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Vendor - {{$vendor->vendor_name}}</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
	@include('includes.header_menu')
	<div class = "container ">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('vendors')}}">Vendors</a></li>
			<li class = "active">View vendor</li>
		</ol>
		<div class = "col-xs-offset-1 col-xs-10 col-xs-offset-1">
			<h4 class = "page-header">Vendor details</h4>
			<table class = "table table-hover table-bordered">
				<tr class = "success">
					<td>Vendor name</td>
					<td>{{$vendor->vendor_name}}</td>
				</tr>
				<tr>
					<td>Email</td>
					<td>{{$vendor->email}}</td>
				</tr>
				<tr class = "success">
					<td>Zip Code</td>
					<td>{{$vendor->zip_code}}</td>
				</tr>
				<tr>
					<td>State</td>
					<td>{{$vendor->state}}</td>
				</tr>
				<tr class = "success">
					<td>Phone number</td>
					<td>{{$vendor->phone_number}}</td>
				</tr>
			</table>
		</div>
		<div class = "col-xs-12" style = "margin-bottom: 30px;">
			<div class = "col-xs-offset-1 col-xs-10" style = "margin-bottom: 10px;">
				<a href = "{{ url(sprintf("/vendors/%d/edit", $vendor->id)) }}" class = "btn btn-success btn-block">Edit
				                                                                                                    this
				                                                                                                    vendor</a>
			</div>
			<div class = "col-xs-offset-1 col-xs-10">
				{!! Form::open(['url' => url(sprintf('/vendors/%d', $vendor->id)), 'method' => 'delete', 'id' => 'delete-vendor-form']) !!}
				{!! Form::submit('Delete vendor', ['class'=> 'btn btn-danger btn-block', 'id' => 'delete-vendor-btn']) !!}
				{!! Form::close() !!}
			</div>
		</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript">
		var message = {
			delete: 'Are you sure you want to delete?',
		};
		$("input#delete-vendor-btn").on('click', function (event)
		{
			event.preventDefault();
			var action = confirm(message.delete);
			if ( action ) {
				var form = $("form#delete-vendor-form");
				form.submit();
			}
		});
	</script>
</body>
</html>