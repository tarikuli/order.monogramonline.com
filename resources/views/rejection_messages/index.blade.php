<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Rejection messages</title>
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
			<li class = "active">Rejection messages</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "col-xs-12 text-right" style = "margin: 10px 0;">
			<button class = "btn btn-success" type = "button" data-toggle = "collapse" data-target = "#collapsible-top"
			        aria-expanded = "false" aria-controls = "collapsible">Add rejection message
			</button>
			<div class = "collapse text-left" id = "collapsible-top">
				{!! Form::open(['url' => url('/rejection_messages'), 'method' => 'post']) !!}
				<div class = "form-group col-xs-12">
					{!! Form::label('department_id', 'Department', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-sm-4">
						{!! Form::select('department_id', $departments_list, null, ['id' => 'department_id', 'class' => "form-control departments-drop-down"]) !!}
					</div>
				</div>
				<div class = "form-group col-xs-12">
					{!! Form::label('station_id', 'Station', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-sm-4">
						{!! Form::select('station_id', [] , null, ['id' => 'station_id', 'class' => "form-control stations-drop-down"]) !!}
					</div>
				</div>
				<div class = "form-group col-xs-12">
					{!! Form::label('rejection_message', 'Rejection message', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-sm-4">
						{!! Form::text('rejection_message', null, ['id' => 'rejection_message', 'class' => "form-control", 'placeholder' => "Enter rejection message"]) !!}
					</div>
				</div>
				<div class = "col-xs-12 apply-margin-top-bottom">
					<div class = "col-xs-offset-2 col-xs-4">
						{!! Form::submit('Add', ['class' => 'btn btn-primary btn-block']) !!}
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
		@if(count($rejection_messages) > 0)
			<div class = "col-xs-12">
				<table class = "table table-bordered">
					<tr>
						<th>#</th>
						<th>Department</th>
						<th>Station</th>
						<th>Rejection message</th>
						<th>Action</th>
					</tr>
					@foreach($rejection_messages as $rejection_message)
						<tr data-id = "{{$rejection_message->id}}">
							<td>{{ $count++ }}</td>
							{{--<td>
								<input class = "form-control" name = "category_code" type = "text"
								       value = "{{$rejection_message->department_id}}">
							</td>
							<td>
								<input class = "form-control" name = "category_description" type = "text"
								       value = "{{$rejection_message->station_id}}">
							</td>
							<td>
								<input class = "form-control" name = "category_display_order" type = "text"
								       value = "{{$rejection_message->rejection_message}}">
							</td>--}}
							<td>{!! Form::select('update_able_department_id', $departments_list, $rejection_message->department_id, ['class' => 'form-control departments-drop-down']) !!}</td>
							<td>{!! Form::select('update_able_station_id', $stations_list->get($rejection_message->department_id, []), $rejection_message->station_id, ['class' => 'form-control stations-drop-down']) !!}</td>
							<td>{!! Form::text('update_able_rejection_message', $rejection_message->rejection_message, ['class' => 'form-control']) !!}</td>
							<td>
								<a href = "#" class = "update" data-toggle = "tooltip" data-placement = "top"
								   title = "Edit this item"><i class = "fa fa-pencil-square-o text-success"></i>
								</a>
								|
								<a href = "#" class = "delete"
								   data-toggle = "tooltip"
								   data-placement = "top"
								   title = "Delete this item"><i
											class = "fa fa-times text-danger"></i>
								</a>
							</td>
						</tr>
					@endforeach
				</table>
			</div>
			<div class = "col-xs-12 text-center">
				{!! $rejection_messages->render() !!}
			</div>
			{!! Form::open(['url' => url('/rejection_messages/id'), 'method' => 'delete', 'id' => 'delete-rejection-message']) !!}
			{!! Form::close() !!}

			{!! Form::open(['url' => url('/rejection_messages/id'), 'method' => 'put', 'id' => 'update-rejection-message']) !!}
			{!! Form::hidden('updated_department_id', null, ['id' => 'updated_department_id']) !!}
			{!! Form::hidden('updated_station_id', null, ['id' => 'updated_station_id']) !!}
			{!! Form::hidden('updated_rejection_message', null, ['id' => 'updated_rejection_message']) !!}
			{!! Form::close() !!}
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No rejection message found.</h3>
				</div>
			</div>
		@endif

	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		var stations_list = {!! $stations_list->toJson() !!};
		$(function ()
		{
			$('[data-toggle="tooltip"]').tooltip();
		});

		$("select.departments-drop-down").on('change', function (event)
		{
			var selected = $(this).val();
			// get the next stations dropdown
			var tr = $(this).closest('tr');
			var node = null;
			if ( tr.length ) {
				node = tr;
			} else {
				var form = $(this).closest('form');
				if ( form.length ) {
					node = form;
				}
			}
			if ( node == null ) {
				alert("Sorry, something went wrong!");
				return false;
			}
			truncate_station_list(node);
			if ( selected == 0 ) {
				return false;
			}
			if ( selected in stations_list ) {
				populate_station_list(node, selected);
			}
		});

		function find_corresponding_station_drop_down (node)
		{
			return $(node).find('select.stations-drop-down');
		}

		function populate_station_list (node, key)
		{
			//truncate_station_list(node);
			var data = stations_list[key];
			var select_station_field = find_corresponding_station_drop_down(node);

			$.each(data, function (key, value)
			{
				$(select_station_field).append('<option value=' + key + '>' + value + '</option>');
			});
		}

		function truncate_station_list (node)
		{
			$(find_corresponding_station_drop_down(node)).empty();
		}

		var message = {
			delete: 'Are you sure you want to delete?',
		};
		$("a.delete").on('click', function (event)
		{
			event.preventDefault();
			var id = $(this).closest('tr').attr('data-id');
			var action = confirm(message.delete);
			if ( action ) {
				var form = $("form#delete-rejection-message");
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

			var updated_department_id = tr.find('select').eq(0).val();
			var updated_station_id = tr.find('select').eq(1).val();
			var updated_rejection_message = tr.find('input[type="text"]').eq(0).val();

			$("input#updated_department_id").val(updated_department_id);
			$("input#updated_station_id").val(updated_station_id);
			$("input#updated_rejection_message").val(updated_rejection_message);
			var form = $("form#update-rejection-message");
			var url = form.attr('action');
			form.attr('action', url.replace('id', id));
			form.submit();
		});
	</script>
</body>
</html>