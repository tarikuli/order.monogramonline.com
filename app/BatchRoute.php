<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BatchRoute extends Model
{
	private function tableColumns ()
	{
		$columns = $this->getConnection()
						->getSchemaBuilder()
						->getColumnListing($this->getTable());
		$remove_columns = [
			'updated_at',
			'created_at',
			'id',
			'is_deleted',
		];

		return array_diff($columns, $remove_columns);
	}

	public static function getTableColumns ()
	{
		return (new static())->tableColumns();
	}

	public function template ()
	{
		return $this->belongsTo('App\Template', 'export_template', 'id');
	}

	/*public function itemGroups ()
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
	}*/

	public function itemGroups ()
	{
		return $this->hasMany('App\Option')
					->select([
						//DB::raw('parameter_options.id as product_table_id'),
						'parameter_options.store_id',
						'parameter_options.batch_route_id',
						'parameter_options.allow_mixing',
						'parameter_options.parent_sku',
						'parameter_options.child_sku',
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
