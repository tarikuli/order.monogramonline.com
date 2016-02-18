<?php

get('test/batch', function () {
	#return \DNS1D::getBarcodeHTML("4445645656", "C39");
	#return date('Y-m-d h:i:s', strtotime("now"));
	$start = strtotime("now");
	$index = 1;
	$x = 0;
	/*foreach ( range(1, \App\Product::count()) as $id ) {
		++$x;
		if ( $index > 31 ) {
			$index = 1;
		}
		$product = \App\Product::find($id);
		$product->batch_route_id = $index;
		$product->save();
		++$index;
	}*/
	foreach ( \App\Product::all() as $product ) {
		++$x;
		if ( $index > 31 ) {
			$index = 1;
		}
		#$product = \App\Product::find($id);
		$product->batch_route_id = $index;
		$product->save();
		++$index;
	}
	$end = strtotime("now");

	return sprintf("%d seconds passed to add routes to %d products", ( $end - $start ), $x);
});

get('set_route', function () {
	$stations = [
		"R-GGR",
		"R-GLP",
		"R-GRD",
		"R-Red",
		"R-BM1",
		"R-BM2",
		"R-BM3",
		"R-BM4",
		"R-BM5",
		"R-PEDD",
		"R-PNAF",
		"R-TBF",
		"R-QCD",
		"R-SHP",
		"R-REC",
		"R-BO",
	];

	return \App\Station::whereIn('station_name', $stations)
					   ->orderBy(\DB::raw(sprintf("field(station_name, '%s')", implode("','", $stations))))
					   ->lists('id');
});

get('set/{id}', function ($id) {
	\Session::put('station_id', $id);

	return redirect(url('stations/my_station'));
});

// auth middleware enabled controller
Route::group([ 'middleware' => [ 'auth' ] ], function () {
	get('/', 'HomeController@index');
	get('logout', 'AuthenticationController@getLogout');

	resource('customers', 'CustomerController');

	resource('users', 'UserController');

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
	post('items/batch', 'ItemController@postBatch');
	get('items/grouped', 'ItemController@getGroupedBatch');
	resource('items', 'ItemController');

	resource('orders', 'OrderController');

	get('logistics/sku_converter', 'LogisticsController@sku_converter');
	post('logistics/sku_converter', 'LogisticsController@post_sku_converter');

	put('logistics/{store_id}/update', 'LogisticsController@sku_converter_update');
	get('logistics/sku_import', 'LogisticsController@get_sku_import');
	post('logistics/sku_import', 'LogisticsController@post_sku_import');


	post('stations/change', 'StationController@change');
	get('stations/status', 'StationController@status');
	get('stations/supervisor', 'StationController@supervisor');
	post('stations/assign_to_station', 'StationController@assign_to_station');
	get('stations/my_station', 'StationController@my_station');

	resource('departments', 'DepartmentController');

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
	Log::info($q);
});