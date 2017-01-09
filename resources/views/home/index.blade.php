<!DOCTYPE html>
<html>
<head>
	<title>{{env('APPLICATION_NAME')}} - Home</title>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link type = "text/css" rel = "stylesheet"
	      href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
	@include('includes.header_menu')
	<div class = "container">
		@include('includes.error_div')
		@include('includes.success_div')
		<div class = "col-xs-12">
		<b>Notice Board:</b>
		{{-- 
		<div class="alert alert-danger">
		  <strong>Danger!</strong> Please don't use Endicia until this message delete
		</div>
		--}}
		</div>
		<div class = "col-xs-6">
			<div class = "col-xs-12">
				<h5 class = "page-header">Users Management</h5>
				<ul>
					<li><a href = "/users">Users</a></li>
					@if(auth()->user()->roles->first()->id == 1)
						<li><a href = "/users/create">Create user</a></li>
					@endif
					<li><a href = "/customers">Customers</a></li>
					<li><a href = "/customers/create">Create Customer</a></li>
					<li><a href = "/vendors">Vendors</a></li>
					<li><a href = "/vendors/create">Create vendor</a></li>
					<li><a href = "/purchasedinvproducts"><strong>Purchase Inventory Products List</strong></a></li>
					<li><a href = "/purchases">Purchases</a></li>
					<li><a href = "/purchases/create">Add purchase</a></li>
				</ul>
			</div>
			<div class = "col-xs-12">
				<h5 class = "page-header">Logistics</h5>
				<ul>
					<li><a href = "/logistics/sku_converter">Set store options to SKU conversion parameters</a></li>
					<li><a href = "/logistics/sku_import">Export/Import options coded SKUs CSV file</a></li>
					{{-- <li><a href = "/logs">Station logs</a></li> --}}
					<li><a href = "/logistics/create_child_sku">Create Child SKU</a></li>
					<li><a href = "/email_templates">Email templates</a></li>
					<li><a href = "/logistics/reset_sorting">Reset Sorting</a></li>
					<li><a href = "/logistics/start_sorting">Start CSV Sorting</a></li>
					<li><a href = "/prints/movePrintImageByBatch">Batch Move to Soft, Hard</a></li>
					
				</ul>
			</div>
			<div class = "col-xs-12">
				<h5 class = "page-header">Inventories</h5>
				<ul>
					<li><a href = "/inventories">Inventories</a></li>
				</ul>
			</div>
		</div>
		<div class = "col-xs-6">
			<div class = "col-xs-12">
				<h5 class = "page-header">Workflow Management</h5>
				<ul>
					<li><a href = "/master_categories">Categories</a></li>
					{{--<li><a href = "/categories">Sub Category 1</a></li>
					<li><a href = "/sub_categories">Sub Category 2</a></li>--}}
					<li><a href = "/production_categories">Production Category</a></li>
					<li><a href = "/sales_categories">Sales Category</a></li>
					<li><a href = "/collections">Collection</a></li>
					<li><a href = "/occasions">Occasion</a></li>
					<li><a href = "/products">Products ( SKUs ) </a></li>
					<li><a href = "/products/sync">Sync products</a></li>
					<li><a href = "/orders/list">Orders</a></li>
					<li><a href = "/departments">Departments</a></li>
					<li><a href = "/stations">Stations</a></li>
					<li><a href = "/templates">Route Templates</a></li>
					<li><a href = "/batch_routes">Routes</a></li>
					<li><a href = "/items">Order items list status</a></li>
					<li><a href = "/items/batch">Preview batch</a></li>
					<li><a href = "/items/grouped">Batch list</a></li>
					<li><a href = "/stations/supervisor">Supervisor</a></li>
					<li><a href = "/rules">Shipping Rules</a></li>
					<li><a href = "/shipping">Shipping list</a></li>
					<li><a href = "shippinglabel_print">Shipping Label Print</a></li>
					<li><a href = "prints/packing_slip/bulk">Bulk Packing Slip Print By Order#</a></li>
					{{-- <li><a href = "/items/waiting_for_another_item">Waiting for another items</a></li> --}}
					<li><a href = "/summary">Stations summary ( 1 min to Load)</a></li>
					<li><a href = "/export_station">Export station log</a></li>
					<li><a href = "/rejection_reasons">Rejection reasons</a></li>
					<li><a href = "/stations/bulk">Bulk batch change</a></li>
					<li><a href = "/stations/single">Single batch change</a></li>
					<li><a href = "/stations/itemstationchange">Move to WAP Station by Item#</a></li>
					<li><a href = "/stations/itemshippingstationchange">Move to Shipping by Order# or Item#</a></li>
					<li><a href = "/items/active_batch_group">Active batch by SKU group</a></li>
					<li><a href = "/products_specifications">Product specification sheet</a></li>
				</ul>
				<hr />
				<ul>
					<li><a href = "/orders/manual"><em><strong>Add new order manually</strong></em></a></li>
					{{--
					<li><a href = "/stations/status">Station status</a></li>
					<li><a href = "/stations/my_station">My station</a></li>--}}
				</ul>
			</div>
		</div>
	</div>
</body>
</html>
