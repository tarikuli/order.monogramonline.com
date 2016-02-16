<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Departments</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.3/css/bootstrap-select.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link rel = "stylesheet" href = "{{url('assets/css/common.css')}}" type = "text/css" />
	<link type = "text/css" href = "{{url('assets/css/ui.multiselect.css')}}" rel = "stylesheet" />
	<link type = "text/css" href = "http://yandex.st/jquery-ui/1.8.11/themes/humanity/jquery.ui.all.min.css"
	      rel = "stylesheet" />
</head>
<body style = "background:#ffffff ;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;color: #000000;">
	@include('includes.header_menu')
	<div style = "margin-left: 250px;margin-right: 250px">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">Department</li>
		</ol>
		@if($errors->any())
			<div class = "col-xs-12">
				<div class = "alert alert-danger">
					<ul>
						@foreach($errors->all() as $error)
							<li>{{$error}}</li>
						@endforeach
					</ul>
				</div>
			</div>
		@endif
		<div class = "col-xs-12" style = "margin: 10px 0;">

			<label style = "margin-left:330px">Manage WorkFlow Department</label>
			</table>

		</div>
		@if(count($departments) > 0)
			<table>
				<tr>
					<th style = "padding-bottom:10px"><b> </b></th>
					<th style = "padding-bottom:10px"><b>Department code</b></th>
					<th style = "padding-bottom:10px"><b>Department name</b></th>
					<th style = "padding-bottom:10px"><b>Stations</b></th>
					<th style = "padding-bottom:10px"><b>Action</b></th>
				</tr>

				@foreach($departments as $department)
					<tr data-id = "{{$department->id}}">
						<td style = "vertical-align: top;margin-right:20px;padding-bottom:7px">
							<a href = "#"
							   class = "delete"
							   data-toggle = "tooltip"
							   data-placement = "top"
							   title = "Delete this item">
								<i class = "fa fa-times text-danger"></i>
							</a>
						</td>
						<td style = "vertical-align: top;padding-bottom:7px;">{!! Form::text('department_code', $department->department_code, ['style'=>'width:100px;margin-right:80px;margin-left:5px','readonly'=>'readonly']) !!}</td>
						<td style = "vertical-align: top;padding-bottom:7px;">{!! Form::text('department_name', $department->department_name, ['style'=>'width:250px;margin-right:80px']) !!}</td>
						<td style = "vertical-align: top;padding-bottom:7px;">{!! Form::textarea('department_stations', implode(",\n", array_map(function($station) { return $station['station_name']; }, $department->stations_list->toArray())), ['style'=>'width:120px;height:80px;margin-right:80px;overflow-y: scroll;']) !!}</td>
						<td style = "vertical-align: top;padding-bottom:7px;">
							<a href = "#" class = "update" data-toggle = "tooltip" data-placement = "top"
							   title = "Edit this item">
								<button>update</button>
							</a>

						</td>
					</tr>
				@endforeach
			</table>

			<div class = "col-xs-12 text-center">
				{!! $departments->render() !!}
			</div>

			{!! Form::open(['url' => url('/departments/id'), 'method' => 'delete', 'id' => 'delete-department']) !!}
			{!! Form::close() !!}

			{!! Form::open(['url' => url('/departments/id'), 'method' => 'put', 'id' => 'update-department']) !!}
			{!! Form::hidden('department_code', null, ['id' => 'update_department_code']) !!}
			{!! Form::hidden('department_name', null, ['id' => 'update_department_name']) !!}
			{!! Form::hidden('department_stations', null, ['id' => 'update_department_stations']) !!}
			{!! Form::close() !!}

		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No department found.</h3>
				</div>
			</div>
		@endif

		<hr style = "width: 100%; color: black; background-color:black;margin-top: 10px" size = "2" />

		<div class = "col-xs-12 ">
			{!! Form::open(['url' => url('/departments'), 'method' => 'post', 'id' => 'create-department-route']) !!}
			<table>
				<tr>
					<td style = "vertical-align: top;"> {!! Form::text('department_code', null, ['id' => 'department_code', 'placeholder' => "Enter department code", 'style'=>'width:100px']) !!} </td>
					<td style = "vertical-align: top;padding-left:10px">{!! Form::text('department_name', null, ['id' => 'department_name', 'placeholder' => "Enter department name", 'style'=>'width:250px']) !!}  </td>
					<td style = "vertical-align: top;padding-left:10px">
						{!! Form::select('department_stations[]', $stations, null, ['id' => 'countries', 'multiple' => true, 'class' => 'multiselect','style'=>'height:200px']) !!}
					</td>
				</tr>
			</table>
			<br>
			<div class = "col-sm-offset-2 col-sm-4">
				{!! Form::submit('Add', ['style'=>'margin-left:700px;padding:5px 15px']) !!}
			</div>
			{!! Form::close() !!}

		</div>
		<hr style = "width: 100%; color: black; background-color:black;margin-top: 10px" size = "1" />
	</div>

	<script type = "text/javascript" src = "{{ url('assets/js/jquery-1.7.2.min.js') }}"></script>
	<script type = "text/javascript" src = "{{ url('assets/js/jquery-ui.js') }}"></script>
	<script type = "text/javascript" src = "{{ url('assets/js/ui.multiselect.js') }}"></script>
	<script type = "text/javascript">

		$(function ()
		{
			$(".multiselect").multiselect();
		});

	</script>


	{{--<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>--}}
	{{--<script type = "text/javascript"
			src = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.3/js/bootstrap-select.min.js"></script>--}}
	<script type = "text/javascript">

		$(function ()
		{
			$(".multiselect").multiselect();
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
				var form = $("form#delete-department");
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
			var code = tr.find('input').eq(0).val();
			var route = tr.find('input').eq(1).val();
			var stations = tr.find('textarea').eq(0).val();


			$("input#update_department_code").val(code);
			$("input#update_department_name").val(route);
			$("input#update_department_stations").val(stations);

			var form = $("form#update-department");
			var url = form.attr('action');
			form.attr('action', url.replace('id', id));
			form.submit();
		});

		var form = $("form#create-department");

		$(form).on('submit', function ()
		{

			$("ul.selected li").each(function ()
			{
				var selected_id = $(this).attr('data-selected-id');
				if ( selected_id ) {
					$(form).append("<input type='hidden' value='" + selected_id + "' name='department_stations[]' />");
				}
			});
		});

	</script>


</body>
</html>