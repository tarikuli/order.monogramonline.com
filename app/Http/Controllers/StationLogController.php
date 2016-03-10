<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Item;
use App\Station;
use App\StationLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StationLogController extends Controller
{
	public function index (Request $request)
	{
		$logs = StationLog::with('station', 'item', 'user')
						  ->searchStation($request->get('station'))
						  ->searchUser($request->get('user_id'))
						  ->searchSKU($request->get('sku')) # this field is for getting any item id to get item code and search for that items
						  ->withinDate($request->get('start_date'), $request->get('end_date'))
						  ->latest('started_at')
						  ->latest('station_id')
						  ->latest('user_id')
						  ->groupBy('started_at', 'station_id', 'user_id')
						  ->paginate(500, [
							  DB::raw('SUM(1) as item_count'),
							  'station_id',
							  'user_id',
							  'item_id',
							  'started_at',
						  ]);

		#return $logs;
		$users = User::where('is_deleted', 0)
					 ->lists('username', 'id')
					 ->prepend('Select a user', 0);

		/*
		 * Get the skus from the station logs table
		 */

		$logged_item_ids = StationLog::lists('item_id')
									 ->toArray();
		/*
		 * Group by item code and get the distinct values from table
		 * where the item ids are from above operation
		 */
		$skus = Item::whereIn('id', $logged_item_ids)
					->groupBy('item_code')
					->lists('item_code', 'item_code')
					->prepend('Select a SKU', 0);

		$stations = Station::where('is_deleted', 0)
						   ->lists('station_description', 'id')
						   ->prepend('Select station', '0');
		$count = 1;

		return view('logs.index', compact('logs', 'request', 'count', 'stations', 'users', 'skus'));
	}
}
