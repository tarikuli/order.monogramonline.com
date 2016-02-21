<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Monogram\Helper;

class Item extends Model
{
	public function order ()
	{
		return $this->belongsTo('App\Order', 'order_id', 'order_id')
					->where('is_deleted', 0)
					->select([
						'id',
						'order_id',
						'item_count',
						'order_date',
						'short_order',
						'store_id',
						'order_status',
					]);
	}

	public function lowest_order_date ()
	{
		return $this->belongsTo('App\Order', 'order_id', 'order_id')
					->where('is_deleted', 0)
					->orderBy('order_numeric_time', 'asc')
					->select([
						'id',
						'order_id',
						'item_count',
						'order_date',
						'short_order',
						'store_id',
					]);
	}

	public function product ()
	{
		/*return $this->belongsTo('App\Product', 'item_id', 'id_catalog')
					->where('is_deleted', 0);*/
		return $this->belongsTo('App\Product', 'item_code', 'product_model')
					->where('is_deleted', 0);
	}

	public function store ()
	{
		return $this->belongsTo('App\Store', 'store_id', 'store_id');
	}

	public function route ()
	{
		return $this->belongsTo('App\BatchRoute', 'batch_route_id', 'id');
	}

	public function groupedItems ()
	{
		return $this->hasMany('App\Item', 'batch_number', 'batch_number');
	}

	private function commaTrimmer ($string)
	{
		return trim($string, ",");
	}

	private function exploder ($string)
	{
		return explode(",", str_replace(" ", "", $this->commaTrimmer($string)));
	}

	public function shipInfo ()
	{
		return $this->hasOne('App\Ship', 'item_id', 'id');
	}

	/* Scope Search methods */

	public function scopeSearch ($query, $search_for, $search_in)
	{
		if ( !$search_for ) {
			return;
		}
		$values = $this->exploder($search_for);
		if ( $search_in == 'all' ) {
			return;
		} elseif ( $search_in == 'order' ) {

			return $query->where('order_id', 'REGEXP', implode("|", $values));

		} elseif ( $search_in == 'order_date' ) {

			$order_ids = Order::where('order_date', 'REGEXP', implode("|", $values))
							  ->lists('order_id')
							  ->toArray();

			return $query->whereIn('order_id', $order_ids);

		} elseif ( $search_in == 'store_id' ) {

			return $query->where('store_id', 'REGEXP', implode("|", $values));

		} elseif ( $search_in == 'state' ) {

			return $query->where('state', 'REGEXP', implode("|", $values));

		} elseif ( $search_in == 'description' ) {

			return $query->where('item_description', 'REGEXP', implode("|", $values));

		} elseif ( $search_in == 'item_code' ) {

			return $query->where('item_code', 'REGEXP', implode("|", $values));

		} elseif ( $search_in == 'batch' ) {

			return $query->where('batch_number', 'REGEXP', implode("|", $values));

		} elseif ( $search_in == 'batch_creation_date' ) {

			return $query->where('batch_creation_date', 'REGEXP', implode("|", $values));

		} else {
			return;
		}
	}

	public function scopeSearchBatch ($query, $batch_number)
	{
		if ( !$batch_number ) {
			return;
		}

		#return $query->where('batch_number', 'LIKE', $batch_number);
		return $query->whereIn('batch_number', explode(",", trim($batch_number, ",")));
	}

	public function scopeSearchRoute ($query, $batch_route_id)
	{
		if ( !$batch_route_id || $batch_route_id == 'all' ) {
			return;
		}

		return $query->where('batch_route_id', '=', $batch_route_id);
	}

	public function scopeSearchStation ($query, $station_id)
	{
		if ( !$station_id || $station_id == 'all' ) {
			return $query->where('station_name', '!=', Helper::getSupervisorStationName());
		}

		$station = Station::find($station_id);
		if ( !$station ) {
			return;
		}
		$station_name = $station->station_name;

		return $query->where('station_name', $station_name);
	}

	public function scopeSearchStatus ($query, $status)
	{
		if ( !$status || $status == 'all' ) {
			return;
		}

		return $query->where('item_order_status', '=', $status);
	}

	public function scopeSearchOptionText ($query, $option_text)
	{
		if ( !$option_text ) {
			return;
		}
		$trimmed_text = trim($option_text);
		$underscored_text = str_replace(" ", "_", $trimmed_text);

		return $query->where('item_option', 'LIKE', sprintf("%%%s%%", $underscored_text));
	}

	public function scopeSearchOrderIds ($query, $order_ids)
	{
		if ( !$order_ids ) {
			return;
		}

		$ids = explode(",", trim($order_ids, ","));

		return $query->where('order_id', 'REGEXP', implode("|", $ids));
	}

	public function scopeSearchDate ($query, $start_date, $end_date)
	{
		if ( !$start_date ) {
			return;
		}
		$orders = Order::withinDate($start_date, $end_date)
					   ->get([ 'order_id' ]);

		return $query->whereIn('order_id', $orders);
	}
	/*public function scopeSearchByOrderId ($query, $search_for, $search_in)
	{
		if ( !$search_for ) {
			return;
		}
		$values = $this->exploder($search_for);
		if ( $search_in == 'order' ) {
			$query->where('order_id', 'REGEXP', implode("|", $values));
		} else {
			return;
		}
	}

	public function scopeSearchByOrderDate ($query, $search_for, $search_in)
	{
		if ( !$search_for ) {
			return;
		}
		$values = $this->exploder($search_for);
		if ( $search_in == 'order_date' ) {
			$query->where('order_id', 'REGEXP', implode("|", $values));
		} else {
			return;
		}
	}

	public function scopeSearchByStoreId ($query, $search_for, $search_in)
	{
		if ( !$search_for ) {
			return;
		}
		$values = $this->exploder($search_for);
		if ( $search_in == 'order' ) {
			$query->where('order_id', 'REGEXP', implode("|", $values));
		} else {
			return;
		}
	}

	public function scopeSearchByState ($query, $search_for, $search_in)
	{
		if ( !$search_for ) {
			return;
		}
		$values = $this->exploder($search_for);
		if ( $search_in == 'order' ) {
			$query->where('order_id', 'REGEXP', implode("|", $values));
		} else {
			return;
		}
	}

	public function scopeSearchByDescription ($query, $search_for, $search_in)
	{
		if ( !$search_for ) {
			return;
		}
		$values = $this->exploder($search_for);
		if ( $search_in == 'order' ) {
			$query->where('order_id', 'REGEXP', implode("|", $values));
		} else {
			return;
		}
	}

	public function scopeSearchByItemId ($query, $search_for, $search_in)
	{
		if ( !$search_for ) {
			return;
		}
		$values = $this->exploder($search_for);
		if ( $search_in == 'order' ) {
			$query->where('order_id', 'REGEXP', implode("|", $values));
		} else {
			return;
		}
	}

	public function scopeSearchByBatch ($query, $search_for, $search_in)
	{
		if ( !$search_for ) {
			return;
		}
		$values = $this->exploder($search_for);
		if ( $search_in == 'order' ) {
			$query->where('order_id', 'REGEXP', implode("|", $values));
		} else {
			return;
		}
	}

	public function scopeSearchByBatchCreationDate ($query, $search_for, $search_in)
	{
		if ( !$search_for ) {
			return;
		}
		$values = $this->exploder($search_for);
		if ( $search_in == 'order' ) {
			$query->where('order_id', 'REGEXP', implode("|", $values));
		} else {
			return;
		}
	}*/
}
