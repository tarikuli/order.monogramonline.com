<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Update rule</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<style>

	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('/rules')}}">Rules</a></li>
			<li class = "active">Update => {{$rule->rule_name}}</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		{!! Form::open(['url' => url(sprintf("rules/bulk_update/%d", $rule->id)), 'id' => 'rule-update', 'method' => 'put', 'class' => 'form-horizontal' ]) !!}
		{{-- Shipping rule triggers --}}
		<div class = "col-xs-12">
			<table class = "table" id = "rule-trigger-table">
				<caption><h4>Shipping rule triggers</h4></caption>
				<thead>
				<tr>
					<th>Remove</th>
					<th>Parameter</th>
					<th>Relation</th>
					<th>Value</th>
				</tr>
				</thead>
				<tbody id = "rule-trigger-table-rows">
				@if(count($rule->triggers))
					@foreach($rule->triggers as $trigger)
						<tr>
							<td>
								<span class = "text-danger delete-row" data-toggle = "tooltip" data-placement = "top"
								      title = "Delete trigger"><i class = "fa fa-times"></i> </span>
							</td>
							<td>{!! Form::select('trigger_type[]', $trigger_parameters, $trigger->rule_trigger_parameter, ['class' => 'form-control changable-rule-trigger-parameter', 'disabled' => 'disabled']) !!}</td>
							<td>{!! Form::select('trigger_relation[]', $trigger_relations, $trigger->rule_trigger_relation, ['class' => 'form-control']) !!}</td>
							{{--<td>{!! Form::text('trigger_value[]', $trigger->rule_trigger_value, ['class' => 'form-control']) !!}</td>--}}
							<td>{!! $that->get_view_for_trigger_option($trigger->rule_trigger_parameter, $trigger->rule_trigger_value) !!}</td>
						</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
		<div class = "col-xs-12">
			<div class = "form-group pull-right">
				{!! Form::button('Add trigger row', ['id' => 'add-new-row-to-trigger-table', 'class' => 'btn btn-info']) !!}
			</div>
		</div>
		{{-- Shipping rule actions --}}
		<div class = "col-xs-12">
			<table class = "table" id = "rule-action-table">
				<caption><h4>Shipping rule actions</h4></caption>
				<thead>
				<tr>
					<th>Remove</th>
					<th>Parameter</th>
					<th>Value</th>
				</tr>
				</thead>
				<tbody id = "rule-action-table-rows">
				@if(count($rule->actions))
					@foreach($rule->actions as $action)
						<tr>
							<td>
								<span class = "text-danger delete-row" data-toggle = "tooltip" data-placement = "top"
								      title = "Delete action"><i class = "fa fa-times"></i> </span>
							</td>
							<td>{!! Form::select('action_type[]', $action_parameters, $action->rule_action_parameter, ['class' => 'form-control changable-rule-action']) !!}</td>
							{{--<td>{!! Form::text('action_value[]', $action->rule_action_value, ['class' => 'form-control']) !!}</td>--}}
							<td>{!! $that->get_view_for_action($action->rule_action_parameter, $action->rule_action_value) !!}</td>
						</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
		<div class = "col-xs-12">
			<div class = "form-group pull-right">
				{!! Form::button('Add action row', ['id' => 'add-new-row-to-action-table', 'class' => 'btn btn-info']) !!}
			</div>
		</div>

		<div class = "col-xs-12">
			<div class = "form-group pull-right">
				{!! Form::submit('Update', ['id' => 'update', 'class' => 'btn btn-success']) !!}
			</div>
		</div>
		{!! Form::close() !!}
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		var message = {
			delete: 'Are you sure you want to delete?',
		};

		$(function ()
		{
			$("body").tooltip({selector: '[data-toggle="tooltip"]'});
		});

		$("body").on('change', 'select.changable-rule-trigger-parameter', function ()
		{
			var value = $(this).val();
			if ( value == "null" ) {
				alert("Not a valid Parameter");
				return;
			}
			var id = $(this).closest('tr').attr('data-id');
			var formUrl = '{{url('rules/parameter')}}';
			var that = $(this);

			$.ajax({
				method: 'GET', url: formUrl, data: {
					option: value
				}, success: function (data, textStatus, xhr)
				{
					var td = $(that).closest('tr').find('td:eq(3)');
					$(td).empty();
					$(td).html(data);
				}, error: function (xhr, textStatus, errorThrown)
				{
					alert('Could not update product route');
				}
			});
		});

		$("body").on('change', 'select.changable-rule-action', function ()
		{
			var value = $(this).val();
			if ( value == "null" ) {
				alert("Not a valid Parameter");
				return;
			}
			var id = $(this).closest('tr').attr('data-id');
			var formUrl = '{{url('rules/actions')}}';
			var that = $(this);

			$.ajax({
				method: 'GET', url: formUrl, data: {
					option: value
				}, success: function (data, textStatus, xhr)
				{
					var td = $(that).closest('tr').find('td:eq(2)');
					$(td).empty();
					$(td).html(data);

				}, error: function (xhr, textStatus, errorThrown)
				{
					alert('Could not update product route');
				}
			});
		});

		$("body").on('click', "span.delete-row", function (event)
		{
			event.preventDefault();
			var answer = confirm(message.delete);
			if ( answer ) {
				$(this).closest('tr').remove();
			}
		});

		function get_trigger_row ()
		{
			var row = '<tr>\
							<td>\
								<span class="text-danger delete-row" data-toggle="tooltip" data-placement="top" title="Delete trigger"><i class="fa fa-times"></i> </span>\
							</td>\
						<td>\
							<select class="form-control changable-rule-trigger-parameter" name="trigger_type[]">\
								<option value="">Select Parameter</option>\
								<option value="VAL">Items Value ($)</option>\
								<option value="OT">Order total ($)</option>\
								<option value="NUM">Number of items</option>\
								<option value="DOM">Domestic/International</option>\
								<option value="WGT">Weight (Lbs.)</option>\
								<option value="SHIP">Selected shipping method by customer</option>\
								<option value="STAT">Ship to state list</option>\
								<option value="SKU">SKUs list</option>\
								<option value="MKT">Store</option>\
							</select>\
						</td>\
						<td>\
							<select class="form-control" name="trigger_relation[]">\
								<option value="<">&lt;</option>\
								<option value="<=">&lt;=</option>\
								<option value="=">=</option>\
								<option value=">">&gt;</option>\
								<option value=">=">&gt;=</option>\
								<option value="IN">IN</option>\
							</select>\
						</td>\
						<td>\
							<input class="form-control" name="trigger_value[]" type="text" >\
						</td>\
						</tr>';
			return row;
		}

		function add_new_trigger_row (place)
		{
			$(place).append($(get_trigger_row()));
		}

		$("button#add-new-row-to-trigger-table").on('click', function (event)
		{

			var tbody = $('table#rule-trigger-table tbody#rule-trigger-table-rows');
			add_new_trigger_row(tbody);
		});


		function get_action_row ()
		{
			var row = '<tr>\
							<td>\
								<span class="text-danger delete-row" data-toggle="tooltip" data-placement="top" title="Delete action"><i class="fa fa-times"></i> </span>\
							</td>\
							<td>\
								<select class="form-control changable-rule-action" name="action_type[]">\
									<option value="">Select Parameter</option>\
									<option value="CAR">Carrier</option>\
									<option value="CLS">Shipping class</option>\
									<option value="INS">Insurance</option>\
									<option value="PKG">Package shape</option>\
									<option value="SIG">Signature Confirmation</option>\
									<option value="ADW">Add weight (Oz)</option>\
								</select>\
							</td>\
							<td>\
								<input class="form-control" name="action_value[]" type="text">\
							</td>\
					</tr>';
			return row;
		}

		function add_new_action_row (place)
		{
			$(place).append($(get_action_row()));
		}

		$("button#add-new-row-to-action-table").on('click', function (event)
		{

			var tbody = $('table#rule-action-table tbody#rule-action-table-rows');
			add_new_action_row(tbody);
		});

		$("form#rule-update").on('submit', function ()
		{
			$("select.changable-rule-trigger-parameter").prop('disabled', false);
		});

	</script>
</body>
</html>