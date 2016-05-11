<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Monogram\Helper;

class Option extends Model
{
	protected $table = 'parameter_options';

	// search for is the string text
	// search in is the field name to search, dropdown
	public function scopeSearchInParameterOption ($query, $store_id, $search_for = null, $search_in = "")
	{
		if ( is_null($search_for) || empty( $search_in ) ) {
			return;
		}

		// check if the parameter value as search really exits
		/*$parameter = Parameter::where('store_id', $store_id)
							  ->where(DB::raw('BINARY `parameter_value`'), $search_in)
							  ->first();*/
		// parameter doesn't exist with that value,
		// return null
		/*if ( !$parameter ) {
			return;
		}*/
		$columns = [
			'id_catalog',
			'parent_sku',
			'child_sku',
			'graphic_sku',
		];
		if ( in_array($search_in, $columns) ) {
			if ( $search_in == 'id_catalog' ) {
				return $this->scopeSearchInIdCatalog($query, $search_for);
			} elseif ( $search_in == 'parent_sku' ) {
				return $this->scopeSearchInParentSku($query, $search_for);
			} elseif ( $search_in == 'child_sku' ) {
				return $this->scopeSearchInChildSku($query, $search_for);
			} elseif ( $search_in == 'graphic_sku' ) {
				return $this->scopeSearchInGraphicSku($query, $search_for);
			}
		}
		$search_for = str_replace([ "%" ], "", $search_for);
		// create a json like format that will be searchable
		// i,e; "%\"code\":\"MN%%"
		$searchable_data = sprintf('%%"%s":"%%%s%%%%', $search_in, $search_for);
		// need to fix the function.
		// searching gold in metal type shows extra values

		#dd($searchable_data);
		return $query->where(DB::raw('BINARY `parameter_option`'), "LIKE", $searchable_data);
	}

	public function scopeSearchInIdCatalog ($query, $text)
	{
		$text = trim($text);
		if ( empty( $text ) ) {
			return;
		}

		return $query->where('id_catalog', 'LIKE', sprintf("%%%s%%", $text));
	}

	public function scopeSearchInParentSku ($query, $text)
	{
		$text = trim($text);
		if ( empty( $text ) ) {
			return;
		}

		return $query->where('parent_sku', 'LIKE', sprintf("%%%s%%", $text));
	}

	public function scopeSearchInChildSku ($query, $text)
	{
		$text = trim($text);
		if ( empty( $text ) ) {
			return;
		}

		return $query->where('child_sku', 'LIKE', sprintf("%%%s%%", $text));
	}

	public function scopeSearchInGraphicSku ($query, $text)
	{
		$text = trim($text);
		if ( empty( $text ) ) {
			return;
		}

		return $query->where('graphic_sku', 'LIKE', sprintf("%%%s%%", $text));
	}


	public function product ()
	{
		return $this->belongsTo("App\\Product", "parent_sku", 'product_model');
	}

	public function route ()
	{
		return $this->belongsTo('App\BatchRoute', 'batch_route_id', 'id');
	}

	public function scopeSearchUnassigned ($query, $unassigned)
	{
		if ( intval($unassigned) < 1 ) {
			return;
		}

		return $query->where('batch_route_id', Helper::getDefaultRouteId());
	}
}
