<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
	protected $table = "purchased_products";

	public function product_details ()
	{
		return $this->belongsTo('App\Product', 'product_id', 'id')
					->where('is_deleted', 0);
	}
}
