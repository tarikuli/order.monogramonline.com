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
		return $query->where('stock_no_unique', "LIKE", sprintf("%%%s%%", stock_no_unique));
	}
	
	public function scopeSearchStockNameDiscription ($query, $stock_name_discription)
	{
		if ( empty( $stock_name_discription ) ) {
			return;
		}
	
		return $query->where('stock_name_discription', "LIKE", sprintf("%%%s%%", $stock_name_discription));
	}

// 	public function getSkuStatusAttribute ($index)
// 	{
// 		return array_key_exists($index, static::$skuStatuses) ? ucfirst(static::$skuStatuses[$index]) : null;
// 	}

// 	public function setSkuStatusAttribute ($text)
// 	{
// 		$flipped = array_flip(static::$skuStatuses);
// 		$lowercase_text = strtolower($text);

// 		$this->attributes['sku_status'] = array_key_exists($lowercase_text, $flipped) ? $flipped[$lowercase_text] : 0;
// 	}

// 	public function getRtStatusAttribute ($index)
// 	{
// 		return array_key_exists($index, static::$realtime_status) ? ucfirst(static::$realtime_status[$index]) : null;
// 	}

// 	public function setRealTimeStatusAttribute ($text)
// 	{
// 		$flipped = array_flip(static::$realtime_status);
// 		$lowercase_text = strtolower($text);

// 		$this->attributes['rt_status'] = array_key_exists($lowercase_text, $flipped) ? $flipped[$lowercase_text] : 0;
// 	}

// 	public function getInvAmazonAttribute ($index)
// 	{
// 		return array_key_exists($index, static::$inventory_amazon) ? ucfirst(static::$inventory_amazon[$index]) : null;
// 	}

// 	public function setInvAmazonAttribute ($text)
// 	{
// 		$flipped = array_flip(static::$inventory_amazon);
// 		$lowercase_text = strtolower($text);

// 		$this->attributes['inv_amazon'] = array_key_exists($lowercase_text, $flipped) ? $flipped[$lowercase_text] : 0;
// 	}

// 	public function getFeedHouzzAttribute ($index)
// 	{
// 		return array_key_exists($index, static::$feed_houzz) ? ucfirst(static::$feed_houzz[$index]) : null;
// 	}

// 	public function setFeedHouzzAttribute ($text)
// 	{
// 		$flipped = array_flip(static::$feed_houzz);
// 		$lowercase_text = strtolower($text);

// 		$this->attributes['feed_houzz'] = array_key_exists($lowercase_text, $flipped) ? $flipped[$lowercase_text] : 0;
// 	}

// 	public function getFeedJetAttribute ($index)
// 	{
// 		return array_key_exists($index, static::$feed_jet) ? ucfirst(static::$feed_jet[$index]) : null;
// 	}

// 	public function setFeedJetAttribute ($text)
// 	{
// 		$flipped = array_flip(static::$feed_jet);
// 		$lowercase_text = strtolower($text);

// 		$this->attributes['feed_jet'] = array_key_exists($lowercase_text, $flipped) ? $flipped[$lowercase_text] : 0;
// 	}
}
