<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Create child sku</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<style>
		td {
			width: 1px;
			white-space: nowrap;
		}

		td.description {
			white-space: pre-wrap;
			word-wrap: break-word;
			max-width: 1px;
			width: 100%;
		}

		td textarea {
			border: none;
			width: auto;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">Create child sku</li>
		</ol>
		@include('includes.error_div')
		<h3 class = "page-header">Create child sku</h3>
		@setvar($i = 0)

		<div class = "col-md-12" style = "margin-bottom: 20px;">
			{!! Form::open(['url' => url('/logistics/create_child_sku'), 'method' => 'get', 'class' => 'form-inline']) !!}
			<div class = "form-group">
				{!! Form::label('store', "Store", []) !!}
				{!! Form::select('store', $stores, request('store'), ['class'=> 'form-control', 'id' => 'store']) !!}
			</div>
			<div class = "form-group">
				{!! Form::label('id_catalog', "ID Catalog", []) !!}
				{!! Form::text('id_catalog', $id_catalog, ['class'=> 'form-control', 'id' => 'id_catalog', 'placeholder' => 'Enter ID Catalog']) !!}
			</div>
			{!! Form::submit('Pull', ['class' => 'btn btn-success']) !!}
			{!! Form::close() !!}
		</div>
		@if(!is_null($crawled_data) && $crawled_data[$id_catalog])
			<div class = "col-md-12">
				{!! Form::open([]) !!}
				<legend>Select Which Keys and Values you need for create Child SKUs</legend>
				<table class = "table table-bordered">
					<thead>
					<tr>
						<th>Keys</th>
						<th>Values</th>
					</tr>
					</thead>
					<tbody>
					@foreach($crawled_data[$id_catalog] as $node)
						<tr>
							@if($node['type'] == 'text')
								<td>{{ \Monogram\Helper::specialCharsRemover($node['label']) }}</td>
								<td></td>
							@elseif($node['type'] == 'select')
								@setvar($label = \Monogram\Helper::specialCharsRemover($node['label']))
								{!! Form::hidden('groups[]', $label) !!}
								<td>{{ $label }}</td>
								<td>
									<ul>
										@foreach(\Monogram\Helper::crawledOptionValueSplitter($node['options']) as $option)
											<div class = "checkbox">
												<label>
													{!! Form::checkbox(sprintf("%s[]", $label), 1, false) !!} {{ $option['text'] }}
												</label>
											</div>
										@endforeach
									</ul>
								</td>
							@endif
						</tr>
					@endforeach
					</tbody>
					<tfoot>
					<tr>
						<td colspan = "2" class = "text-right">
							{!! Form::submit('Preview', ['class' => 'btn btn-primary btn-sm']) !!}
						</td>
					</tr>
					</tfoot>
				</table>
				{!! Form::close() !!}
			</div>
		@elseif(!$crawled_data[$id_catalog] && $id_catalog)
			<div class = "col-md-12">
				<div class = "alert alert-warning">
					No content is available.
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
		$(".delete-sku_converter").on('click', function (event)
		{
			event.preventDefault();
			var action = confirm(message.delete);
			if ( action ) {
				$(this).closest('form').submit();
			}
			//return false;
		});
	</script>
</body>
</html>