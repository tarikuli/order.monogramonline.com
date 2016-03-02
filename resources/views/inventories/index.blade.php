<!doctype html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Inventories</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link type = "text/css" rel = "stylesheet"
	      href = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
	<style>
		td {
			width: 1px;
			white-space: nowrap;
		}

		li {
			font-size: 10px;
		}
	</style>
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		<ol class = "breadcrumb">
			<li><a href = "{{url('/')}}">Home</a></li>
			<li><a href = "{{url('inventories')}}" class = "active">Inventories</a></li>
		</ol>
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "row">
			<div class = "col-md-7">
				<div class = "row">
					<div class = "col-md-12">
						{!! Form::open(['url' => url('imports/inventory'), 'files' => true]) !!}
						{!! Form::hidden('todo', null, ['id' => 'todo']) !!}
						<legend>Import inventory file</legend>
						<div class = "form-group">
							{!! Form::select('inventory_index', $inventory_indexes, 0, ['id' => 'inventory_index', 'class' => 'form-control']) !!}
						</div>
						<div class = "form-group">
							{!! Form::file('attached_csv', ['id' => 'attached_csv', 'class' => 'form-control', 'accept' => '.csv']) !!}
						</div>
						<div class = "form-group">
							{!! Form::button('Validate file', ['class' => 'btn btn-sm btn-default', 'id' => 'validate-file']) !!}
							{!! Form::button('Upload file', ['class' => 'btn btn-sm btn-success', 'id' => 'upload-file']) !!}
						</div>
						{!! Form::close() !!}
					</div>
					<div class = "col-md-12">
						{!! Form::open(['url' => url('exports/inventory'), 'method' => 'get']) !!}
						<legend>Export inventory file</legend>
						<div class = "form-group">
							{!! Form::label('export_inventory_index', 'Filter export by shipper =>', ['class' => 'control-label']) !!}
							{!! Form::select('export_inventory_index', $inventory_indexes, 0, ['id' => 'export_inventory_index', 'class' => 'form-control']) !!}
						</div>
						<div class = "form-group">
							{!! Form::submit('Export', ['class' => 'btn btn-sm btn-primary']) !!}
						</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
			<div class = "col-md-5">
				<div class = "row">
					<strong>Batch Inventory upload guide:</strong>
					<br />
					<strong>Local Warehouse uploads:</strong>
					<ol>
						<li>
							Add new SKUs to your local warehouse (local WH):
							The necessary file columns: idCatalog, sku (NO vendorId column)
							*Select the local WH prior to uploading the file.
						</li>

						<li>
							Update existing SKUs, quantities and functions for your local warehouse (local WH):
							The necessary file columns: idCatalog, sku (NO vendorId column)
							*Select the local WH prior to uploading the file.
						</li>
					</ol>
					<br />
					<strong>Drop Ship Vendor uploads:</strong>
					<ol>
						<li>
							To add new SKUs for a specific vendor:
							The necessary file columns: idCatalog, vendorId, sku
							*Select the vendor prior to uploading the file.
						</li>

						<li>
							Update existing SKUs, quantities and functions for a specific vendor:
							The necessary file columns: idCatalog, vendorId, sku
							*Select the vendor prior to uploading the file.
							<br />
							* Check the box - Only update
							<ul>
								<li>Create a CSV file</li>
								<li>The first row should contain the column names as follows:
									<ul>
										<li>id_catalog - unique product part #</li>
										<li>parent_item - item master code/style (can be used instead of the item id, id_catalog => please don't use both columns)</li>
										<li>vendor_id - Shipper's ID</li>
										<li>warehouse - location of the product, warehouse Code</li>
										<li>wh_bin - BIN location of the product</li>
										<li>sku - item SKU</li>
										<li>upc - item UPC</li>
										<li>vendor_sku - Supplier's sku</li>
										<li>desc - short description</li>
										<li>sku_cost - 999.99 (no $ sign)</li>
										<li>sku_cost_sup - SKU cost in supplier currency</li>
										<li>sku_weight - item weight</li>
										<li>re_order_qty - reorder point qty (units)</li>
										<li>min_reorder - re-order qty (units)</li>
										<li>qty_on_hand - qty on hand</li>
										<li style = "text-decoration: underline; font-weight: bold;">qty_frozen - qty frozen (optional)</li>
										<li>sku_status - 0- Inactive, 1- Active, 2 - Discontinued</li>
										<li>rt_status - real time inventory for the item 0-Inactive, 1-Active</li>
										<li>inv_amazon - update Amazon inventory for the item 0-No, 1-Yes</li>
										<li>feed_houzz - update Houzz inventory for the item 0-No, 1-Yes</li>
										<li>feed_jet - update JET.com inventory for the item 0-No, 1-Yes</li>
										<li>order_qty_size - Order quantity size</li>
										<li>inv_ebay - zero - exclude from feed; feed qty if qty/av >= qty</li>
										<li style = "text-decoration: underline; font-weight: bold;">lead_time - Lead time for shipping (days)</li>
										<li style = "text-decoration: underline; font-weight: bold;">exp_date - SKU's expected time</li>
										<li>ASIN - Amazon's ASIN</li>
										<li>amz_price - Price on Amazon</li>
										<li>ebay_price - Price on eBay</li>
										<li>buy_com_price - Price on Rakuten (buy.com)</li>
										<li>del - if set as 1 remove this SKU from the inventory file</li>
									</ul>
								</li>
								<li>Columns order is not mandatory EXCEPT The file MUST have at least two columns and the first column MUST be the idCatalog</li>
								<li>
									<strong>*vendorId</strong> must be included to update a specific drop shipper inventory
								</li>
							</ul>
						</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	{{--<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script type = "text/javascript" src = "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>--}}
	<script type = "text/javascript">
		$("#validate-file").on('click', function ()
		{
			$("#todo").val('validate');
			$(this).closest('form').submit();
		});
		$("#upload-file").on('click', function ()
		{
			$("#todo").val('upload');
			$(this).closest('form').submit();
		});
	</script>

</body>
</html>