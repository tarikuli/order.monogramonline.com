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
		$parameter = Parameter::where('store_id', $store_id)
							  ->where(DB::raw('BINARY `parameter_value`'), $search_in)
							  ->first();
		// parameter doesn't exist with that value,
		// return null
		if ( !$parameter ) {
			return;
		}

		$search_for = str_replace([ "%" ], "", $search_for);
		// create a json like format that will be searchable
		// i,e; "%\"code\":\"MN%%"
		$searchable_data = sprintf('%%\\"%s\\":\\"%%%s%%%%', $search_in, $search_for);

		#dd($searchable_data);
		return $query->where(DB::raw('BINARY `parameter_option`'), "LIKE", $searchable_data);
	}


	public function product ()
	{
		return $this->belongsTo("App\\Product", "parent_sku", 'product_model');
	}

	public function scopeSearchUnassigned ($query, $unassigned)
	{
		if ( intval($unassigned) < 1 ) {
			return;
		}

		return $query->where('batch_route_id', Helper::getDefaultRouteId())
					 ->orWhere('batch_route_id', 206);
	}
}
