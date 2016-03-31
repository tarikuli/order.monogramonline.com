<?php namespace App\Http\Controllers;


use App\BatchRoute;
use App\Department;
use App\Item;
use App\Order;
use App\StationLog;
use App\Status;
use Illuminate\Http\Request;
use App\Station;

use App\Http\Requests\StationCreateRequest;
use App\Http\Requests\StationUpdateRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Csv\Writer;
use Monogram\Helper;

class StationController extends Controller
{
	private $statuses = [
		'not started' => 'Not started',
		'active'      => 'Active',
		'completed'   => 'Completed',
	];

	public function index ()
	{
		$count = 1;
		$stations = Station::with('departments_list')
						   ->where('is_deleted', 0)
						   ->latest()
						   ->paginate(50);

		$departments = Department::where('is_deleted', 0)
								 ->lists('department_name', 'id')
								 ->prepend('No department assigned', '');


		return view('stations.index', compact('stations', 'count', 'departments'));
	}

	public function create ()
	{
		return view('stations.create');
	}

	public function store (StationCreateRequest $request)
	{
		$station = new Station();
		$station->station_name = trim($request->get('station_name'));
		$station->station_description = $request->get('station_description');
		$station->save();

		session()->flash('success', 'Station is successfully added');

		return redirect(url('stations'));
	}

	public function show ($id)
	{
		$station = Station::where('is_deleted', 0)
						  ->find($id);

		if ( !$station ) {
			return view('errors.404');
		}

		return view('stations.show', compact('station'));
	}

	public function edit ($id)
	{
		$station = Station::where('is_deleted', 0)
						  ->find($id);

		if ( !$station ) {
			return view('errors.404');
		}

		return view('stations.edit', compact('station'));
	}

	public function update (StationUpdateRequest $request, $id)
	{
		$station = Station::where('is_deleted', 0)
						  ->find($id);

		if ( !$station ) {
			return view('errors.404');
		}

		$station->station_name = trim($request->get('station_name'));
		$station->station_description = $request->get('station_description');
		$station->save();

		session()->flash('success', 'Station is successfully updated.');

		return redirect(url('stations'));
	}

	public function destroy ($id)
	{
		$station = Station::where('is_deleted', 0)
						  ->find($id);

		if ( !$station ) {
			return view('errors.404');
		}

		$station->is_deleted = 1;
		$station->save();

		return redirect(url('stations'));
	}

	public function status (Request $request)
	{
		$station_name = $request->get('station_name');
		$stations = Station::where('is_deleted', 0)
						   ->lists('station_description', 'station_name');
		$stations->prepend('All', 'all');
		$items = [ ];
		if ( $station_name && $station_name != 'all' ) {
			$items = Item::where('station_name', $station_name)
						 ->where('is_deleted', 0)
						 ->paginate(50);
		}

		return view('stations.status', compact('station_name', 'stations', 'items', 'request'));
	}

	public function my_station ()
	{
		$station_id = session('station_id');
		$station = Station::find($station_id);
		if ( !$station ) {
			return view('errors.404');
		}
		$items = Item::where('station_name', $station->station_name)
					 ->where('is_deleted', 0)
					 ->paginate(50);
		$station_description = $station->station_description;

		return view('stations.my_station', compact('items', 'station_description'));

	}

	public function change (Request $request)
	{
		$action = $request->get('action');
		$item_id = $request->get('item_id');

		#$item = Item::find($item_id);
		$item = Item::where('id', $item_id)
					->first();
		if ( !$item ) {
			return view('errors.404');
		}

		$batch_route_id = $item->batch_route_id;
		$current_station_name = $item->station_name;
		$next_station_name = Helper::getNextStationName($batch_route_id, $current_station_name);

		if ( $action == 'done' ) {

			if ( in_array($next_station_name, Helper::$shippingStations) ) {
				Helper::populateShippingData($item);
			}
			$item->station_name = $next_station_name;
			Item::where('batch_number', $item->batch_number)
				->update([
					'item_order_status' => 'active',
				]);
			#$item->item_order_status = "active";

			if ( $next_station_name == '' ) {
				$item->item_order_status_2 = 3;
				$item->item_order_status = "complete";
			} else {
				$station_log = new StationLog();
				$station_log->item_id = $item->id;
				$station_log->batch_number = $item->batch_number;
				$station_log->station_id = Station::where('station_name', $next_station_name)
												  ->first()->id;
				$station_log->started_at = date('Y-m-d', strtotime("now"));
				$station_log->user_id = Auth::user()->id;
				$station_log->save();
			}
			$item->save();

		} elseif ( $action == 'reject' ) {
			$rules = [
				'rejection_reason'  => 'required|exists:rejection_reasons,id',
				'rejection_message' => 'required',
			];
			$validation = Validator::make($request->all(), $rules);
			if ( $validation->fails() ) {
				return redirect()
					->back()
					->withErrors($validation);
			}
			$item->previous_station = $item->station_name;
			$item->station_name = Helper::getSupervisorStationName();
			$item->rejection_reason = $request->get('rejection_reason');
			$item->rejection_message = trim($request->get('rejection_message'));
			$item->save();
		}

		$batch_item_count = Item::where('batch_route_id', $batch_route_id)
								->where('station_name', $current_station_name)
								->where('is_deleted', 0)
								->count();

		if ( $batch_item_count ) {
			return redirect()->back();
		} else {
			return redirect(url('items/grouped'));
		}
	}

	public function supervisor (Request $request)
	{
		$routes = BatchRoute::where('is_deleted', 0)
							->latest()
							->lists('batch_route_name', 'id')
							->prepend('Select a route', 'all');

		$stations = Station::where('is_deleted', 0)
						   ->latest()
						   ->lists('station_description', 'id')
						   ->prepend('Select a station', 'all');

		#$statuses = (new Collection($this->statuses))->prepend('Select status', 'all');
		$statuses = (new Collection(Helper::getBatchStatusList()))->prepend('Select status', 'all');
		$item_statuses = Status::where('is_deleted', 0)
							   ->lists('status_name', 'id');

		$items = null;
		if ( count($request->all()) ) {
			$items = Item::with('route.stations_list', 'order')
						 ->searchBatch($request->get('batch'))
						 ->searchRoute($request->get('route'))
						 ->searchStatus($request->get('status'))
						 ->searchStation($request->get('station'))
						 ->searchOptionText($request->get('option_text'))
						 ->searchOrderIds($request->get('order_id'))
						 ->where('is_deleted', 0)
						 ->paginate(50);
		} else {
			$items = Item::with('route.stations_list', 'order')
						 ->where('is_deleted', 0)
						 ->whereNotNull('batch_number')
						 ->where('station_name', Helper::getSupervisorStationName())
						 ->paginate(50);
		}

		return view('stations.supervisor', compact('items', 'request', 'routes', 'stations', 'statuses', 'item_statuses'));
	}

	public function on_change_apply (Request $request)
	{
		$item_id = $request->get('item_id');
		$item = Item::find($item_id);
		if ( !$item ) {
			return redirect()->back();
		}
		if ( $request->has('station_name') ) {
			$station_name = $request->get('station_name');
			$item->station_name = $station_name;
			$item->rejection_message = null;
			$item->rejection_reason = null;
			$item->save();
		} elseif ( $request->has('order_status') ) {
			$order_status = $request->get('order_status');
			$order = Order::where('order_id', $item->order_id)
						  ->first();
			if ( $order ) {
				$order->order_status = $order_status;
				$order->save();
			}

		} elseif ( $request->has('item_order_status_2') ) {
			$item_order_status_2 = $request->get('item_order_status_2');
			$item->item_order_status_2 = $item_order_status_2;
			$item->save();
		}

		return redirect()->back();
	}

	public function summary ()
	{
		$items = Item::where('station_name', '!=', '')
					 ->groupBy('station_name')
					 ->get();

		$summaries = [ ];

		foreach ( $items as $item ) {
			$summary = [ ];

			$station_name = $item->station_name;

			$items_count = Item::where('station_name', $station_name)
							   ->groupBy('station_name')
							   ->first([ DB::raw('SUM(item_quantity) as items_count') ])->items_count;
			$earliest_batch_creation_date = Item::where('station_name', $station_name)
												->orderBy('batch_creation_date', 'asc')
												->first()->batch_creation_date;
			$order_ids = Item::where('station_name', $station_name)
							 ->get();
			$earliest_order_date = Order::whereIn('order_id', $order_ids->lists('order_id')
																		->toArray())
										->orderBy('order_date', 'asc')
										->first()->order_date;

			$station = Station::where('station_name', $station_name)
							  ->first();

			$summary['station_id'] = $station->id;
			$summary['station_description'] = $station->station_description;
			$summary['station_name'] = $station_name;
			$summary['items_count'] = $items_count;
			$summary['earliest_batch_creation_date'] = substr($earliest_batch_creation_date, 0, 10);
			$summary['earliest_order_date'] = substr($earliest_order_date, 0, 10);

			$summaries[] = $summary;
		}

		return view('stations.summary', compact('summaries'));
	}

	public function getBulkChange ()
	{
		$stations = Station::where('is_deleted', 0)
						   ->lists('station_description', 'station_name')
						   ->prepend('Select a station', null);

		return view('items.bulk_change')->with('stations', $stations);
	}

	public function postBulkChange (Request $request)
	{
		$posted_station = trim($request->get('station'));
		// check if station exists
		$station = Station::where('is_deleted', 0)
						  ->where('station_name', '=', $posted_station)
						  ->first();

		if ( !$station ) {
			return redirect()
				->back()
				->withInput()
				->withErrors([
					'error' => 'Selected station is not valid',
				]);
		}

		// station exists
		// divide the given batches
		$posted_batches = $request->get('batches');
		// remove newlines and spaces
		$posted_batches = trim(preg_replace('/\s+/', '', $posted_batches));

		$batches = array_map(function ($batch) {
			$integer_value_of_batch_number = intval($batch);
			// safety check
			// if the integer value of a batch number is 0,
			// table having 0 as batch number has different meaning
			// thus returns -1, table will never have any value -1;
			return $integer_value_of_batch_number ?: -1;
		}, explode(",", $posted_batches));

		$items = Item::whereIn('batch_number', $batches)
					 ->get();
		if ( $items->count() == 0 ) {
			return redirect()
				->back()
				->withInput()
				->withErrors([
					'error' => 'Batches given are not valid',
				]);
		}
		$changed = $this->apply_station_change($items, $posted_station);

		return redirect()
			->back()
			->with('success', sprintf("Total of: %d items moved to station: %s", $changed, $posted_station));
	}

	private function apply_station_change ($items, $station_name)
	{
		foreach ( $items as $item ) {
			$item->station_name = $station_name;
			$item->save();

			$station_log = new StationLog();
			$station_log->item_id = $item->id;
			$station_log->batch_number = $item->batch_number;
			$station_log->station_id = Station::where('station_name', $station_name)
											  ->first()->id;
			$station_log->started_at = date('Y-m-d', strtotime("now"));
			$station_log->user_id = Auth::user()->id;
			$station_log->save();
		}

		return $items->count();
	}

	public function getExportStationLog ()
	{
		return view('stations.export_station');
	}

	public function postExportStationLog (Request $request)
	{
		// grab the month
		$start = trim($request->get('start_date'));
		#$end = trim($request->get('end_date'));

		if ( empty( $start ) ) {
			return redirect()
				->back()
				->withInput()
				->withErrors([
					'error' => 'Date is not selected',
				]);
		}
		$month_starts = "01";
		$month_ends = date('t', strtotime($request->get('start_date')));

		$first_day_of_the_month = sprintf("%s-%s", $start, $month_starts);
		$end_day_of_the_month = sprintf("%s-%s", $start, $month_ends);

		/*
		 * DB QUERY: SELECT station_id, started_at, SUM( 1 ) FROM  `station_logs` WHERE started_at >=  '2016-03-01' AND started_at <=  '2016-03-31' GROUP BY station_id, started_at ORDER BY station_id
		 * SELECT station_id, started_at, user_id, sum(1) FROM `station_logs`  where started_at >= '2016-03-01' and started_at <= '2016-03-31' group by station_id, user_id, started_at order by started_at
		 */

		$station_logs = StationLog::with('user', 'station')
								  ->searchWithinMonthGroupLog($first_day_of_the_month, $end_day_of_the_month)
								  ->orderBy('started_at')
								  ->get([
									  'started_at',
									  'station_id',
									  'user_id',
									  DB::raw('SUM(1) as item_count'),
								  ]);
		$dates = $this->range_date($first_day_of_the_month, $end_day_of_the_month);
		$header = array_merge([
			'station',
			// uncomment user if user is required
			#'user',
		], $dates, [ 'total' ]);

		/*
		 * File write operation
		 */

		$file_path = sprintf("%s/assets/exports/station_log/", public_path());
		$file_name = sprintf("station_log-%s-%s.csv", date("y-m-d", strtotime('now')), str_random(5));
		$fully_specified_path = sprintf("%s%s", $file_path, $file_name);
		$csv = Writer::createFromFileObject(new \SplFileObject($fully_specified_path, 'w+'), 'w');
		$csv->insertOne($header);
		foreach ( $station_logs as $log ) {
			$station_name = $log->station->station_name;
			// uncomment user if user is required
			# $user = $log->user->username;
			$row = [ ];
			$row[] = $station_name;
			// uncomment user if user is required
			#$row[] = $user;
			$month_total_task_per_station = 0;
			foreach ( $dates as $date ) {
				$per_day = 0;
				if ( $date == $log->started_at ) {
					$per_day = $log->item_count;
				}
				$row[] = $per_day;
				$month_total_task_per_station += $per_day;
			}
			$row[] = $month_total_task_per_station;
			$csv->insertOne($row);
		}

		return response()->download($fully_specified_path);
	}

	private function range_date ($first, $last)
	{
		$arr = array();
		$now = strtotime($first);
		$last = strtotime($last);

		while ( $now <= $last ) {
			$arr[] = date('Y-m-d', $now);
			$now = strtotime('+1 day', $now);
		}

		return $arr;
	}

}
