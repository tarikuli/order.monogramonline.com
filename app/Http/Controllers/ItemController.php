<?php namespace App\Http\Controllers;

use App\BatchRoute;
use App\Department;
use App\Item;
use App\Product;
use App\RejectionReason;
use App\Setting;
use App\Station;
use App\StationLog;
use App\Template;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use DNS1D;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
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
		#return [$request->get('search_for_first'), $request->get('search_in_first')];
		$items = Item::with('order.customer', 'store', 'route.stations_list')
					 ->where('is_deleted', 0)
					 ->search($request->get('search_for_first'), $request->get('search_in_first'))
					 ->search($request->get('search_for_second'), $request->get('search_in_second'))
					 ->searchDate($request->get('start_date'), $request->get('end_date'))
					 ->searchTrackingDate($request->get('tracking_date'))
					 ->latest()
					 ->paginate(50);

		#return $items;

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
			'store_id'            => 'Store',
			'state'               => 'State',
			'description'         => 'Description',
			'item_code'           => 'SKU',
			'batch'               => 'Batch',
			'batch_creation_date' => 'Batch Creation date',
			'tracking_number'     => 'Tracking number',
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
						 ])
						 ->paginate(10000);
			},
		])
								  ->where('batch_routes.is_deleted', 0)
								  ->get();

		#return $batch_routes;
		return view('items.create_batch', compact('batch_routes', 'count', 'serial'));
	}

	public function postBatch (Requests\ItemToBatchCreateRequest $request)
	{
		$today = date('md', strtotime('now'));
		$batches = $request->get('batches');

		$acceptedGroups = [ ];

		/*$items = Item::where('batch_number', 'LIKE', sprintf("%s%%", $today))
					 ->groupBy('batch_number')
					 ->get();*/
		$items = Item::groupBy('batch_number')
					 ->where('batch_number', '!=', 0)
					 ->latest('batch_number')// newly added line, because, just count will overlap the batch again.
					 ->get();
		$fixed_value = 10000;
		$max_batch_number = count($items) ? $items->first()->batch_number : $fixed_value;
		$last_batch_number = $max_batch_number;
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
			$proposedBatch = sprintf("%d", $last_batch_number);

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
				$item->item_order_status = Helper::getBatchStatus();
				$item->batch_creation_date = date('Y-m-d H:i:s', strtotime('now'));
				$item->item_order_status_2 = 2;
				$item->save();

				/*$station_log = new StationLog();
				$station_log->item_id = $item_id;
				$station_log->batch_number = $batch_number;
				$station_log->station_id = $station_id;
				$station_log->started_at = date('Y-m-d h:i:s', strtotime("now"));
				$station_log->save();*/

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
					 ->groupBy('batch_number')#->latest('batch_creation_date')
					 ->latest('batch_number')
					 ->paginate(50);

		$routes = BatchRoute::where('is_deleted', 0)
							->latest()
							->lists('batch_route_name', 'id')
							->prepend('Select a route', 'all');

		$stations = Station::where('is_deleted', 0)
						   ->latest()
						   ->lists('station_description', 'id')
						   ->prepend('Select a station', 'all');

		// Get Station List
		$stations = Station::where('is_deleted', 0)
						   ->latest()
						   ->lists('station_description', 'id')
						   ->prepend('Select a station', 'all');
		//  Get Station Name by Station Request parameter.
		$station_name = Station::find($request->get('station'));
		$current_station_by_url = $station_name['station_name'];

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
			$items_on_station = [ ];
			foreach ( $item->groupedItems as $singleRow ) {
				if ( $start ) {
					$start = false;
					$previous_station = $singleRow->station_name;
				}
				$checker[] = $previous_station == $singleRow->station_name;
				$working_stations[] = $singleRow->station_name;
				$this_station = $singleRow->station_name;
				$items_on_station[$this_station] = array_key_exists($this_station, $items_on_station) ? ++$items_on_station[$this_station] : 1;
			}

			$current_station_name = '';
			$current_station_description = '';

			$next_station_name = '';
			$next_station_description = '';

			$station_list = $item->route->stations_list;
			$grab_next = false;
			$batch_statuses = array_keys(array_flip($item->groupedItems->lists('item_order_status')
																	   ->toArray()));
			# Flip the values to keys
			# then get the keys,
			# Faster than array_unique
			if ( in_array("active", $batch_statuses) ) {
				$batch_status = Helper::getBatchStatus("active");
			} elseif ( in_array("not started", $batch_statuses) ) {
				$batch_status = Helper::getBatchStatus("not started");
			} else {
				$batch_status = Helper::getBatchStatus("complete");
			}

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
			if ( !empty( $current_station_by_url ) ) {
				$current_station_name = $current_station_by_url;
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
			$row['batch_status'] = $batch_status;
			$row['current_station_item_count'] = $items_on_station[$current_station_name];

			$rows[] = $row;
		}
		#return $rows;

		#$statuses = (new Collection($this->statuses))->prepend('Select status', 'all');
		$statuses = (new Collection(Helper::getBatchStatusList()))->prepend('Select status', 'all');

		return view('routes.index', compact('rows', 'items', 'request', 'routes', 'stations', 'statuses'));
	}

	public function batch_details ($batch_number)
	{
		$items = Item::with('order', 'station_details', 'product')
					 ->where('batch_number', $batch_number)
					 ->get();
		if ( !count($items) ) {
			return view('errors.404');
		}
		#$bar_code = DNS1D::getBarcodeHTML($batch_number, "C39");
		$bar_code = Helper::getHtmlBarcode($batch_number);
		$statuses = Helper::getBatchStatusList();
		$route = BatchRoute::with('stations', 'template')
						   ->find($items[0]->batch_route_id);

		#$department_id = DB::table('department_station')->where('station_id', Station::where('station_name', $station_name)->first()->id)->first()->department_id;

		#$department = Department::find($department_id);
		#$department_name = $department ? $department->department_name : '';
		$stations = Helper::routeThroughStations($items[0]->batch_route_id);

		#return $items;
		$count = 1;

		return view('routes.batch_details', compact('items', 'bar_code', 'batch_number', 'statuses', 'route', 'stations', 'count', 'department_name'));
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
			return redirect()->to('items/grouped');
			#return view('errors.404');
		}
		#$bar_code = DNS1D::getBarcodeHTML($batch_number, "C39");
		#$bar_code = Helper::getHtmlBarcode($batch_number);
		$bar_code = null;
		#$statuses = $this->statuses;
		$statuses = Helper::getBatchStatusList();
		$route = BatchRoute::with('stations')
						   ->find($items[0]->batch_route_id);

		$department_id = DB::table('department_station')
						   ->where('station_id', Station::where('station_name', $station_name)
														->first()->id)
						   ->first()->department_id;
		$rejection_reasons = new Collection();
		if ( $items->count() ) {
			$station_name = $items[0]->station_name;
			$current_batch_station = Station::where('station_name', $station_name)
											->first();
			$rejection_reasons = RejectionReason::where('station_id', $current_batch_station->id)
												->orWhereNull('station_id')
												->where('is_deleted', 0)
												->orderBy('station_id', 'desc')
												->lists('rejection_message', 'id')
												->prepend('Select a reason', 0);
		}

		$department = Department::find($department_id);
		$department_name = $department ? $department->department_name : '';
		/*$stations = implode(" > ", array_map(function ($elem) {
			return $elem['station_name'];
		}, $route->stations->toArray()));
		$stations = str_replace($station_name, sprintf("<strong>%s</strong>", $station_name), $stations);*/
		$stations = Helper::routeThroughStations($items[0]->batch_route_id, $station_name);

		#return $items;
		$count = 1;

		return view('routes.show', compact('items', 'bar_code', 'batch_number', 'rejection_reasons', 'statuses', 'route', 'stations', 'count', 'department_name'));
	}

	public function updateBatchItems (Request $request, $batch_number)
	{
		$items = Item::where('batch_number', $batch_number)
					 ->get();

		if ( $request->has('status') ) {
			$status = $request->get('status');
			/*if ( !count($items) || !$status || !array_key_exists($status, $this->statuses) ) {
				return redirect()->back();
			}*/
			if ( !count($items) || !$status || !array_key_exists($status, Helper::getBatchStatusList()) ) {
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
								 ->where('station_name', $station_name)
								 ->get();
					Helper::populateShippingData($items);
				}
				$updates = [
					'station_name' => $next_station_name,
				];

				if ( $next_station_name == '' ) {
					$updates['item_order_status_2'] = 3;
					$updates['item_order_status'] = 'complete';
				} else {
					$updates['item_order_status'] = 'active';
				}
				$previousItems = Item::where('batch_number', $batch_number)
									 ->where('station_name', $station_name)
									 ->get();
				$items = Item::where('batch_number', $batch_number)
							 ->where('station_name', $station_name)
							 ->update($updates);
				if ( $next_station_name ) {
					foreach ( $previousItems as $item ) {
						$station_log = new StationLog();
						$station_log->item_id = $item->id;
						$station_log->batch_number = $item->batch_number;
						$station_log->station_id = Station::where('station_name', $station_name)
														  ->first()->id;
						#$station_log->started_at = date('Y-m-d h:i:s', strtotime("now"));
						$station_log->started_at = date('Y-m-d', strtotime("now"));
						$station_log->user_id = Auth::user()->id;
						$station_log->save();
					}
				}

				break;
			case 'reject':
				$supervisor_station = Helper::getSupervisorStationName();

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


				$items = Item::where('batch_number', $batch_number)
							 ->where('station_name', $station_name)
							 ->update([
								 'station_name'      => $supervisor_station,
								 'rejection_reason'  => $request->get('rejection_reason'),
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
		$file_name = sprintf("%s.csv", $batch_id);
		$fully_specified_path = sprintf("%s%s", $file_path, $file_name);
		$csv = Writer::createFromFileObject(new \SplFileObject($fully_specified_path, 'w+'), 'w');
		$csv->insertOne($columns);
		foreach ( $items as $item ) {
			$row = [ ];
			#$row[] = explode("-", $item->order_id)[2];
			$options = $item->item_option;
			$decoded_options = json_decode($options, true);

			foreach ( $template->exportable_options as $column ) {
				$result = '';

				if ( str_replace(" ", "", strtolower($column->option_name)) == "order#" ) { //if the value is order number
					#$result = array_slice(explode("-", $item->order_id), -1, 1);
					$exp = explode("-", $item->order_id); // explode the short order
					$result = $exp[count($exp) - 1];
					#$result = $item->order_id;
				} elseif ( str_replace(" ", "", strtolower($column->option_name)) == "sku" ) { // if the template value is sku
					$result = $item->item_code;
				} elseif ( str_replace(" ", "", strtolower($column->option_name)) == "po#" ) { // if string is po/batch number
					$result = $item->batch_number;
				} elseif ( str_replace(" ", "", strtolower($column->option_name)) == "orderdate" ) {//if the string is order date
					$result = substr($item->order->order_date, 0, 10);
				} else {
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
					if ( $values ) {
						$result = implode(",", $values);
					}
				}
				$row[] = $result;
				/*if ( $found ) {

				} else {
					$row[] = '';
				}*/
			}
			$csv->insertOne($row);
		}

		return response()->download($fully_specified_path);
	}

	public function release ($item_id)
	{
		$item = Item::find($item_id);
		if ( !$item ) {
			return redirect()
				->back()
				->withError([ 'error' => 'Not a valid batch id' ]);
		}
		$item->batch_number = 0;
		$item->batch_route_id = null;
		$item->station_name = null;
		$item->item_order_status = null;
		$item->batch_creation_date = null;
		$item->tracking_number = null;
		$item->item_order_status_2 = null;
		$item->previous_station = null;
		$item->item_status = null;
		$item->rejection_message = null;
		$item->rejection_reason = null;
		$item->save();

		return redirect()->back();
	}

	public function get_active_batch_by_sku (Request $request)
	{
		$items = Item::with('lowest_order_date', 'route.stations')
					 ->searchActiveByStation($request->get('station'))
					 ->where('batch_number', '!=', '0')
					 ->get();

		$stations = Station::where('is_deleted', 0)
						   ->latest()
						   ->get()
						   ->lists('custom_station_name', 'station_name')
						   ->prepend('Select a station', '');
		$rows = [ ];
		$total_count = 0;
		/*foreach ( $items->groupBy('station_name') as $station_name => $items_on_station ) {
			$groupBySKU = $items_on_station->groupBy('item_code');
			foreach ( $groupBySKU as $sku => $sku_groups ) {
				$count = $sku_groups->count();
				$total_count += $count;
				$rows[] = [
					'station_name'   => $station_name,
					'sku'            => $sku,
					'item_name'      => $sku_groups->first() ? $sku_groups->first()->item_description : "-",
					'min_order_date' => $sku_groups->count() ? substr($sku_groups->first()->lowest_order_date->order_date, 0, 10) : "",
					'item_count'     => $count,
					'action'         => url(sprintf('items/active_batch/sku/%s/%s', $sku, $station_name)),
				];
			}
		}*/
		#return $items->groupBy('item_code');
		foreach ( $items->groupBy('item_code') as $sku => $sku_groups ) {
			$route = $sku_groups->first()->route;
			$batch_stations = $route->stations->lists('custom_station_name', 'id')
											 ->prepend('Select station to change', '0');
			$count = $sku_groups->count();
			$total_count += $count;
			$rows[] = [
				'sku'            => $sku,
				'item_name'      => $sku_groups->first() ? $sku_groups->first()->item_description : "-",
				'min_order_date' => $sku_groups->count() ? substr($sku_groups->first()->lowest_order_date->order_date, 0, 10) : "",
				'item_count'     => $count,
				'action'         => url(sprintf('items/active_batch/sku/%s', $sku)),
				'route'          => sprintf("%s : %s", $route->batch_code, $route->batch_route_name),
				'batch_stations' => $batch_stations,
			];
		}

		return view('routes.active_batch_by_sku')
			->with('rows', $rows)
			->withRequest($request)
			->with('stations', $stations)
			->with('total_count', $total_count);
	}

	#public function get_sku_on_stations (Request $request, $sku, $station_name)

	public function get_sku_on_stations (Request $request, $sku)
	{
		$items = Item::with('lowest_order_date', 'order', 'station_details')
					 ->where('batch_number', '!=', 0)
					 ->whereNotNull('station_name')
					 ->Where('station_name', '!=', '')
					 ->where('item_code', $sku)#->where('station_name', $station_name)#->groupBy('batch_number')
					 ->get([ '*', ]);

		$rows = [ ];
		$total = 0;
		foreach ( $items as $item ) {
			$count = $item->total;
			$total += $count;
			$rows[] = [
				'batch_number'   => $item->batch_number,
				'sku'            => $sku,
				'item_name'      => $item->item_description,
				'item_count'     => $count,
				'min_order_date' => substr($item->lowest_order_date->order_date, 0, 10),
			];
		}

		$stations = Station::where('is_deleted', 0)
						   ->get()
						   ->lists('custom_station_name', 'id')
						   ->prepend('Select a station to change', '0');
		$count = 1;

		$items_in_stations = Station::whereIn('station_name', $items->lists('station_name'))
									->lists('id')
									->toArray();

		$rejection_reasons = RejectionReason::whereIn('station_id', $items_in_stations)
											->orWhereNull('station_id')
											->where('is_deleted', 0)
											->orderBy('station_id', 'desc')
											->lists('rejection_message', 'id')
											->prepend('Select a reason', 0);

		return view('routes.active_sku_show')
			->with('stations', $stations)#->with('station_name', $station_name)
			->with('count', $count)
			->with('sku', $sku)
			->with('rejection_reasons', $rejection_reasons)
			->with('items', $items)
			->with('total', $total);

	}

	public function changeStationBySKU (Request $request, $sku)
	{
		#return $request->all();
		#$station_id = $request->get('station');
		$station_id = $request->get('batch_stations');
		$station = Station::find($station_id);
		if ( !$station ) {
			return redirect()
				->back()
				->withErrors([
					'Not a valid station selected',
				]);
		}
		$station_name = $station->station_name;

		$items = Item::where('batch_number', '!=', 0)
					 ->whereNotNull('station_name')
					 ->Where('station_name', '!=', '')
					 ->where('item_code', $sku)
					 ->get();
		foreach ( $items as $item ) {
			$station_log = new StationLog();
			$station_log->item_id = $item->id;
			$station_log->batch_number = $item->batch_number;
			$station_log->station_id = $station_id;
			$station_log->started_at = date('Y-m-d', strtotime("now"));
			$station_log->user_id = Auth::user()->id;
			$station_log->save();
		}

		Item::with('lowest_order_date', 'order')
			->where('batch_number', '!=', 0)
			->whereNotNull('station_name')
			->Where('station_name', '!=', '')
			->where('item_code', $sku)
			->update([
				'station_name' => $station_name,
			]);

		return redirect()
			->to(url('/items/active_batch_group'))
			->with('success', 'Stations changed successfully.');
	}

	public function rejectDoneFromSKUList (Request $request)
	{
		$action = $request->get('action');
		#$station_name = $request->get('station_name');
		$sku = $request->get('sku');

		switch ( $action ) {
			case 'done':
				$items = Item::where('item_code', $sku)
							 ->where('batch_number', '!=', 0)
							 ->get();

				if ( count($items) == 0 ) {
					return redirect()->back();
				}

				foreach ( $items as $item ) {
					$batch_number = $item->batch_number;
					$current_item_station = $item->station_name;
					$next_station_name = Helper::getNextStationName($item->batch_route_id, $current_item_station);
					if ( in_array($next_station_name, Helper::$shippingStations) ) {
						$items = Item::where('batch_number', $batch_number)
									 ->where('station_name', $current_item_station)
									 ->where('item_code', $sku)
									 ->get();
						Helper::populateShippingData($items);
					}
					$updates = [
						'station_name' => $next_station_name,
					];

					if ( $next_station_name == '' ) {
						$updates['item_order_status_2'] = 3;
						$updates['item_order_status'] = 'complete';
					} else {
						$updates['item_order_status'] = 'active';
					}
					$previousItems = Item::where('batch_number', $batch_number)
										 ->where('station_name', $current_item_station)
										 ->get();
					$update_items = Item::where('batch_number', $batch_number)
										->where('station_name', $current_item_station)
										->where('item_code', $sku)
										->update($updates);
					if ( $next_station_name ) {
						foreach ( $previousItems as $item ) {
							$station_log = new StationLog();
							$station_log->item_id = $item->id;
							$station_log->batch_number = $item->batch_number;
							$station_log->station_id = Station::where('station_name', $current_item_station)
															  ->first()->id;

							$station_log->started_at = date('Y-m-d', strtotime("now"));
							$station_log->user_id = Auth::user()->id;
							$station_log->save();
						}
					}
				}

				return redirect()->to('/items/active_batch_group');
			case 'reject':
				$supervisor_station = Helper::getSupervisorStationName();

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

				$items = Item::where('item_code', $sku)
							 ->whereNotNull('station_name')
							 ->Where('station_name', '!=', '')
							 ->get();
				foreach ( $items as $rejected_item ) {
					$rejected_from_station = $rejected_item->station_name;
					$rejected_item->station_name = $supervisor_station;
					$rejected_item->rejection_reason = $request->get('rejection_reason');
					$rejected_item->rejection_message = trim($request->get('rejection_message'));
					$rejected_item->previous_station = $rejected_from_station;
					$rejected_item->save();
				}

				/*$items = Item::where('item_code', $sku)
							 ->whereNotNull('station_name')
							 ->Where('station_name', '!=', '')
							 ->update([
								 'station_name'      => $supervisor_station,
								 'rejection_reason'  => $request->get('rejection_reason'),
								 'rejection_message' => trim($request->get('rejection_message')),
								 'previous_station'  => $station_name,
							 ]);*/

				return redirect()->to('/items/active_batch_group');
			default:
				return redirect()->to('/');
		}
	}
}
