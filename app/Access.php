<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
	protected $table = 'accesses';

	public static $pages = [
		'batch_list'               => 'Batch list',
		'batch_routes'             => 'Routes',
		'collections'              => 'Collections',
		'customers'                => 'Customers',
		'departments'              => 'Departments',
		'exports'                  => 'Exports',
		'imports'                  => 'Import',
		'inventories'              => 'Inventories',
		'items'                    => 'Items',
		'logs'                     => 'Logs',
		'master_categories'        => 'Categories',
		'occasions'                => 'Occasions',
		'order_item_status'        => 'Order item list status',
		'orders'                   => 'Orders',
		'preview_batch'            => 'Preview batch',
		'products'                 => 'Products',
		'production_categories'    => 'Production categories',
		'rules'                    => 'Shipping rules',
		'sales_categories'         => 'Sales categories',
		'sku_conversion_parameter' => 'SKU Conversion parameter',
		'stations'                 => 'Stations',
		'summary'                  => 'Station summary',
		'supervisor'               => 'Supervisor',
		'sync_products'            => 'Sync Products',
		'templates'                => 'Route templates',
		'users'                    => 'Users',
		'vendors'                  => 'Vendors',
		#'sku_import'               => 'SKU Export/Import',
		#'route_templates'          => 'Route templates',
		'rejection_reasons'        => 'Rejection reasons',
		'purchases'                => 'Purchases',
		'add_purchase'             => 'Add purchase',
		'prints'                   => 'Prints',
		'batches'                  => 'Batches',
		'batch_details'            => 'Batch details',
		'logistics'                => 'Logistics',
		'shipping'                 => 'Shipping',
		'stations/bulk'            => 'Bulk station update',
		'export_station'           => 'Export station log',
		'products_specifications'  => 'Product specifications',
		'change_station_by_sku'    => 'Change station by SKU group',
		'orders/manual'            => 'Manual Order',
	];
}
