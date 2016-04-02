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
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">

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

		<div class = "col-xs-12">
			{!! Form::open(['method' => 'get', 'url' => url('shipping'), 'id' => 'search-order']) !!}
			<div class = "form-group col-xs-3">
				<label for = "search_for_first">Search for 1</label>
				{!! Form::text('search_for_first', $request->get('search_for_first'), ['id'=>'search_for_first', 'class' => 'form-control', 'placeholder' => 'Comma delimited']) !!}
			</div>
			<div class = "form-group col-xs-3">
				<label for = "search_in_first">Search in 1</label>
				{!! Form::select('search_in_first', $search_in, $request->get('search_in_first'), ['id'=>'search_in_first', 'class' => 'form-control']) !!}
			</div>
			<div class = "form-group col-xs-3">
				<label for = "search_for_second">Search for 2</label>
				{!! Form::text('search_for_second', $request->get('search_for_second'), ['id'=>'search_for_second', 'class' => 'form-control', 'placeholder' => 'Comma delimited']) !!}
			</div>
			<div class = "form-group col-xs-3">
				<label for = "search_in_first">Search in 2</label>
				{!! Form::select('search_in_second', $search_in, $request->get('search_in_second'), ['id'=>'search_in_second', 'class' => 'form-control']) !!}
			</div>
			<br />

			<div class = "form-group col-xs-3">
				<label for = "start_date">Start date</label>
				<div class = 'input-group date' id = 'start_date_picker'>
					{!! Form::text('start_date', $request->get('start_date'), ['id'=>'start_date', 'class' => 'form-control', 'placeholder' => 'Enter start date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
			<div class = "form-group col-xs-3">
				<label for = "end_date">End date</label>
				<div class = 'input-group date' id = 'end_date_picker'>
					{!! Form::text('end_date', $request->get('end_date'), ['id'=>'end_date', 'class' => 'form-control', 'placeholder' => 'Enter end date']) !!}
					<span class = "input-group-addon">
                        <span class = "glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
			<div class = "form-group col-xs-2">
				<label for = "" class = ""></label>
				{!! Form::button('Reset', ['id'=>'reset', 'type' => 'reset', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-warning form-control']) !!}
			</div>
			<div class = "form-group col-xs-2">
				<label for = "" class = ""></label>
				{!! Form::submit('Search', ['id'=>'search', 'style' => 'margin-top: 2px;', 'class' => 'btn btn-primary form-control']) !!}
			</div>
			{!! Form::close() !!}
		</div>

		@if(count($ships) > 0)
			<h3 class = "page-header">
				Shipping list ({{ $ships->total() }} items found / {{$ships->currentPage()}} of {{$ships->lastPage()}} pages)
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
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script type = "text/javascript"
	        src = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
	<script type = "text/javascript">
		var options = {
			format: "YYYY-MM-DD", maxDate: new Date()
		};
		$(function ()
		{
			$('#start_date_picker').datetimepicker(options);
			$('#end_date_picker').datetimepicker(options);
		});
	</script>
</body>
</html>