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
		<div class = "col-xs-6">
			<div class = "col-xs-12">
				<h5 class = "page-header">Users Management</h5>
				<ul>
					<li><a href = "/users">Users</a></li>
					<li><a href = "/users/create">Create user</a></li>
					<li><a href = "/customers">Customers</a></li>
					<li><a href = "/customers/create">Create Customer</a></li>
				</ul>
			</div>
			<div class = "col-xs-12">
				<h5 class = "page-header">Logistics</h5>
				<ul>
					<li><a href = "/logistics/sku_converter">Set store options to SKU conversion parameters</a></li>
					<li><a href = "/logistics/sku_import">Export/Import options coded SKUs CSV file</a></li>
					<li><a href = "/templates">Templates</a></li>
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
					<li><a href = "/products">Products ( SKUs ) </a></li>
					<li><a href = "/orders/list">Orders</a></li>
					<li><a href = "/departments">Departments</a></li>
					<li><a href = "/stations">Stations</a></li>
					<li><a href = "/batch_routes">Routes</a></li>
					<li><a href = "/items">Order items list status</a></li>
					<li><a href = "/items/batch">Preview batch</a></li>
					<li><a href = "/items/grouped">Batch list</a></li>
					<li><a href = "/stations/supervisor">Supervisor</a></li>
					<li><a href = "/rules">Shipping Rules</a></li>
					<li><a href = "/shipping">Shipping list</a></li>
					<li><a href = "/summary">Stations summary</a></li>
					<li><a href = "/rejection_reasons">Rejection reasons</a></li>
				</ul>
				<hr />
				<ul>
					<li><a href = "/products/create">Create Product</a></li>
					<li><a href = "/orders/add">Add new order</a></li>
					<li><a href = "/stations/status">Station status</a></li>
					<li><a href = "/stations/my_station">My station</a></li>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>
