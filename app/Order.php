<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Status;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
	protected $fillable = [ '*' ];

	public function customer ()
	{
		return $this->belongsTo('App\Customer', 'order_id', 'order_id')
					->where('is_deleted', 0);
	}

	public function items ()
	{
		return $this->hasMany('App\Item', 'order_id', 'order_id')
					->where('is_deleted', 0);
	}

	public function shipping ()
	{
		return $this->hasMany('App\Ship', 'order_number', 'order_id')
					->where('is_deleted', 0);
	}

	public function store ()
	{
		return $this->belongsTo(Store::class);
	}

	public function order_sub_total ()
	{
		return $this->hasOne('App\Item', 'order_id', 'order_id')
					->where('is_deleted', 0)
					->groupBy('order_id')
					->select([
						'order_id',
						DB::raw('(SUM(item_unit_price * item_quantity)) AS sub_total'),
					]);
	}

	public function shippingInfo ()
	{
		return $this->hasMany('App\Ship', 'order_number', 'order_id');
	}

	public function notes ()
	{
		return $this->hasMany('App\Note', 'order_id', 'order_id');
	}

	public function scopeStoreId ($query, $store_id)
	{
		if ( $store_id == 'all' || null === $store_id ) {
			return;
		}

		return $query->where('store_id', $store_id);
	}

	public function scopeShipping ($query, $shipping_method)
	{
		if ( $shipping_method == 'all' || null === $shipping_method ) {
			return;
		}
		$order_ids = Customer::where('shipping', $shipping_method)
							 ->lists('order_id');

		return $query->whereIn('order_id', $order_ids);
	}

	public function scopeStatus ($query, $status)
	{
		if ( $status == 'all' || null === $status ) {
			return;
		}

		return $query->where('order_status', Status::where('status_code', $status)
												   ->first()->id);
	}

	public function scopeSearch ($query, $search_for, $search_in)
	{
		if ( !$search_for ) {
			return;
		}
		$replaced = str_replace(" ", "", $search_for);
		$values = explode(",", trim($replaced, ","));
		if ( $search_in == 'store_order' ) {
			$values = array_map(function ($value) {
				return str_ireplace([
					'M-',
					'S-',
				], "", $value);
			}, $values);

			return $query->where('short_order', 'REGEXP', implode("|", $values));
		}
		if ( $search_in == 'five_p_order' ) {
			$values = array_map(function ($value) {
				return intval($value);
			}, $values);

			return $query->where('id', 'REGEXP', implode("|", $values));
		}

		return;
	}

	public function scopeWithinDate ($query, $start_date, $end_date)
	{
		if ( !$start_date ) {
			return;
		}
		$starting = sprintf("%s 00:00:00", $start_date);
		$ending = sprintf("%s 23:59:59", $end_date ? $end_date : $start_date);

		return $query->where('order_date', '>=', $starting)
					 ->where('order_date', '<=', $ending);
	}
}
