<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Batch preview</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.3/css/bootstrap-select.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<style>
		table {
			table-layout: fixed;
			font-size: 11px;
		}

		td {
			width: auto;
		}

		img {
			width: 50px;
			height: 50px;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container" style = "margin-left: 50px;">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">Batch rotes</li>
		</ol>

		@include('includes.error_div')
		@include('includes.success_div')

		@if(count($batch_routes) > 0)
			{!! Form::open(['url' => url('items/batch'), 'method' => 'post']) !!}
			<div class = "col-xs-12">
				<div class = "checkbox pull-left">
					<label>
						{!! Form::checkbox('select-deselect', 1, false, ['id' => 'select-deselect']) !!} Select add /
						                                                                                 Deselect all
					</label>
				</div>
				<a style = "margin-bottom:20px; margin-left: 10px;" class = "btn btn-success btn-sm pull-left"
				   href = "{{url('/items')}}">Back to Item Page</a>
				<div class = "form-group pull-right">
					{!! Form::submit('Create batch', ['class' => 'btn btn-success']) !!}
				</div>
			</div>
			<div class = "row">
				<div class = "col-xs-12">
					<table class = "table">
						<tr>
							<th>Batch#</th>
							<th>S.L#</th>
							<th>Batch S.L#</th>
							<th>Route</th>
							<th>ID</th>
							<th>Order date</th>
							<th>SKU</th>
							<th>Quantity</th>
						</tr>
					</table>
				</div>
				@foreach($batch_routes as $batch_route)
					@if($batch_route->itemGroups)
						@if($batch_route->batch_max_units)
							@setvar($mixed_groups = $batch_route->itemGroups->groupBy('allow_mixing'))
							@foreach($mixed_groups as $group_key => $group_values) {{-- $group_key = 0/no mix, = 1 / mix --}}
							@if($group_key == 0) {{-- Allow mixing is not permissible--}}
							@foreach($group_values->groupBy('child_sku') as $row)
								@foreach($row->chunk($batch_route->batch_max_units) as $chunkedRows)
									@if($batch_route->stations_list->count())
										<div class = "col-xs-12">
											<table class = "table" style = "margin-top: 5px;">
												<tr data-id = "{{$batch_route->id}}">
													<td>{{ $count }}</td>
													<td></td>
													<td colspan="2" >
														<div class = "checkbox">
															<label>
																{!! Form::checkbox('select-deselect', 1, false, ['class' => 'group-select']) !!} {{$batch_route->batch_code}} = {{ $batch_route->batch_route_name }}
															</label>
														</div>
													</td>
													<td colspan="3" >> Next station >>> {{$batch_route->stations_list[0]->station_name}} ( {{$batch_route->stations_list[0]->station_description}} )</td>
													<td></td>
												</tr>
												@setvar($row_serial = 1)
												@foreach($chunkedRows->sortBy('product_model') as $item)
													<tr>
														<td><img src = "{{$item->item_thumb}}" /></td>
														<td>{{$serial++}}</td>
														<td>{{$row_serial++}}</td>
														<td>{!! Form::checkbox('batches[]', sprintf("%s|%s|%s", $count, $batch_route->id, /*$item->product_table_id, */$item->item_table_id) ,false, ['class' => 'checkable']) !!}</td>
														<td>
															<a href = "{{url("orders/details/$item->order_id")}}"
															   target = "_blank">{{\Monogram\Helper::orderIdFormatter($item, "order_table_id")}}</a>
														</td>
														<td>{{substr($item->order_date, 0, 10)}}</td>
														<td>
															<a href = "{{ url(sprintf("logistics/sku_show?store_id=%s&search_for=%s&search_in=child_sku", $item->store_id, $item->child_sku)) }}"
															   target = "_blank">{{$item->child_sku}}</a>
														</td>
														<td>{{$item->item_quantity}}</td>
													</tr>
												@endforeach
												<tr>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td>
														<span class = "item_selected">0</span> of <span
																class = "item_total">{{$batch_route->batch_max_units}}</span>
													</td>
												</tr>
												@setvar(++$count)
											</table>
										</div>
									@endif
								@endforeach
							@endforeach
							@else
								@foreach($group_values->chunk($batch_route->batch_max_units) as $chunkedRows)
									@if($batch_route->stations_list->count())
										<div class = "col-xs-12">
											<table class = "table" style = "margin-top: 5px;">
												<tr data-id = "{{$batch_route->id}}">
													<td>{{ $count }}</td>
													<td></td>
													<td colspan="2" >
														<div class = "checkbox">
															<label>
																{!! Form::checkbox('select-deselect', 1, false, ['class' => 'group-select']) !!} {{$batch_route->batch_code}} = {{ $batch_route->batch_route_name }}
															</label>
														</div>
													</td>
													<td colspan="3" >Next station >>> {{$batch_route->stations_list[0]->station_name}} ( {{$batch_route->stations_list[0]->station_description}} )</td>
													<td></td>
												</tr>
												@setvar($row_serial = 1)
												@foreach($chunkedRows->sortBy('product_model') as $item)
													<tr>
														<td><img src = "{{$item->item_thumb}}" /></td>
														<td>{{$serial++}}</td>
														<td>{{$row_serial++}}</td>
														<td>{!! Form::checkbox('batches[]', sprintf("%s|%s|%s", $count, $batch_route->id, /*$item->product_table_id, */$item->item_table_id) ,false, ['class' => 'checkable']) !!}</td>
														<td>
															<a href = "{{url("orders/details/$item->order_id")}}"
															   target = "_blank">{{\Monogram\Helper::orderIdFormatter($item, "order_table_id")}}</a>
																<br>
								   							<a href = "{{ url("orders/details/".$item->order_id) }}"
															   target = "_blank">{{\Monogram\Helper::itemOrderNameFormatter($item)}}
															</a>

														</td>
														<td>{{substr($item->order_date, 0, 10)}}</td>
														<td>
															<a href = "{{ url(sprintf("logistics/sku_show?store_id=yhst-128796189915726&search_for=%s&search_in=child_sku", $item->child_sku)) }}"
															   target = "_blank">{{$item->child_sku}}</a>
														</td>
														<td>{{$item->item_quantity}}</td>
													</tr>
												@endforeach
												<tr>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td>
														<span class = "item_selected">0</span> of <span
																class = "item_total">{{$batch_route->batch_max_units}}</span>
													</td>
												</tr>
												@setvar(++$count)
											</table>
										</div>
									@endif
								@endforeach
							@endif
							@endforeach
						@endif
					@endif
				@endforeach
			</div>
			<div class = "col-xs-12">
				<div class = "checkbox pull-left">
					<label>
						{!! Form::checkbox('select-deselect', 1, false, ['id' => 'select-deselect']) !!} Select add /
						                                                                                 Deselect all
					</label>
				</div>
				<div class = "form-group pull-right">
					{!! Form::submit('Create batch', ['class' => 'btn btn-success']) !!}
				</div>
			</div>
			{!! Form::close() !!}
		@else
			<div class = "col-xs-12">
				<div class = "alert alert-warning text-center">
					<h3>No batch to create.</h3>
				</div>
			</div>
		@endif
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type = "text/javascript">
		var state = false;
		$("input#select-deselect").click(function (event)
		{
			state = !state;
			$("input[type='checkbox']").not($(this)).prop('checked', state);
			$("table").each(function ()
			{
				updateTableInfo($(this));
			});
		});
		$("input.group-select").on("click", function (event)
		{
			var table = $(this).closest('table');
			var state = $(this).prop('checked');
			table.find('tr').not(':first').not(':last').each(function ()
			{
				$(this).find('input:checkbox').prop('checked', state);
			});

			updateTableInfo(table);
		});
		$("input.checkable").not('input#select-deselect, input.group-select').on('click', function (event)
		{
			var table = $(this).closest('table');
			var item_selected = getSelectedItemCount(table);
			var item_total = table.find('tr').not(':first').not(':last').length;
			//$(table).find('span.item_selected').text(item_selected);
			updateTableInfo(table);
			$(table).find('tr').eq(0).find('input:checkbox').prop('checked', item_selected == item_total);
		});

		function updateTableInfo (table)
		{
			$(table).find('span.item_selected').text(getSelectedItemCount(table));
		}

		function getSelectedItemCount (table)
		{
			var total_selected = 0;
			table.find('tr').not(':first').not(':last').each(function ()
			{
				if ( $(this).find('input:checkbox').prop('checked') == true ) {
					++total_selected;
				}
			});
			return total_selected;
		}
	</script>
</body>
</html>