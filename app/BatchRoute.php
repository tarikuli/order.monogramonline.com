<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BatchRoute extends Model
{
	public function template ()
	{
		return $this->belongsTo('App\Template', 'export_template', 'id');
	}

	public function itemGroups ()
	{
		return $this->hasMany('App\Product')
					->where('products.is_deleted', 0)
					->select([
						DB::raw('products.id as product_table_id'),
						'products.store_id',
						'products.batch_route_id',
						'products.id_catalog',
						'products.product_model',
						'products.allow_mixing',
					]);
	}

	public function stations ()
	{
		return $this->belongsToMany('App\Station', 'batch_route_station', 'batch_route_id', 'station_id')
					->withTimestamps();
	}

	public function products ()
	{
		return $this->hasMany('App\Product', 'batch_route_id', 'id')
					->where('is_deleted', 0)
					->select([
						'id',
						'batch_route_id',
						'id_catalog',
					]);
	}

	public function stations_list ()
	{
		return $this->belongsToMany('App\Station')
					->select([
						'station_name',
						'station_description',
						'station_id',
					]);
	}
}
