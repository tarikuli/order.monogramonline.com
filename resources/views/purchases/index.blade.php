<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Purchases</title>
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
			<li class = "active">Purchases</li>
		</ol>

		@include('includes.error_div')
		@include('includes.success_div')

		<div class = "col-md-12 pull-right" style = "margin-bottom: 10px;">
			<a class = "btn btn-success btn-sm pull-right" href = "{{url('/purchases/create')}}">Add purchase</a>
		</div>
		@if(count($purchases) > 0)
			<h3 class = "page-header">
				Purchases
			</h3>
			<table class = "table table-bordered">
				<tr>
					<th>#</th>
					<th>Added on</th>
					<th>Vendor name</th>
					<th>Products</th>
					<th>Action</th>
				</tr>
				@foreach($purchases as $purchase)
					<tr data-id = "{{$purchase->id}}">
						<td>{{ $count++ }}</td>
						<td>{{ substr($purchase->created_at, 0, 10) }}</td>
						<td>{{ substr($purchase->vendor_details->vendor_name , 0, 30) }}</td>
						<td>{{ $purchase->products->count() }}</td>
						<td>
							<a href = "{{ url(sprintf("/purchases/%d", $purchase->id)) }}" data-toggle = "tooltip"
							   data-placement = "top"
							   title = "View this purchase"><i class = 'fa fa-eye text-primary'></i></a>
							{{--| <a href = "{{ url(sprintf("/purchases/%d/edit", $purchase->id)) }}" data-toggle = "tooltip"
							     data-placement = "top"
							     title = "View this purchase"><i class = 'fa fa-pencil-square-o text-success'></i></a>--}}
							| <a href = "#" class = "delete" data-toggle = "tooltip" data-placement = "top"
							     title = "Delete this purchase"><i class = 'fa fa-times text-danger'></i></a>
							| <a href = "{{url(sprintf("/prints/purchase/%d", $purchase->id))}}" class = "print" data-toggle = "tooltip" data-placement = "top"
							     title = "Print purchase slip"><i class = 'fa fa-print text-info'></i></a>
						</td>
					</tr>
				@endforeach
			</table>
			{!! Form::open(['url' => url('/purchases/id'), 'method' => 'delete', 'id' => 'delete-purchase']) !!}
			{!! Form::close() !!}
			<div class = "col-xs-12 text-center">
				{!! $purchases->render() !!}
			</div>
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No purchase found.</h3>
				</div>
			</div>
		@endif
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		$(function ()
		{
			$('[data-toggle="tooltip"]').tooltip();
		});
		var message = {
			delete: 'Are you sure you want to delete?',
		};
		$("a.delete").on('click', function (event)
		{
			event.preventDefault();
			var id = $(this).closest('tr').attr('data-id');
			var action = confirm(message.delete);
			if ( action ) {
				var form = $("form#delete-purchase");
				var url = form.attr('action');
				form.attr('action', url.replace('id', id));
				form.submit();
			}
		});
	</script>
</body>
</html>