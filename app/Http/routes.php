<?php

get('test/batch', function () {
	$today = date('md', strtotime('now'));

	return $today;
});

// auth middleware enabled controller
Route::group([ 'middleware' => [ 'auth' ] ], function () {
	get('/', 'HomeController@index');
	get('logout', 'AuthenticationController@getLogout');

	post('imports/inventory', 'ImportController@importInventory');

	resource('customers', 'CustomerController');

	resource('rejection_reasons', 'RejectionReasonController');

	resource('users', 'UserController');
	resource('vendors', 'VendorController');
	resource('purchases', 'PurchaseController');

	get('prints/packing/{id}', 'PrintController@packing');
	get('prints/invoice/{id}', 'PrintController@invoice');
	get('prints/purchase/{purchase_id}', 'PrintController@purchase');
	get('prints/batches', 'PrintController@batches');

	resource('inventories', 'InventoryController');
	get('exports/inventory', 'ExportController@inventory');

	get('exports/batch/{id}', 'ItemController@export_batch');

	get('products/unassigned', 'ProductController@unassigned');
	#get('products/import', 'ProductController@getAddProductsByCSV');
	post('products/import', 'ProductController@import');
	get('products/export', 'ProductController@export');
	resource('products', 'ProductController');

	get('orders/details/{order_id}', 'OrderController@details');
	get('orders/add', 'OrderController@getAddOrder');
	post('orders/add', 'OrderController@postAddOrder');
	get('orders/list', 'OrderController@getList');
	get('orders/search', 'OrderController@search');

	get('batches/{batch_number}/{station_name}', 'ItemController@getBatchItems');
	post('batches/{batch_number}/{station_name}', 'ItemController@postBatchItems');

	put('batches/{batch_number}', 'ItemController@updateBatchItems');
	get('items/batch', 'ItemController@getBatch');
	get('batch_details/{batch_number}', 'ItemController@batch_details');
	post('items/batch', 'ItemController@postBatch');
	get('items/grouped', 'ItemController@getGroupedBatch');
	get('items/release/{item_id}', 'ItemController@release');
	resource('items', 'ItemController');

	resource('orders', 'OrderController');

	get('logistics/sku_converter', 'LogisticsController@sku_converter');
	post('logistics/sku_converter', 'LogisticsController@post_sku_converter');

	delete('/logistics/delete_sku/{unique_row_value}', 'LogisticsController@delete_sku');
	get('/logistics/edit_sku_converter', 'LogisticsController@edit_sku_converter');
	put('/logistics/edit_sku_converter', 'LogisticsController@update_sku_converter');

	put('logistics/{store_id}/update', 'LogisticsController@sku_converter_update');
	get('logistics/sku_import', 'LogisticsController@get_sku_import');
	post('logistics/sku_import', 'LogisticsController@post_sku_import');
	get('logistics/sku_show', 'LogisticsController@get_sku_show');


	post('stations/change', 'StationController@change');
	get('stations/status', 'StationController@status');
	get('stations/supervisor', 'StationController@supervisor');
	post('stations/on_change_apply', 'StationController@on_change_apply');
	get('stations/my_station', 'StationController@my_station');
	get('summary', 'StationController@summary');

	resource('departments', 'DepartmentController');

	resource('production_categories', 'ProductionCategoryController');

	resource('stations', 'StationController');

	resource('categories', 'CategoryController');

	resource('sub_categories', 'SubCategoryController');

	resource('batch_routes', 'BatchRouteController');

	resource('templates', 'TemplateController', [
		'except' => [ 'create' ],
	]);

	resource('shipping', 'ShippingController');

	get('rules/parameter', 'RuleController@parameter_option');
	get('rules/actions', 'RuleController@rule_action');

	put('rules/bulk_update/{id}', 'RuleController@bulk_update');

	get('master_categories/get_next/{parent_category_id}', 'MasterCategoryController@getNext');
	resource('master_categories', 'MasterCategoryController');
	resource('rules', 'RuleController', [
		'except' => [ 'create' ],
	]);
});

// guest middleware enabled controller
Route::group([ 'middleware' => [ 'guest' ] ], function () {
	get('login', 'AuthenticationController@getLogin');
	post('login', 'AuthenticationController@postLogin');
	post('hook', 'OrderController@hook');
});

// Redefinition of routes
get('home', function () {
	return redirect(url('/'));
});
Route::group([ 'prefix' => 'auth' ], function () {
	get('login', 'AuthenticationController@getLogin');
	get('logout', 'AuthenticationController@getLogout');
});

Event::listen('illuminate.query', function ($q) {
	#Log::info($q);
});