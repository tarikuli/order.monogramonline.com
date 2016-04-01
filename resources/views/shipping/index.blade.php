<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Ships list</title>
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
			<li>Shipping list</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')

		@if(count($ships) > 0)
			<h3 class = "page-header">
				Shipping list
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>Order number</th>
					<th>Mail class</th>
					<th>Package shape</th>
					<th>Tracking type</th>
					<th>Length</th>
					<th>Height</th>
					<th>Width</th>
					<th>Billed weight</th>
					<th>Actual weight</th>
					<th>Name</th>
					<th>Company</th>
					<th>Address 1</th>
					<th>Address 2</th>
					<th>City</th>
					<th>State</th>
					<th>Postal code</th>
					<th>Country</th>
					<th>Email</th>
					<th>Phone</th>
				</tr>
				@foreach($ships as $ship)
					<tr data-id = "{{$ship->id}}" class = "text-center">
						<td>
							<a href = "{{url(sprintf("orders/details/%s", $ship->order_number))}}">{{ $ship->unique_order_id }}</a>
						</td>
						<td>{{$ship->mail_class}}</td>
						<td>{{$ship->package_shape}}</td>
						<td>{{$ship->tracking_type}}</td>
						<td>{{$ship->length}}</td>
						<td>{{$ship->height}}</td>
						<td>{{$ship->width}}</td>
						<td>{{$ship->billed_weight}}</td>
						<td>{{$ship->actual_weight}}</td>
						<td>{{$ship->name}}</td>
						<td>{{$ship->company}}</td>
						<td>{{$ship->address1}}</td>
						<td>{{$ship->address2}}</td>
						<td>{{$ship->city}}</td>
						<td>{{$ship->state_city}}</td>
						<td>{{$ship->postal_code}}</td>
						<td>{{$ship->country}}</td>
						<td>{{$ship->email}}</td>
						<td>{{$ship->phone}}</td>
					</tr>
				@endforeach
			</table>

			<div class = "col-xs-12 text-center">
				{!! $ships->appends($request->all())->render() !!}
			</div>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No ships found.</h3>
				</div>
			</div>
		@endif
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>