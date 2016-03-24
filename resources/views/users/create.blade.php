<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Create User</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('users')}}">Users</a></li>
			<li class = "active">Create user</li>
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


		{!! Form::open(['url' => url('/users'), 'method' => 'post','class'=>'form-horizontal','role'=>'form']) !!}
		<div class = "col-md-12">
			<ul class = "nav nav-tabs" role = "tablist">
				<li role = "presentation" class = "active">
					<a href = "#tab-info" aria-controls = "info" role = "tab" data-toggle = "tab">Info</a>
				</li>
				<li role = "presentation">
					<a href = "#tab-permission" aria-controls = "permission" role = "tab"
					   data-toggle = "tab">Permissions</a>
				</li>
			</ul>
			<div class = "tab-content" style = "margin-top: 20px;">
				<div role = "tabpanel" class = "tab-pane fade in active" id = "tab-info">
					<div class = 'form-group'>
						{!!Form::label('username','Username :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
						<div class = 'col-xs-5'>
							{!! Form::text('username', null, ['id' => 'username','class' => 'form-control']) !!}
						</div>
					</div>
					<div class = 'form-group'>
						{!!Form::label('email','Email :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::email('email', null, ['id' => 'email','class' => 'form-control']) !!}
						</div>
					</div>
					<div class = 'form-group'>
						{!!Form::label('password','Password :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::password('password', ['id' => 'password','class' => 'form-control']) !!}
						</div>
					</div>
					<div class = 'form-group'>
						{!!Form::label('vendor_id','Role :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::select('role', $roles, null, ['id' => 'vendor_id','class' => 'form-control']) !!}
						</div>
					</div>
					<div class = 'form-group'>
						{!!Form::label('vendor_id','Vendor ID :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('vendor_id', null, ['id' => 'vendor_id','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = 'form-group'>
						{!!Form::label('zip_code','Zip Code :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('zip_code', null, ['id' => 'zip_code','class'=>'form-control']) !!}
						</div>
					</div>
					<div class = 'form-group'>
						{!!Form::label('state','State :',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
						<div class = "col-xs-5">
							{!! Form::text('state', null, ['id' => 'state','class'=>'form-control']) !!}
						</div>
					</div>
				</div>
				<div role = "tabpanel" class = "tab-pane fade" id = "tab-permission">
					<div class = "form-group">
						{!!Form::label('user_access','User access: ',['class'=>'control-label col-xs-2'])!!}
						<div class = "col-xs-10">
							@setvar($i = 1)
							@foreach(\App\Access::$pages as $link => $text)
								<div class = "checkbox">
									<label>
										{!! Form::checkbox('user_access[]', $link, false, ['id' => sprintf('user_access-%d', $i),'class'=>'checkbox access-control-checkbox']) !!} {{ $text }}
									</label>
								</div>
							@endforeach
						</div>
					</div>
					<div class = "form-group">
						<div class = "col-md-10 col-md-offset-2">
							<div class = "checkbox">
								<label>
									{!! Form::checkbox('select-deselect-all', '1', false, ['id' => 'select-deselect-all']) !!} Select/Deselect All
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class = 'form-group'>
				<div class = "col-xs-12 text-right">
					{!! Form::submit('Create User',['class'=>'btn btn-primary']) !!}
				</div>
			</div>
		</div>


		{!! Form::close() !!}
	</div>
	<script src = "//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		var state = false;
		$("input#select-deselect-all").on('click', function (event)
		{
			state = !state;
			$("input.access-control-checkbox").prop('checked', state);
		});
	</script>
</body>
</html>