<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Monogram\Helper;

class StationLog extends Model
{
	public function station ()
	{
		return $this->belongsTo('App\Station', 'station_id', 'id');
	}

	public function scopeSearchStation ($query, $station_id)
	{
		if ( !$station_id ) {
			return;
		}

		return $query->where('station_id', $station_id);
	}

	public function scopeWithinDate ($query, $start_date, $end_date)
	{
		if ( !$start_date ) {
			return;
		}

		$starting = sprintf("%s 00:00:00", $start_date);
		$ending = sprintf("%s 23:59:59", $end_date ? $end_date : $start_date);

		return $query->where('started_at', '>=', $starting)
					 ->where('started_at', '<=', $ending);
	}
}
