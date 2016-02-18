<?php

namespace App\Http\Controllers;

use App\BatchRoute;
use App\Department;
use App\Item;
use App\Product;
use App\Setting;
use App\Station;
use App\StationLog;
use App\Template;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use DNS1D;
use Illuminate\Support\Facades\Session;
use League\Csv\Writer;
use Monogram\Helper;

class ItemController extends Controller
{

	private $statuses = [
		'active'      => 'Active',
		'not started' => 'Not started',
		'completed'   => 'Completed',
	];

	public function index (Request $request)
	{
		$items = Item::with('order.customer', 'store', 'route.stations_list')
					 ->where('is_deleted', 0)
					 ->search($request->get('search_for'), $request->get('search_in'))
					 ->searchDate($request->get('start_date'), $request->get('end_date'))
					 ->latest()
					 ->paginate(50);

		$unassignedProducts = Product::whereNull('batch_route_id')
									 ->orWhere('batch_route_id', Helper::getDefaultRouteId())
									 ->where('is_deleted', 0)
									 ->get();
		$unassignedProductCount = $unassignedProducts->count();

		/*$unassignedItems = DB::table('items')
							 ->leftJoin('products', 'items.item_code', '=', 'products.product_model')
							 ->select(DB::raw('count(*) as count'))
							 ->get();*/
		//todo:: we'll use in future
		//$unassignedItems = DB::select(DB::raw(sprintf("SELECT COUNT(*) as aggregate FROM items LEFT JOIN products ON items.item_code = products.product_model WHERE items.batch_number = 0 AND items.is_deleted = '0' AND products.batch_route_id != %d", Helper::getDefaultRouteId())));

		$unassignedItems = DB::table('items')
							 ->select(DB::raw('count(*) as aggregate'))
							 ->join('products', 'items.item_code', '=', 'products.product_model')
							 ->where('items.batch_number', '=', 0)
							 ->where('items.is_deleted', '=', 0)
							 ->where('products.batch_route_id', '!=', Helper::getDefaultRouteId())
							 ->get();

		$unassigned = count($unassignedItems) > 0 ? $unassignedItems[0]->aggregate : 0;
		$search_in = [
			'all'                 => 'All',
			'order'               => 'Order',
			'order_date'          => 'Order date',
			'store_id'            => 'Store',
			'state'               => 'State',
			'description'         => 'Description',
			'item_id'             => 'SKU',
			'batch'               => 'Batch',
			'batch_creation_date' => 'Batch Creation date',
		];

		#return $items;
		return view('items.index', compact('items', 'search_in', 'request', 'unassigned', 'unassignedProductCount'));
	}

	public function getBatch ()
	{
		$count = 1;
		$serial = 1;
		$batch_routes = BatchRoute::with([
			'stations_list',
			'itemGroups' => function ($q) {
				/*return $q->join('items', 'products.id_catalog', '=', 'items.item_id')*/
				return $q->join('items', 'products.product_model', '=', 'items.item_code')
						 ->where('items.is_deleted', 0)
						 ->where('items.batch_number', '0')
						 ->join('orders', 'orders.order_id', '=', 'items.order_id')
						 ->where('orders.is_deleted', 0)
						 ->addSelect([
							 DB::raw('items.id AS item_table_id'),
							 'items.item_id',
							 'items.item_code',
							 'items.order_id',
							 'items.item_quantity',
							 DB::raw('orders.id as order_table_id'),
							 'orders.order_id',
							 'orders.order_date',
						 ]);
			},
		])
								  ->where('batch_routes.is_deleted', 0)
								  ->get();

		#return $batch_routes;

		return view('items.create_batch', compact('batch_routes', 'count', 'serial'));
	}

	public function postBatch (Requests\ItemToBatchCreateRequest $request)
	{
		$today = date('ymd', strtotime('now'));
		$batches = $request->get('batches');

		$acceptedGroups = [ ];

		$items = Item::where('batch_number', 'LIKE', sprintf("%s%%", $today))
					 ->groupBy('batch_number')
					 ->get();
		$last_batch_number = count($items);
		$current_group = -1;

		foreach ( $batches as $preferredBatch ) {
			list( $inGroup, $batch_route_id, $item_id ) = explode("|", $preferredBatch);
			if ( $inGroup != $current_group ) {
				#$max_batch_id++;
				$last_batch_number++;
				$current_group = $inGroup;
			}
			#$batch_code = BatchRoute::find($batch_route_id)->batch_code;
			#$proposedBatch = sprintf("%s~%s~%s", $today, $batch_code, $max_batch_id);
			$proposedBatch = sprintf("%s%04d", $today, $last_batch_number);

			$acceptedGroups[$inGroup][] = [
				$item_id,
				$proposedBatch,
				$batch_route_id,
			];
		}

		#return $acceptedGroups;
		foreach ( $acceptedGroups as $groups ) {
			foreach ( $groups as $itemGroup ) {
				$item_id = $itemGroup[0];
				$batch_number = $itemGroup[1];
				$batch_route_id = $itemGroup[2];

				$item = Item::find($item_id);
				$item->batch_number = $batch_number;
				$item->batch_route_id = $batch_route_id;
				$batch = BatchRoute::with('stations_list')
								   ->find($batch_route_id);
				$station_id = $batch->stations_list[0]->station_id;
				$station_name = $batch->stations_list[0]->station_name;
				$item->station_name = $station_name;
				$item->item_order_status = 'active';
				$item->batch_creation_date = date('Y-m-d H:i:s', strtotime('now'));
				$item->save();

				$station_log = new StationLog();
				$station_log->item_id = $item_id;
				$station_log->batch_number = $batch_number;
				$station_log->station_id = $station_id;
				$station_log->started_at = date('Y-m-d h:i:s', strtotime("now"));
				$station_log->save();

			}
		}

		return redirect(url('items/grouped'));
	}

	public function getGroupedBatch (Request $request)
	{
		if ( $request->has('station') ) {
			Session::put('station', $request->get('station'));
		}

		$items = Item::with('lowest_order_date', 'route.stations_list', 'groupedItems')
					 ->where('batch_number', '!=', '0')
					 ->searchBatch($request->get('batch'))
					 ->searchRoute($request->get('route'))
					 ->searchStation(session('station', 'all'))
					 ->searchStatus($request->get('status'))
					 ->groupBy('batch_number')
					 ->latest('batch_creation_date')
					 ->paginate(50);

		$routes = BatchRoute::where('is_deleted', 0)
							->latest()
							->lists('batch_route_name', 'id')
							->prepend('Select a route', 'all');

		$stations = Station::where('is_deleted', 0)
						   ->latest()
						   ->lists('station_description', 'id')
						   ->prepend('Select a station', 'all');
		$rows = [ ];
		foreach ( $items as $item ) {
			$row = [ ];
			$row['batch_number'] = $item->batch_number;
			$row['batch_creation_date'] = substr($item->batch_creation_date, 0, 10);
			$row['route_code'] = $item->route->batch_code;
			$row['route_name'] = $item->route->batch_route_name;
			$row['lines'] = count($item->groupedItems);
			#$item_first_station = $item->groupedItems[0]->station_name;
			$previous_station = '';
			$start = true;
			$checker = [ ];
			$working_stations = [ ];
			foreach ( $item->groupedItems as $singleRow ) {
				if ( $start ) {
					$start = false;
					$previous_station = $singleRow->station_name;
				}
				$checker[] = $previous_station == $singleRow->station_name;
				$working_stations[] = $singleRow->station_name;
			}
			$current_station_name = '';
			$current_station_description = '';

			$next_station_name = '';
			$next_station_description = '';

			$station_list = $item->route->stations_list;
			$grab_next = false;

			if ( count(array_unique($checker)) == 1 ) {
				foreach ( $station_list as $station ) {
					if ( $grab_next ) {
						$grab_next = false;
						$next_station_name = $station->station_name;
						$next_station_description = $station->station_description;
						break;
					}
					if ( in_array($station->station_name, $working_stations) ) {
						$current_station_name = $station->station_name;
						$current_station_description = $station->station_description;
						$grab_next = true;
					}
				}
				#$item->groupedItems[0]->station_name;
			} else {
				foreach ( $station_list as $station ) {
					if ( $grab_next ) {
						$grab_next = false;
						$next_station_name = $station->station_name;
						$next_station_description = $station->station_description;
						break;
					}
					if ( in_array($station->station_name, $working_stations) ) {
						$current_station_name = $station->station_name;
						$current_station_description = $station->station_description;
						$grab_next = true;
					}
				}
			}
			if ( $current_station_name == '' ) {
				$current_station_name = Helper::getSupervisorStationName();
				$current_station_description = "Supervisor station";
			}
			$row['current_station_name'] = $current_station_name;
			$row['current_station_description'] = $current_station_description;
			$row['current_station_since'] = substr($item->batch_creation_date, 0, 10);
			$row['next_station_name'] = $next_station_name;
			$row['next_station_description'] = $next_station_description;
			$row['min_order_date'] = substr($item->lowest_order_date->order_date, 0, 10);

			$rows[] = $row;
		}

		$statuses = (new Collection($this->statuses))->prepend('Select status', 'all');

		return view('routes.index', compact('rows', 'items', 'request', 'routes', 'stations', 'statuses'));
	}

	public function getBatchItems ($batch_number, $station_name)
	{
		if ( $station_name == Helper::getSupervisorStationName() ) {
			return redirect()->back();
		}
		$items = Item::with('order')
					 ->where('batch_number', $batch_number)
					 ->where('station_name', $station_name)
					 ->get();
		if ( !count($items) ) {
			return view('errors.404');
		}
		$bar_code = DNS1D::getBarcodeHTML($batch_number, "C39");
		$statuses = $this->statuses;
		$route = BatchRoute::with('stations')
						   ->find($items[0]->batch_route_id);

		$department_id = DB::table('department_station')
						   ->where('station_id', Station::where('station_name', $station_name)
														->first()->id)
						   ->first()->department_id;

		$department = Department::find($department_id);
		$department_name = $department ? $department->department_name : '';
		/*$stations = implode(" > ", array_map(function ($elem) {
			return $elem['station_name'];
		}, $route->stations->toArray()));
		$stations = str_replace($station_name, sprintf("<strong>%s</strong>", $station_name), $stations);*/
		$stations = Helper::routeThroughStations($items[0]->batch_route_id, $station_name);

		#return $items;
		$count = 1;

		return view('routes.show', compact('items', 'bar_code', 'batch_number', 'statuses', 'route', 'stations', 'count', 'department_name'));
	}

	public function updateBatchItems (Request $request, $batch_number)
	{
		$items = Item::where('batch_number', $batch_number)
					 ->get();

		if ( $request->has('status') ) {
			$status = $request->get('status');
			if ( !count($items) || !$status || !array_key_exists($status, $this->statuses) ) {
				return redirect()->back();
			}

			foreach ( $items as $item ) {
				$item->item_order_status = $request->get('status');
				$item->save();
			}
		} elseif ( $request->has('station') ) {
			$station = Station::where('station_name', $request->get('station'))
							  ->first();
			$station_name = $station->station_name;
			foreach ( $items as $item ) {
				$item->station_name = $station_name;
				$item->save();
			}
		}

		return redirect()->back();
	}

	public function postBatchItems (Request $request, $batch_number, $station_name)
	{
		$action = $request->get('action');
		switch ( $action ) {
			case 'done':
				$item = Item::where('batch_number', $batch_number)
							->where('station_name', $station_name)
							->first();

				if ( count($item) == 0 ) {
					return redirect()->back();
				}
				$next_station_name = Helper::getNextStationName($item->batch_route_id, $item->station_name);
				if ( in_array($next_station_name, Helper::$shippingStations) ) {
					$items = Item::where('batch_number', $batch_number)
								 ->where('station_name', $station_name)#->where('station_name', $station_name)
								 ->get();
					Helper::populateShippingData($items);
				}
				$items = Item::where('batch_number', $batch_number)
							 ->where('station_name', $station_name)
							 ->update([ 'station_name' => $next_station_name ]);

				break;
			case 'reject':
				$supervisor_station = Helper::getSupervisorStationName();

				$items = Item::where('batch_number', $batch_number)
							 ->where('station_name', $station_name)
							 ->update([
								 'station_name'      => $supervisor_station,
								 'rejection_message' => trim($request->get('rejection_message')),
								 'previous_station'  => $station_name,
							 ]);

				break;
			default:
				break;
		}

		return redirect(url('items/grouped'));
	}

	public function export_batch (Request $request, $id)
	{
		if ( !$id || $id == 0 ) {
			return view('errors.404');
		}
		$batch_id = intval($id);

		$items = Item::where('batch_number', $batch_id)
					 ->get();

		if ( !$items ) {
			return view('errors.404');
		}

		$route_id = $items[0]->batch_route_id;
		$route = BatchRoute::find($route_id);

		$template_id = $route->export_template;
		$template = Template::with('exportable_options')
							->find($template_id);

		$columns = $template->exportable_options->lists('option_name')
												->toArray(); #->prepend('Order id');

		$file_path = sprintf("%s/assets/exports/batches/", public_path());
		$file_name = sprintf("batch_%d-%s-%s.csv", $batch_id, date("y-m-d", strtotime('now')), str_random(5));
		$fully_specified_path = sprintf("%s%s", $file_path, $file_name);

		$csv = Writer::createFromFileObject(new \SplFileObject($fully_specified_path, 'a+'), 'w');
		$csv->insertOne($columns);

		foreach ( $items as $item ) {
			$row = [ ];
			#$row[] = explode("-", $item->order_id)[2];
			$options = $item->item_option;
			$decoded_options = json_decode($options, true);

			foreach ( $template->exportable_options as $column ) {
				$keys = explode(",", $column->value);
				$found = false;
				$values = [ ];
				foreach ( $keys as $key ) {
					$trimmed_key = implode("_", explode(" ", trim($key)));
					if ( array_key_exists($trimmed_key, $decoded_options) ) {
						$values[] = $decoded_options[$trimmed_key];
						$found = true;
					}
				}
				if ( $found ) {
					$row[] = implode(",", $values);
				} else {
					$row[] = '';
				}
			}
			$csv->insertOne($row);
		}

		return response()->download($fully_specified_path);
	}
}
