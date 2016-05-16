<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
	public function products ()
	{
		return $this->hasMany('App\PurchaseProduct', 'purchase_id', 'id')
					->where('is_deleted', 0);
	}

	public function vendor_details ()
	{
		return $this->belongsTo('App\Vendor', 'vendor_id', 'id')
					->where('is_deleted', 0);
	}
}
