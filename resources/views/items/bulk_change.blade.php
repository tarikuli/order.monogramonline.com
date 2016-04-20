<!doctype html>
<!--suppress JSUnresolvedVariable -->
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Bulk batch update</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "/assets/css/bootstrap-horizon.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<style>
		.parent-selector {
			width: 200px;
			overflow: auto;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">Bulk batch station update</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')

		{!! Form::open(['url' => url('stations/bulk'), 'method' => 'post', 'class'=>'form-horizontal', 'role'=>'form']) !!}
		<div class = "form-group">
			{!!Form::label('station','Station',['class'=>'control-label col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::select('station', $stations, null, ['id' => 'station','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			{!!Form::label('batches','Batches',['class'=>'control-label col-xs-2'])!!}
			<div class = "col-xs-5">
				{!! Form::textarea('batches', null, ['id' => 'batches','class'=>'form-control']) !!}
			</div>
		</div>
		<div class = "form-group">
			<div class = "col-md-offset-2 col-md-2">
				{!! Form::submit('Change station',['class'=>'btn btn-primary']) !!}
			</div>
		</div>
		{!! Form::close() !!}
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

	<script type = "text/javascript">
		$(document).on('change', "select.parent-selector", function (event)
		{
			var node = $(this);
			var selected_parent_category = parseInt($(this).val());
			delete_next(node);

			if ( !selected_parent_category ) {
				var parent_id = $(this).closest('div.col-sm-4').attr('data-parent');
				set_parent_category(parent_id);
				return false;
			}

			set_parent_category(selected_parent_category);
			ajax_performer(selected_parent_category, node);
		});

		function delete_next (node)
		{
			$(node).closest('div.col-sm-4').nextAll().each(function ()
			{
				$(this).remove();
			});
		}

		function set_parent_category (val)
		{
			$("#product_master_category").val(val);
		}

		function set_select_form_data (node, data)
		{
			$(node).closest('div.col-sm-4').after(data);
		}

		function ajax_performer (category_id, node)
		{
			var url = "/master_categories/get_next/" + category_id;
			var method = "GET";
			$.ajax({
				method: method, url: url, success: function (data, status, xhr)
				{
					var select_form_data = data.select_form_data;
					set_select_form_data(node, select_form_data);
				}, error: function (xhr, status, error)
				{
					alert("Something went wrong!!");
				}
			});
		}

	</script>
</body>
</html>