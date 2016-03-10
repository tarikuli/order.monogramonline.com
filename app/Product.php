<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
	protected $fillable = [ '*' ];
	protected $hidden = [
		'updated_at',
		'created_at',
	];

	private function tableColumns ()
	{
		$columns = $this->getConnection()
						->getSchemaBuilder()
						->getColumnListing($this->getTable());
		$remove_columns = [
			'updated_at',
			'created_at',
			'id',
			'product_category',
			'product_sub_category',
		];

		return array_diff($columns, $remove_columns);
	}

	public static function getTableColumns ()
	{
		return (new static())->tableColumns();
	}

	public function batch_route ()
	{
		return $this->hasOne('App\BatchRoute', 'id', 'batch_route_id');
	}

	public function master_category ()
	{
		return $this->belongsTo('App\MasterCategory', 'product_master_category', 'id');
	}

	public function category ()
	{
		return $this->belongsTo('App\Category', 'product_category', 'id');
	}

	public function sub_category ()
	{
		return $this->belongsTo('App\SubCategory', 'product_sub_category', 'id');
	}

	public function production_category ()
	{
		return $this->belongsTo('App\ProductionCategory', 'product_production_category', 'id');
	}

	public function product_occasion_details ()
	{
		return $this->belongsTo('App\Occasion', 'product_occasion', 'id')
					->where('is_deleted', 0);
	}

	public function occasions ()
	{
		return $this->belongsToMany('App\Occasion')
					->withTimestamps();
	}

	public function collections ()
	{
		return $this->belongsToMany('App\Collection')
					->withTimestamps();
	}

	public function product_collection_details ()
	{
		return $this->belongsTo('App\Collection', 'product_collection', 'id')
					->where('is_deleted', 0);
	}

	public function groupedItems ()
	{
		/*return $this->hasMany('App\Item', 'item_id', 'id_catalog')
					->whereNull('batch_number')
					->where('is_deleted', 0)
					->select([
						'id',
						'item_id',
						'order_id',
					]);*/
		return $this->hasMany('App\Item', 'item_code', 'product_model')
					->whereNull('batch_number')
					->where('is_deleted', 0)
					->select([
						'id',
						'item_id',
						'item_code',
						'order_id',
					]);
	}

	public function scopeSearchIdCatalog ($query, $id_catalog)
	{
		if ( !$id_catalog ) {
			return;
		}
		$replaced = str_replace(" ", "", $id_catalog);
		$values = explode(",", trim($replaced, ","));

		return $query->where('id_catalog', 'REGEXP', implode("|", $values));
	}

	public function scopeSearchProductModel ($query, $product_model)
	{
		if ( !$product_model ) {
			return;
		}
		$replaced = str_replace(" ", "", $product_model);
		$values = explode(",", trim($replaced, ","));

		return $query->where('product_model', 'REGEXP', implode("|", $values));
	}

	public function scopeSearchProductName ($query, $product_name)
	{
		if ( !$product_name ) {
			return;
		}

		return $query->where('product_name', 'LIKE', sprintf("%%%s%%", $product_name));
	}

	public function scopeSearchRoute ($query, $route_id)
	{
		if ( !$route_id ) {
			return;
		}

		return $query->where('batch_route_id', $route_id);
	}

	public function scopeSearchMasterCategory ($query, $product_master_category)
	{
		if ( !$product_master_category || $product_master_category == 'all' ) {
			return;
		}

		return $query->where('product_master_category', $product_master_category);
	}

	public function scopeSearchCategory ($query, $product_category)
	{
		if ( !$product_category || $product_category == 'all' ) {
			return;
		}

		return $query->where('product_category', $product_category);
	}

	public function scopeSearchSubCategory ($query, $sub_category)
	{
		if ( !$sub_category || $sub_category == 'all' ) {
			return;
		}

		return $query->where('product_sub_category', $sub_category);
	}

	public function scopeSearchProductionCategory ($query, $production_category)
	{
		if ( !$production_category || $production_category == 'all' ) {
			return;
		}

		return $query->where('product_production_category', intval($production_category));
	}

	public function scopeSearchProductCollection ($query, $product_collection_id)
	{
		if ( !$product_collection_id || $product_collection_id == 0 ) {
			return;
		}
		$product_ids = DB::table('collection_product')
						 ->where('collection_id', $product_collection_id)
						 ->lists('product_id');

		return $query->whereIn('id', $product_ids);

		#return $query->where('product_collection', intval($product_collection_id));
	}

	public function scopeSearchProductOccasion ($query, $product_occasion_id)
	{
		if ( !$product_occasion_id || $product_occasion_id == 0 ) {
			return;
		}
		$product_ids = DB::table('occasion_product')
						 ->where('occasion_id', $product_occasion_id)
						 ->lists('product_id');

		return $query->whereIn('id', $product_ids);

		#return $query->where('product_occasion', intval($product_occasion_id));*/
	}
}
