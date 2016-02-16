<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
	public function scopeSearch ($query, $store_id)
	{
		if ( !$store_id ) {
			return;
		}

		return $query->where('store_id', $store_id);
	}

	public function parameters ()
	{
		return $this->hasMany('App\Parameter', 'store_id', 'store_id');
	}
}
