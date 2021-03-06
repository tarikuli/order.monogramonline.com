<?php

namespace App;

use App\Http\Controllers\InventoryController;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
	protected $table = "inventories";

// 	public function inventory_details ()
// 	{
// 		return $this->hasMany('App\PurchasedInvProducts', 'stock_no_unique', 'stock_no')
// 					->where('is_deleted', 0);
// 	}

// 	private static $skuStatuses = [
// 		0 => 'inactive',
// 		1 => 'active',
// 		2 => 'discontinued',
// 	];

// 	private static $realtime_status = [
// 		0 => 'inactive',
// 		1 => 'active',
// 	];

// 	private static $inventory_amazon = [
// 		0 => 'no',
// 		1 => 'yes',
// 	];

// 	private static $feed_houzz = [
// 		0 => 'no',
// 		1 => 'yes',
// 	];

// 	private static $feed_jet = [
// 		0 => 'no',
// 		1 => 'yes',
// 	];

	private function tableColumns ()
	{
		$columns = $this->getConnection()
						->getSchemaBuilder()
						->getColumnListing($this->getTable());
		$remove_columns = [
			'id',
			'updated_at',
			'created_at',
			'is_deleted',
		];

		return array_diff($columns, $remove_columns);
	}

	public static function getTableColumns ()
	{
		return (new static())->tableColumns();
	}

	public function scopeSearchCriteria ($query, $search_for, $search_in)
	{
		$search_for = trim($search_for);
		if ( in_array($search_in, array_keys(InventoryController::$search_in)) ) {
			/*
			 * camel case method converts the key to camel case
			 * uc first converts the word to upper case first to match the method name
			 */
			$search_function_to_respond = sprintf("scopeSearch%s", ucfirst(camel_case($search_in)));

// dd($search_function_to_respond, $search_in);
	
			return $this->$search_function_to_respond($query, $search_for);
		}
	
		return;
	}
	
	public function scopeSearchStockNoUnique ($query, $stock_no_unique)
	{
		if ( empty( $stock_no_unique ) ) {
			return;
		}
		return $query->where('stock_no_unique', "LIKE", sprintf("%%%s%%", $stock_no_unique));
	}
	
	public function scopeSearchStockNameDiscription ($query, $stock_name_discription)
	{
		if ( empty( $stock_name_discription ) ) {
			return;
		}
	
		return $query->where('stock_name_discription', "LIKE", sprintf("%%%s%%", $stock_name_discription));
	}

}
