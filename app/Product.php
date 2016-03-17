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
	private $data_set = [ ];

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

	public function store ()
	{
		return $this->belongsTo('App\Store', 'store_id', 'store_id');
	}

	public function vendor ()
	{
		return $this->belongsTo('App\Vendor', 'vendor_id', 'id');
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

	public function sales_category ()
	{
		return $this->belongsTo('App\SalesCategory', 'product_sales_category', 'id');
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

	private function recursion ($categories)
	{
		$this->data_set = array_merge($this->data_set, $categories->lists('id')
																  ->toArray());
		foreach ( $categories as $category ) {
			$child_count = $category->children()
									->count();

			if ( $child_count > 0 ) {
				$this->recursion($category->children()
										  ->get());
			}
		}
	}

	private function trimmer ($haystack, $needle)
	{
		if ( is_array($haystack) ) {
			return array_diff($haystack, [ $needle ]);
		}

		return [ ];
	}

	public function scopeSearchInOption ($query, $search_in, $search_for)
	{
		$available_fields = [
			'id_catalog',
			'product_model',
			'product_name',
			'product_sales_category',
		];
		// search in field means, in which field you want to search
		if ( $search_in && in_array($search_in, $available_fields) ) {
			if ( $search_in == 'id_catalog' ) {
				return $this->scopeSearchIdCatalog($query, $search_for);
			} elseif ( $search_in == 'product_mode' ) {
				return $this->scopeSearchProductModel($query, $search_for);
			} elseif ( $search_in == 'product_name' ) {
				return $this->scopeSearchProductName($query, $search_for);
			} elseif ( $search_in == 'product_sales_category' ) {
				return $this->scopeSearchProductSalesCategory($query, $search_for);
			}
		}

		return;
	}

	public function scopeSearchProductSalesCategory ($query, $sales_category)
	{
		/*if ( !$sales_category ) {
			return;
		}
		$replaced = str_replace(" ", "", $sales_category);
		$values = explode(",", trim($replaced, ","));*/
		if ( !$sales_category || $sales_category == 'all' ) {
			return;
		}

		$sales_categories = SalesCategory::where('sales_category_code', $sales_category)
										 ->lists('id');
										 //->toArray();

		return $query->whereIn('product_sales_category', $sales_categories);
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
		$product_name = trim($product_name);

		return $query->where('product_name', 'LIKE', sprintf("%%%s%%", $product_name));
	}

	public function scopeSearchRoute ($query, $route_ids)
	{

		if ( !$route_ids || !is_array($route_ids) ) {
			return;
		}
		$stripped_values = $this->trimmer($route_ids, 0);
		if ( count($stripped_values) == 0 ) {
			return;
		}

		return $query->whereIn('batch_route_id', $stripped_values);
	}

	public function scopeSearchMasterCategory ($query, $product_master_category_id)
	{
		if ( !$product_master_category_id || $product_master_category_id == 'all' ) {
			return;
		}
		$this->data_set[] = $product_master_category_id;

		$categories = MasterCategory::where('parent', $product_master_category_id)
									->get();

		$this->recursion($categories);

		$category_id_list = $this->data_set;

		#dd($category_id_list);
		return $query->whereIn('product_master_category', $category_id_list);

		#return $query->where('product_master_category', $product_master_category);
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
		if ( !$production_category || !is_array($production_category) ) {
			return;
		}
		$stripped_values = $this->trimmer($production_category, 'all');

		if ( count($stripped_values) == 0 ) {
			return;
		}

		return $query->whereIn('product_production_category', $stripped_values);
		/*if ( !$production_category || $production_category == 'all' ) {
			return;
		}
		return $query->where('product_production_category', intval($production_category));*/
	}

	public function scopeSearchProductCollection ($query, $product_collection_id)
	{
		if ( !$product_collection_id || !is_array($product_collection_id) ) {
			return;
		}
		$stripped_values = $this->trimmer($product_collection_id, 0);

		if ( count($stripped_values) == 0 ) {
			return;
		}
		$product_ids = DB::table('collection_product')
						 ->whereIn('collection_id', $stripped_values)
						 ->lists('product_id');

		return $query->whereIn('id', $product_ids);
	}

	public function scopeSearchProductOccasion ($query, $product_occasion_id)
	{
		if ( !$product_occasion_id || !is_array($product_occasion_id) ) {
			return;
		}
		$stripped_values = $this->trimmer($product_occasion_id, 0);

		if ( count($stripped_values) == 0 ) {
			return;
		}

		$product_ids = DB::table('occasion_product')
						 ->whereIn('occasion_id', $product_occasion_id)
						 ->lists('product_id');

		return $query->whereIn('id', $product_ids);
		#return $query->where('product_occasion', intval($product_occasion_id));*/
	}
}
