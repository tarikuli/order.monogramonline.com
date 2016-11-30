<?php
get('test', 'HomeController@test');
// get('phantom', function (\Illuminate\Http\Request $request) {
// 	$id = $request->get('id', 'pemoanwisicr');
// 	$url = sprintf("http://monogramonline.monogramonline.com/crawl.php?id=%s", $id);
// 	$phantom = new \Monogram\Phantom($url);
// 	$response = $phantom->request()
// 						->getResponse();

// 	$reader = new \Monogram\DOMReader($response);

// 	return json_decode($reader->readCrawledData());
// });

get('combination', 'HomeController@combination');
get('update_items', 'HomeController@bulk_item_update');
get('update_single_item', 'HomeController@update_single_item');

/*To crawl the data from monogram page*/
get('crawl', 'LogisticsController@crawl');
get('get_file_contents', 'LogisticsController@get_file_contents');

// auth middleware enabled controller
Route::group([ 'middleware' => [ 'auth' ] ], function () {
	Route::group([ 'middleware' => 'user_has_access' ], function () {
		get('/', 'HomeController@index');
		get('logout', 'AuthenticationController@getLogout');
		get('logistics/reset_sorting', 'LogisticsController@resetSort');
		get('logistics/start_sorting', 'LogisticsController@startSort');
		

		post('imports/inventory', 'ImportController@importInventory');
		post('imports/batch_route', 'ImportController@importBatchRoute');

		resource('customers', 'CustomerController');

		resource('rejection_reasons', 'RejectionReasonController');

		resource('users', 'UserController');
		resource('vendors', 'VendorController');
		resource('purchases', 'PurchaseController');
		resource('purchases/getVendorById', 'PurchaseController@getVendorById');
		resource('purchases/purchased_inv_products', 'PurchaseController@getPurchasedInvProducts');

		get('autocomplete',array('as'=>'autocomplete','uses'=>'PurchaseController@index'));
		resource('purchases/searchajax', 'PurchaseController@autoComplete');


		resource('collections', 'CollectionController');
		resource('occasions', 'OccasionController');

		get('prints/packing/{id}', 'PrintController@packing');
		get('prints/invoice/{id}', 'PrintController@invoice');
		get('prints/purchase/{purchase_id}', 'PrintController@purchase');
		get('prints/batches', 'PrintController@batches');
		get('prints/batch_packing', 'PrintController@batch_packing_slip');
		get('prints/batch_packing_small', 'PrintController@batch_packing_slip_small');
		get('prints/email_packing', 'PrintController@sendShippingConfirm');
		
		get('prints/shippinglable', 'PrintController@printShippingLableByOrderId');
		post('prints/shippinglabel_reprint', 'PrintController@reprintShippinglabel');

// 		get('prints/sendbyscript', 'PrintController@sendShippingConfirmByScript');

		get('prints/email_order_status', 'PrintController@sendOrderConfirm');

		get('prints/sheets', 'PrintController@print_spec_sheet');

		resource('logs', 'StationLogController');

		resource('inventories', 'InventoryController');
		post('inventories/getuniquestock','InventoryController@getStockNoUnique');
		post('inventoriesupdate/{inventorie_id}', 'InventoryController@updateInventorie');
		get('inventoriessyn', 'InventoryController@synStock');
		
		resource('purchasedinvproducts', 'PurchasedInvProductsController');

		get('exports/inventory', 'ExportController@inventory');
		get('exports/batch_routes', 'ExportController@batch_routes');

		get('exports/batch/{id}/{station}', 'ItemController@export_batch');
		get('exports/batchbulk', 'ItemController@export_bulk');

		post('orders/mailer', 'MailController@mailer');
		post('orders/send_mail', 'MailController@send_mail');

		post('products/change_mixing_status', 'ProductController@change_mixing_status');
		get('products/unassigned', 'ProductController@unassigned');
		get('products/sync', 'ProductController@getSync');
		post('products/sync', 'ProductController@postSync');
		post('products/post_to_yahoo', 'ProductController@post_to_yahoo');
		#get('products/import', 'ProductController@getAddProductsByCSV');
		post('products/import', 'ProductController@import');
		get('products/export', 'ProductController@export');
		resource('products', 'ProductController');

		get('orders/details/{order_id}', 'OrderController@details');
		get('orders/add', 'OrderController@getAddOrder');
		post('orders/add', 'OrderController@postAddOrder');
		get('orders/manual', 'OrderController@getManual');
		post('orders/manual', 'OrderController@postManual');
		get('orders/addmanual/{order_id}', 'OrderController@manualReOrder');

		get('orders/ajax', 'OrderController@ajax');
		get('orders/product_info', 'OrderController@product_info');
		get('orders/list', 'OrderController@getList');
		get('orders/search', 'OrderController@search');

		get('batches/{batch_number}/{station_name}', 'ItemController@getBatchItems');
		post('batches/{batch_number}/{station_name}', 'ItemController@postBatchItems');
		post('change_station_by_sku/{sku}', 'ItemController@changeStationBySKU');
		post('items/sku_station_done_reject', 'ItemController@rejectDoneFromSKUList');
		post('items/partial_shipping', 'ItemController@partial_shipping');
		get('items/waiting_for_another_item', 'ItemController@waiting_for_another_item');
		get('items/active_batch_group', 'ItemController@get_active_batch_by_sku');
		get('items/delete_item/{order_id}/{item_id}', 'ItemController@delete_item_id');


		get('items/active_batch/sku/{sku}', 'ItemController@get_sku_on_stations');
		put('batches/{batch_number}', 'ItemController@updateBatchItems');
		get('items/batch', 'ItemController@getBatch');
		get('batch_details/{batch_number}', 'ItemController@batch_details');
		post('items/batch', 'ItemController@postBatch');

		// Add changeBatchStation
		put('items/{batch_number}', 'ItemController@changeBatchStation');

		get('items/doctor', 'ItemController@doctorCheckup');
		get('items/grouped', 'ItemController@getGroupedBatch');
		get('items/release/{item_id}', 'ItemController@release');
		get('items/release_batch', 'ItemController@releaseBatches');
		get('items/bulk_item_change', 'ItemController@getBulkItemChange');
		post('items/bulk_item_change', 'ItemController@postBulkItemChange');

		resource('items', 'ItemController');

		get('products_specifications/step/{id?}', 'ProductSpecificationController@getSteps');
		post('products_specifications/step/{id}', 'ProductSpecificationController@postSteps');
		get('copy_products_specifications/{categoty_id}/{product_sku}', 'ProductSpecificationController@copyProduct');
		resource('products_specifications', 'ProductSpecificationController');

		resource('orders', 'OrderController');

		get('logistics/sku_converter', 'LogisticsController@sku_converter');
		post('logistics/sku_converter', 'LogisticsController@post_sku_converter');
		post('logistics/update_parameter_option/{unique_row}', 'LogisticsController@update_parameter_option');

		delete('logistics/delete_sku/{unique_row_value}', 'LogisticsController@delete_sku');
		get('logistics/edit_sku_converter', 'LogisticsController@edit_sku_converter');
		put('logistics/edit_sku_converter', 'LogisticsController@update_sku_converter');
		get('logistics/add_child_sku', 'LogisticsController@get_add_child_sku');
		post('logistics/add_child_sku', 'LogisticsController@post_add_child_sku');

		resource('email_templates', 'EmailTemplateController');

		put('logistics/{store_id}/update', 'LogisticsController@sku_converter_update');
		get('logistics/sku_import', 'LogisticsController@get_sku_import');
		post('logistics/sku_import', 'LogisticsController@post_sku_import');
		get('logistics/sku_show', 'LogisticsController@get_sku_show');

		get('logistics/create_child_sku', 'LogisticsController@create_child_sku');
		post('logistics/create_child_sku', 'LogisticsController@post_create_child_sku');
		post('logistics/post_preview', 'LogisticsController@post_preview');

		get('stations/bulk', 'StationController@getBulkChange');
		post('stations/bulk', 'StationController@postBulkChange');
		
		get('stations/single', 'StationController@getSingleChange');
		get('stations/moveawaysingle', 'StationController@moveAwaySingleChange');
		post('stations/single', 'StationController@postSingleChange');

		get('stations/itemstationchange', 'StationController@getItemStationChange');
		post('stations/itemstationchange', 'StationController@postItemStationChange');
		
		get('stations/itemshippingstationchange', 'StationController@itemshippingstationchange');
		#post('stations/itemshippingstationchange', 'StationController@change');
		post('stations/itemshippingstationchange', 'StationController@postItemShippingStationchange');
		
		get('stations/back_to_previous_stat', 'StationController@getBackPrevious');
		post('stations/back_to_previous_stat', 'StationController@postBackPrevious');
		
		get('export_station', 'StationController@getExportStationLog');
		post('export_station', 'StationController@postExportStationLog');
		post('stations/change', 'StationController@change');
		get('stations/status', 'StationController@status');
		get('stations/supervisor', 'StationController@supervisor');
		post('stations/on_change_apply', 'StationController@on_change_apply');
		get('stations/my_station', 'StationController@my_station');
		get('summary', 'StationController@summary');
		get('summary/export', 'ItemController@exportItemTable');

		resource('departments', 'DepartmentController');

		resource('production_categories', 'ProductionCategoryController');
		resource('sales_categories', 'SalesCategoryController');

		resource('stations', 'StationController');

		resource('categories', 'CategoryController');

		resource('sub_categories', 'SubCategoryController');

		resource('batch_routes', 'BatchRouteController');

		resource('templates', 'TemplateController', [
			'except' => [ 'create' ],
		]);


		get('shipping_addressvalidation', 'ShippingController@addressValidation');
		get('shipping_label_print', 'ShippingController@getShippingLable');
		get('remove_shipping', 'ShippingController@removeTrackingNumber');
		
		
		put('shipping_update', 'ShippingController@updateTrackingNumber');
		resource('shipping', 'ShippingController');
		
		
		get('shipping_lbl_print', 'ShippingController@getShippingAddressByOrderId');
		post('shipping_lbl_print', 'ShippingController@postShippingAddressByOrderId');
				
		get('shipping_address_update', 'ShippingController@shippingAddressUpdate');
		
		
		get('shippinglabel_print', 'ShippingController@getShippingLableByOrderId');
		post('shippinglabel_print', 'ShippingController@postShippingLableByOrderId');

		get('rules/parameter', 'RuleController@parameter_option');
		get('rules/actions', 'RuleController@rule_action');

		put('rules/bulk_update/{id}', 'RuleController@bulk_update');

		get('master_categories/get_next/{parent_category_id}', 'MasterCategoryController@getNext');
		resource('master_categories', 'MasterCategoryController');
		resource('rules', 'RuleController', [
			'except' => [ 'create' ],
		]);
	});
});

// guest middleware enabled controller
Route::group([ 'middleware' => [ 'guest' ] ], function () {
	get('login', 'AuthenticationController@getLogin');
	post('login', 'AuthenticationController@postLogin');
	post('hook', 'OrderController@hook');
	get('trk_order_status', 'ItemController@getOrderStatus');
	get('prints/sendbyscript', 'PrintController@sendShippingConfirmByScript');
});

// // Redefinition of routes
// get('home', function () {
// 	return redirect(url('/'));
// });

Route::group([ 'prefix' => 'auth' ], function () {
	get('login', 'AuthenticationController@getLogin');
	get('logout', 'AuthenticationController@getLogout');
});

Event::listen('illuminate.query', function ($q) {
	#Log::info($q); // join
});

//  `items`.`item_status` = statuses.id ( `statuses`.`status_name` ) = 6
// \Monogram\Helper::getTrackingUrl($ship-->tracking_number) 
// ->last_name tax_and_duties_payer
// /media/Ji-share/5p_batch_csv_export  chunk
// UPDATE `shipping` SET `shipping_unique_id`= 'send' WHERE `shipping_unique_id` IS NULL AND `shipping_unique_id` IS NULL;