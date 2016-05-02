<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Stations</title>
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
			<li class = "active">Stations</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')

		<div class = "col-xs-12 text-right" style = "margin: 10px 0;">
			<button class = "btn btn-success" type = "button" data-toggle = "collapse" data-target = "#collapsible-top"
			        aria-expanded = "false" aria-controls = "collapsible">Create new station
			</button>
			<div class = "collapse text-left" id = "collapsible-top">
				{!! Form::open(['url' => url('/stations'), 'method' => 'post']) !!}
				<div class = "form-group col-xs-12">
					{!! Form::label('station_name', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-sm-4">
						{!! Form::text('station_name', null, ['id' => 'station_name', 'class' => "form-control", 'placeholder' => "Enter station name"]) !!}
					</div>
				</div>
				<div class = "form-group col-xs-12">
					{!! Form::label('station_description', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-sm-4">
						{!! Form::text('station_description', null, ['id' => 'station_description', 'class' => "form-control", 'placeholder' => "Enter station description"]) !!}
					</div>
				</div>
				<div class = "col-xs-12 apply-margin-top-bottom">
					<div class = "col-xs-offset-2 col-xs-4">
						{!! Form::submit('Create station',['class' => 'btn btn-primary btn-block']) !!}
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
		@if(count($stations) > 0)
			<div class = "col-xs-12">
				<table class = "table table-bordered">
					<tr>
						<th>#</th>
						<th>Station name</th>
						<th>Station description</th>
						<th>Department</th>
						<th>Action</th>
					</tr>
					@foreach($stations as $station)
						<tr data-id = "{{$station->id}}" id="{{ $station->station_name }}">
							<td>{{ $count++ }}</td>
							<td><input class = "form-control" name = "station_name" type = "text"
							           value = "{{$station->station_name}}"></td>
							<td><input class = "form-control" name = "station_description" type = "text"
							           value = "{{$station->station_description}}"></td>
							<td>{!! Form::select('dept', $departments, $station->departments_list->count() ? $station->departments_list[0]->department_id : null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}</td>
							<td>
								{{--<a href = "{{ url(sprintf("/stations/%d", $station->id)) }}" class = "btn btn-success">View</a>|--}}
								<a href = "#" class = "update"><i class = "fa fa-pencil-square-o text-success"></i> </a>
								| <a href = "#" class = "delete"><i class = "fa fa-times text-danger"></i></a>
							</td>
						</tr>
					@endforeach
				</table>
			</div>
			<div class = "col-xs-12 text-center">
				{!! $stations->render() !!}
			</div>
			{!! Form::open(['url' => url('/stations/id'), 'method' => 'delete', 'id' => 'delete-station']) !!}
			{!! Form::close() !!}

			{!! Form::open(['url' => url('/stations/id'), 'method' => 'put', 'id' => 'update-station']) !!}
			{!! Form::hidden('station_name', null, ['id' => 'update_station_name']) !!}
			{!! Form::hidden('station_description', null, ['id' => 'update_station_description']) !!}
			{!! Form::close() !!}
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No station found.</h3>
				</div>
			</div>
		@endif
		<div class = "col-xs-12 text-right" style = "margin: 10px 0;">
			<button class = "btn btn-success" type = "button" data-toggle = "collapse"
			        data-target = "#collapsible-bottom"
			        aria-expanded = "false" aria-controls = "collapsible">Create new station
			</button>
			<div class = "collapse text-left" id = "collapsible-bottom">
				{!! Form::open(['url' => url('/stations'), 'method' => 'post']) !!}
				<div class = "form-group col-xs-12">
					{!! Form::label('station_name', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-sm-4">
						{!! Form::text('station_name', null, ['id' => 'station_name', 'class' => "form-control", 'placeholder' => "Enter station name"]) !!}
					</div>
				</div>
				<div class = "form-group col-xs-12">
					{!! Form::label('station_description', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-sm-4">
						{!! Form::text('station_description', null, ['id' => 'station_description', 'class' => "form-control", 'placeholder' => "Enter station description"]) !!}
					</div>
				</div>
				<div class = "col-xs-12 apply-margin-top-bottom">
					<div class = "col-xs-offset-2 col-xs-4">
						{!! Form::submit('Create station',['class' => 'btn btn-primary btn-block']) !!}
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
				var form = $("form#delete-station");
				var url = form.attr('action');
				form.attr('action', url.replace('id', id));
				form.submit();
			}
		});

		$("a.update").on('click', function (event)
		{
			event.preventDefault();
			var tr = $(this).closest('tr');
			var id = tr.attr('data-id');

			var name = tr.find('input').eq(0).val();
			var description = tr.find('input').eq(1).val();

			$("input#update_station_name").val(name);
			$("input#update_station_description").val(description);
			var form = $("form#update-station");
			var url = form.attr('action');
			form.attr('action', url.replace('id', id));
			form.submit();
		});
	</script>
</body>
</html>