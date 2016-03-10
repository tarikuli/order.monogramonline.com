<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Station;
use App\StationLog;
use Illuminate\Http\Request;

class StationLogController extends Controller
{
	public function index (Request $request)
	{
		$logs = StationLog::with('station')
						  ->searchStation($request->get('station'))
						  ->withinDate($request->get('start_date'), $request->get('end_date'))
						  ->groupBy('station_id')
						  ->paginate(50, [
							  \DB::raw('COUNT(*) as item_count'),
							  'station_id',
						  ]);

		$stations = Station::where('is_deleted', 0)
						   ->lists('station_description', 'id')
						   ->prepend('Select station', '0');
		$count = 1;

		return view('logs.index', compact('logs', 'request', 'count', 'stations'));
	}
}
