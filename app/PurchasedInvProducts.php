<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchasedInvProducts extends Model
{
    protected $table = "purchased_inv_products";

    public function purchasedInvProduct_details ()
    {
    	return $this->hasMany('App\Inventory', 'stock_no_unique', 'stock_no' )
    				->where('is_deleted', 0);
    }

}
