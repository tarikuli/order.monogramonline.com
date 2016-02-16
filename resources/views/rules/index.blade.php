<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Rules</title>
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
			<li class = "active">Rules</li>
		</ol>

		@include('includes.error_div')
		@include('includes.success_div')

		@if(count($rules) > 0)
			<div class = "col-xs-12">
				<table class = "table">
					<tr>
						<th>Action</th>
						<th>Rule name</th>
						<th>Display order</th>
						<th>Update</th>
					</tr>
					@foreach($rules as $rule)
						<tr data-id = "{{$rule->id}}">
							<td><a href = "{{ url('rules/'.$rule->id) }}" class = "update" data-toggle = "tooltip"
							       data-placement = "top"
							       title = "Edit this item"><i class = "fa fa-pencil-square-o text-success"></i> </a>
								| <a href = "#" class = "delete" data-toggle = "tooltip" data-placement = "top"
								     title = "Delete this item"><i class = "fa fa-times text-danger"></i> </a></td>
							<td>{!! Form::text('rule_name', $rule->rule_name, ['class' => 'form-control rule-name']) !!}</td>
							<td>{!! Form::text('rule_display_order', $rule->rule_display_order, ['class' => 'form-control rule-display-order']) !!}</td>
							<td><a href = "#" class = "update-rule-btn btn btn-link">Update</a></td>
						</tr>
					@endforeach
				</table>
			</div>
			<div class = "col-xs-12 text-center">
				{!! $rules->render() !!}
			</div>

			{!! Form::open(['url' => url('/rules/id'), 'method' => 'delete', 'id' => 'delete-rule-form']) !!}
			{!! Form::close() !!}

			{!! Form::open(['url' => url('rules/id'), 'method' => 'put', 'id' => 'update-rule-form']) !!}
			{!! Form::hidden('rule_name', null, ['id' => 'update-rule-name']) !!}
			{!! Form::hidden('rule_display_order', null, ['id' => 'update-rule-display-order']) !!}
			{!! Form::close() !!}
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No rule found.</h3>
				</div>
			</div>
		@endif
		<div class = "col-xs-12">
			<h3 class = "page-header">Create new rule</h3>
			{!! Form::open(['url' => url('rules'), 'method' => 'post', 'class' => 'form-horizontal']) !!}
			<div class = "form-group">
				{!! Form::label('rule_name', 'Rule name: ', ['class' => 'control-label col-xs-2']) !!}
				<div class = "col-xs-5">
					{!! Form::text('rule_name', null, ['id' => 'rule_name', 'class' => "form-control", 'placeholder' => "Enter rule name"]) !!}
				</div>
				<div class = "col-xs-4">
					{!! Form::text('rule_display_order', $suggested_display_order, ['id' => 'rule_display_order', 'class' => "form-control", 'placeholder' => "Enter rule display order"]) !!}
				</div>
				{!! Form::submit('Add rule', ['class' => 'btn btn-success']) !!}
			</div>
			{!! Form::close() !!}
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
				var form = $("form#delete-rule-form");
				var url = form.attr('action');
				form.attr('action', url.replace('id', id));
				form.submit();
			}
		});

		$("a.update-rule-btn").on('click', function (event)
		{
			var tr = $(this).closest('tr');
			var rule_name = $(tr).find('td input.rule-name').val();
			var rule_display_order = $(tr).find('td input.rule-display-order').val();

			if(!rule_name){
				alert('Rule name cannot be empty!');
				return;
			}

			var rule_id = $(tr).attr('data-id');

			var form = $("form#update-rule-form");
			$(form).find('input#update-rule-name').val(rule_name);
			$(form).find('input#update-rule-display-order').val(rule_display_order);

			var url = form.attr('action');
			form.attr('action', url.replace('id', rule_id));
			form.submit();

			/*var id = $(this).closest('tr').attr('data-id');
			var form = $("form#update-rule-form");
			var url = form.attr('action');
			alert(url.replace('id', id)); return;
			var form = $("form#update-rule-form");
			var url = form.attr('action');
			form.attr('action', url.replace('id', id));
			form.submit();*/
		});
	</script>
</body>
</html>