<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
	public function stations_list ()
	{
		return $this->belongsToMany('App\Station')
					->select([
						'station_name',
						'station_description',
						'station_id',
					]);
	}

	public function stations ()
	{
		return $this->belongsToMany('App\Station', 'department_station', 'department_id', 'station_id')
					->withTimestamps();
	}
}
