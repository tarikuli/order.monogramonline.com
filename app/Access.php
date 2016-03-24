<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
	protected $table = 'accesses';

	public static $pages = [
		'users'                    => 'Users',
		'customers'                => 'Customers',
		'vendors'                  => 'Vendors',
		'logs'                     => 'Logs',
		'sku_import'               => 'SKU Export/Import',
		'sku_conversion_parameter' => 'SKU Conversion parameter',
		'inventories'              => 'Inventories',
		'master_categories'        => 'Categories',
		'production_categories'    => 'Production categories',
		'sales_categories'         => 'Sales categories',
		'collections'              => 'Collections',
		'occasions'                => 'Occasions',
		'products'                 => 'Products',
		'sync_products'            => 'Sync Products',
		'orders'                   => 'Orders',
		'departments'              => 'Departments',
		'stations'                 => 'Stations',
		'batch_routes'             => 'Routes',
		'route_templates'          => 'Route templates',
		'order_item_status'        => 'Order item list status',
		'preview_batch'            => 'Preview batch',
		'batch_list'               => 'Batch list',
		'supervisor'               => 'Supervisor',
		'shipping_rules'           => 'Shipping rules',
		'summary'                  => 'Station summary',
		'rejection_reasons'        => 'Rejection reasons',
		'purchases'                => 'Purchases',
		'add_purchase'             => 'Add purchase',
	];
}
