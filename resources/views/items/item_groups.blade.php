<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Batch list</title>
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
			font-size: 12px;
		}

		td {
			width: auto;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li class = "active">Batch list</li>
		</ol>

		<div class = "col-xs-12">
			@if(count($itemGroups))
				@foreach($itemGroups as $itemGroup)
					<table class = "table">
						<caption>{{ $count++ }}. Batch: {{$itemGroup->batch_number}}</caption>
						<tr>
							<th>Serial#</th>
							<th>Order id</th>
							<th>Order date</th>
							<th>SKU</th>
						</tr>
						@foreach($itemGroup->groupedItems as $items)
							<tr>
								<td>{{ $serial++ }}</td>
								<td>{{ $items->order_id }}</td>
								<td>{{ $items->order->order_date }}</td>
								<td>{{ $items->item_id }}</td>
							</tr>
						@endforeach
						@setvar($serial = 1)
					</table>
				@endforeach
			@else
				<div class="alert alert-warning">No batch is created</div>
			@endif
		</div>

		<div class = "col-xs-12 text-center">
			{!! $itemGroups->render() !!}
		</div>

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