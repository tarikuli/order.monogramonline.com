<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Categories</title>
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
			<li class = "active">Categories</li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "col-xs-12" style = "margin: 10px 0;">
			{!! Form::open(['url' => url('master_categories/ID'), 'id' => 'modify_master_category', 'method' => 'put', 'class' => 'form-horizontal']) !!}
			{!! Form::hidden('modified_code', null, ['id' => 'modified_code', 'class' => 'form-control']) !!}
			{!! Form::hidden('modified_description', null, ['id' => 'modified_description', 'class' => 'form-control']) !!}
			{!! Form::hidden('modified_display_order', null, ['id' => 'modified_display_order', 'class' => 'form-control']) !!}
			{!! Form::close() !!}

			{!! Form::open(['url' => url('/master_categories'), 'method' => 'post']) !!}
			<div class = "form-group col-xs-12">
				{!! Form::label('parent_category', 'Parent', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-sm-3">
					{!! Form::number('parent_category', 0, ['id' => 'parent_category', 'class' => "form-control", 'readonly' => "readonly"]) !!}
					{!! Form::text('new_code', null, ['id' => 'new_code', 'class' => 'form-control', 'placeholder' => 'Category code']) !!}
					{!! Form::text('new_description', null, ['id' => 'new_description', 'class' => 'form-control', 'placeholder' => 'Category description']) !!}
					{!! Form::text('new_display_order', null, ['id' => 'new_display_order', 'class' => 'form-control', 'placeholder' => 'Category display order']) !!}
					{!! Form::button('Update', ['id' => 'modify_update']) !!}
				</div>
			</div>
			<div class = "form-group col-xs-12">
				{!! Form::label('category', 'category', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-sm-10">
					@include('master_categories.ajax_category_response')
				</div>
			</div>
			<div class = "form-group col-xs-12">
				{!! Form::label('master_category_code', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-sm-4">
					{!! Form::text('master_category_code', null, ['id' => 'master_category_code', 'class' => "form-control", 'placeholder' => "Enter category code"]) !!}
				</div>
			</div>
			<div class = "form-group col-xs-12">
				{!! Form::label('master_category_description', 'Description', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-sm-4">
					{!! Form::text('master_category_description', null, ['id' => 'master_category_description', 'class' => "form-control", 'placeholder' => "Enter category description"]) !!}
				</div>
			</div>
			<div class = "form-group col-xs-12">
				{!! Form::label('master_category_display_order', 'Display order', ['class' => 'col-xs-2 control-label']) !!}
				<div class = "col-sm-4">
					{!! Form::text('master_category_display_order', 0, ['id' => 'master_category_display_order', 'class' => "form-control", 'placeholder' => "Enter category display order"]) !!}
				</div>
			</div>
			<div class = "col-xs-12 apply-margin-top-bottom ">
				<div class = "col-xs-offset-2 col-xs-4">
					{!! Form::submit('Create category', ['class' => 'btn btn-primary btn-block']) !!}
				</div>
			</div>
			{!! Form::close() !!}
		</div>
		@if(count($master_categories) > 0)
			<div class = "col-xs-12">
				<table class = "table table-bordered">
					<tr>
						<th>#</th>
						<th>category code</th>
						<th>category description</th>
						<th>category display order</th>
						<th>Action</th>
					</tr>
					@foreach($master_categories as $category)
						<tr data-id = "{{$category->id}}">
							<td>{{ $count++ }}</td>
							<td>
								<input class = "form-control" name = "category_code" type = "text"
								       value = "{{$category->master_category_code}}">
							</td>
							<td>
								<input class = "form-control" name = "category_description" type = "text"
								       value = "{{$category->master_category_description}}">
							</td>
							<td>
								<input class = "form-control" name = "category_display_order" type = "text"
								       value = "{{$category->master_category_display_order}}">
							</td>
							{{--<td>{!! Form::text('category_code', $category->category_code, ['class' => 'form-control']) !!}</td>
							<td>{!! Form::text('category_description', $category->category_description, ['class' => 'form-control']) !!}</td>
							<td>{!! Form::text('category_display_order', $category->category_display_order, ['class' => 'form-control']) !!}</td>--}}
							<td>
								{{--<a href = "{{ url(sprintf("/categories/%d", $category->id)) }}" class = "btn btn-success">View</a> | --}}
								<a href = "#" class = "update" data-toggle = "tooltip" data-placement = "top"
								   title = "Edit this item"><i class = "fa fa-pencil-square-o text-success"></i> </a>
								| <a href = "#" class = "delete" data-toggle = "tooltip" data-placement = "top"
								     title = "Delete this item"><i class = "fa fa-times text-danger"></i> </a>
							</td>
						</tr>
					@endforeach
				</table>
			</div>
			<div class = "col-xs-12 text-center">
				{!! $master_categories->render() !!}
			</div>
			{!! Form::open(['url' => url('/master_categories/id'), 'method' => 'delete', 'id' => 'delete-category']) !!}
			{!! Form::close() !!}

			{!! Form::open(['url' => url('/master_categories/id'), 'method' => 'put', 'id' => 'update-category']) !!}
			{!! Form::hidden('master_category_code', null, ['id' => 'update_category_code']) !!}
			{!! Form::hidden('master_category_description', null, ['id' => 'update_category_description']) !!}
			{!! Form::hidden('master_category_display_order', null, ['id' => 'update_category_display_order']) !!}
			{!! Form::close() !!}
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No category found.</h3>
				</div>
			</div>
		@endif
		<div class = "col-xs-12 text-right" style = "margin: 10px 0;">
			<button class = "btn btn-success" type = "button" data-toggle = "collapse"
			        data-target = "#collapsible-bottom"
			        aria-expanded = "false" aria-controls = "collapsible">Create new category
			</button>
			<div class = "collapse text-left" id = "collapsible-bottom">
				{!! Form::open(['url' => url('/master_categories'), 'method' => 'post']) !!}
				<div class = "form-group col-xs-12">
					{!! Form::label('master_category_code', 'Category code', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-sm-4">
						{!! Form::text('master_category_code', null, ['id' => 'category_code', 'class' => "form-control", 'placeholder' => "Enter category code"]) !!}
					</div>
				</div>
				<div class = "form-group col-xs-12">
					{!! Form::label('master_category_description', 'Description', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-sm-4">
						{!! Form::text('master_category_description', null, ['id' => 'category_description', 'class' => "form-control", 'placeholder' => "Enter category description"]) !!}
					</div>
				</div>
				<div class = "form-group col-xs-12">
					{!! Form::label('master_category_display_order', 'Display order', ['class' => 'col-xs-2 control-label']) !!}
					<div class = "col-sm-4">
						{!! Form::text('master_category_display_order', 0, ['id' => 'category_display_order', 'class' => "form-control", 'placeholder' => "Enter category display order"]) !!}
					</div>
				</div>
				<div class = "col-xs-12 apply-margin-top-bottom">
					<div class = "col-xs-offset-2 col-xs-4">
						{!! Form::submit('Create category', ['class' => 'btn btn-primary btn-block']) !!}
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	{{--{!! Form::number('parent_category', 0, ['id' => 'parent_category', 'class' => "form-control", 'readonly' => "readonly"]) !!}
	{!! Form::text('new_code', null, ['id' => 'modified_code', 'class' => 'form-control']) !!}
	{!! Form::text('new_description', null, ['id' => 'modified_description', 'class' => 'form-control']) !!}
	{!! Form::text('new_display_order', null, ['id' => 'modified_display_order', 'class' => 'form-control']) !!}
	{!! Form::button('Update', ['id' => 'modify_update']) !!}

	{!! Form::open(['url' => url('master_categories/ID/update'), 'id' => 'modify_master_category', 'method' => 'put', 'class' => 'form-horizontal']) !!}
	{!! Form::hidden('modified_code', null, ['id' => 'modified_code', 'class' => 'form-control']) !!}
	{!! Form::hidden('modified_description', null, ['id' => 'modified_description', 'class' => 'form-control']) !!}
	{!! Form::hidden('modified_display_order', null, ['id' => 'modified_display_order', 'class' => 'form-control']) !!}
	{!! Form::close() !!}--}}
	<script type = "text/javascript">
		$(function ()
		{
			$('[data-toggle="tooltip"]').tooltip();
		});

		$('form').on('keyup keypress', function(e) {
			var keyCode = e.keyCode || e.which;
			if (keyCode === 13) {
				e.preventDefault();
				return false;
			}
		});

		$("button#modify_update").on('click', function (event)
		{
			event.preventDefault();

			var id = $("#parent_category").val();
			var url = "master_categories/" + id;

			var form = $("form#modify_master_category");
			form.attr('action', url);

			var modified_code = $("#new_code").val();
			$("input#modified_code").val(modified_code);

			var modified_description = $("#new_description").val();
			$("input#modified_description").val(modified_description);

			var modified_display_order = $("#new_display_order").val();
			$("input#modified_display_order").val(modified_display_order);
			$(form).submit();
		});

		function enable_form ()
		{

			$("button#modify_update").prop('disabled', false);
		}

		function disable_form ()
		{
			var url = "master_categories/ID";
			var form = $("form#modify_master_category");
			form.attr('action', url);
			$("input#modified_code").val("");
			$("input#modified_description").val("");
			$("input#modified_display_order").val("");
			$("button#modify_update").prop('disabled', true);

			$("input#new_code").val("");
			$("input#new_description").val("");
			$("input#new_display_order").val("");
		}

		function set_form_data (clicked)
		{
			var node = $(clicked).find('option:selected');

			var new_code = $(node).attr('data-code');
			$("#new_code").val(new_code);

			var new_description = $(node).text().trim();
			$("#new_description").val(new_description);

			var new_display_order = $(node).attr('data-display-order');
			$("#new_display_order").val(new_display_order);
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
				var form = $("form#delete-category");
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
			var description = tr.find('input').eq(1).val();
			var order = tr.find('input').eq(2).val();

			$("input#update_category_code").val(code);
			$("input#update_category_description").val(description);
			$("input#update_category_display_order").val(order);
			var form = $("form#update-category");
			var url = form.attr('action');
			form.attr('action', url.replace('id', id));
			form.submit();
		});

		$(document).on('change', "select.parent-selector", function (event)
		{
			var node = $(this);
			var selected_parent_category = parseInt($(this).val());
			delete_next(node);
			if ( !selected_parent_category ) {
				disable_form();
				var parent_id = $(this).closest('div.col-sm-3').attr('data-parent');
				set_parent_category(parent_id);
				return false;
			}
			enable_form();
			set_form_data($(this));
			set_parent_category(selected_parent_category);
			ajax_performer(selected_parent_category, node);
		});

		function set_parent_category (val)
		{
			if ( val == 0 ) {
				var url = "master_categories/ID/update";
				$("form#modify_master_category").attr('action', url);
			}
			$("#parent_category").val(val);
		}

		function delete_next (node)
		{
			$(node).closest('div.col-sm-3').nextAll().each(function ()
			{
				$(this).remove();
			});
		}

		function ajax_performer (category_id, node)
		{
			var url = "/master_categories/get_next/" + category_id;
			var method = "GET";
			$.ajax({
				method: method, url: url, success: function (data, status, xhr)
				{
					$(node).closest('div.col-sm-3').after(data);
				}, error: function (xhr, status, error)
				{
					alert("Something going wrong");
				}
			});
		}
	</script>
</body>
</html>