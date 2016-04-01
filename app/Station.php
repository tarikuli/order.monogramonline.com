<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
	public function departments_list ()
	{
		return $this->belongsToMany('App\Department')
					->select([
						'department_name',
						'department_code',
						'department_id',
					]);
	}

	public function reject_reasons ()
	{
		return $this->hasMany('App\RejectionReason', 'station_id', 'id');
	}

	public function getCustomStationNameAttribute ()
	{
		return sprintf("%s => %s", $this->station_name, $this->station_description);
	}
}
