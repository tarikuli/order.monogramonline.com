<?php namespace App\Http\Controllers;

use App\BatchRoute;
use App\Department;
use App\Item;
use App\Option;
use App\Order;
use App\Parameter;
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
use Illuminate\Support\MessageBag;
use Illuminate\Support\Collection;
use DNS1D;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
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
					 ->searchStatus($request->get('status'))
					 ->latest()
					 ->paginate(50);

		// For debug
		#return $items;

		$unassignedProducts = Option::where(function ($query) {
			return $query->whereNull('batch_route_id')
						 ->orWhere('batch_route_id', Helper::getDefaultRouteId());
		})
									->get();
		$unassignedOrderCount = Item::whereIn('child_sku', $unassignedProducts->lists('child_sku')
																			  ->toArray())
									->where('is_deleted', 0)
									->count();
		$unassignedProductCount = $unassignedProducts->count();

		$unassigned = Helper::countPossibleBatches();
		$emptyStationsCount = count(Helper::getEmptyStation());
		if($emptyStationsCount == 0){
			$emptyStationsCount = "";
		}else{
			$emptyStationsCount = $emptyStationsCount." route have no stations assigned.";
		}
		$search_in = [
			'all'                 => 'All',
			'order'               => 'Order',
			'5p_order'            => '5P-Order',
			'store_id'            => 'Store',
			'state'               => 'State',
			'description'         => 'Description',
			'item_code'           => 'SKU',
			'batch'               => 'Batch',
			'batch_creation_date' => 'Batch Creation date',
			'tracking_number'     => 'Tracking number',
		];

		$statuses = (new Collection(Helper::getBatchStatusList()))->prepend('Select status', 'all');

		#return $items;
		return view('items.index', compact('items', 'emptyStationsCount', 'search_in', 'request', 'unassigned', 'unassignedProductCount', 'unassignedOrderCount', 'statuses'));
	}

	public function getBatch ()
	{
		$count = 1;
		$serial = 1;

		$emptyStationsCount = count(Helper::getEmptyStation());
		if ( $emptyStationsCount > 0 ) {
			return redirect(url('/batch_routes'))
			->withErrors(new MessageBag([
					 'error' => 'In Routes some Route Station empty<br>Please assign correct Station in route.',
			]));
		}

		$batch_routes = Helper::createAbleBatches(true);

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

				/* add order status to order table*/
				$order = Order::where('order_id', $item->order_id)
							  ->first();
				if ( $order ) {
					$order->order_status = 4;
					$order->save();
				}

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

		if ( $request->has('station') && $request->get('station') != 'all' ) {
			Session::put('searching_in_station', $request->get('station'));
		} else {
			Session::forget('searching_in_station');
		}

		$items = Item::with('lowest_order_date', 'route.stations_list', 'groupedItems')
					 ->where('batch_number', '!=', '0')
					 ->searchBatch($request->get('batch'))
					 ->searchRoute($request->get('route'))
					 ->searchStation(session('station', 'all'))
					 ->searchStatus($request->get('status'))
					 ->searchBatchCreationDateBetween($request->get('start_date'), $request->get('end_date'))
					 ->groupBy('batch_number')
					 ->latest('batch_number')
					 ->paginate(50);

		$routes = BatchRoute::where('is_deleted', 0)
							->orderBy('batch_route_name')
							->latest()
							->lists('batch_route_name', 'id')
							->prepend('Select a route', 'all');

		/*$stations = Station::where('is_deleted', 0)
						   ->orderBy('station_description', 'asc')
						   ->lists('station_description', 'id')
						   ->prepend('Select a station', 'all');*/

		// Get Station List
		$stations = Station::where('is_deleted', 0)
						   ->orderBy('station_description', 'asc')
						   ->lists('station_description', 'id')
						   ->prepend('Select a station', 'all');
		//  Get Station Name by Station Request parameter.
		$station_name = Station::find($request->get('station'));
		$current_station_by_url = $station_name['station_name'];

		$rows = [ ];
		foreach ( $items as $item ) {
			$row = [ ];
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
				$tracking_numbers_array = $item->groupedItems->lists('tracking_number')
															 ->toArray();
				$filtered_tracking_number = array_filter($tracking_numbers_array);
				if ( count($tracking_numbers_array) == count($filtered_tracking_number) ) {
					Item::where('batch_number', $item->batch_number)
						->update([
							'item_order_status' => "complete",
						]);
					$batch_status = Helper::getBatchStatus("complete");
				}

			} elseif ( in_array("not started", $batch_statuses) ) {
				$batch_status = Helper::getBatchStatus("not started");
			} else {
				// this will never reach here
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
			// Sum Total number of Item in batch
			$current_station_item_count = array_sum($items_on_station);
			$searched_station_name = null;
			if ( session('searching_in_station') ) {
				$x = Station::find(session('searching_in_station'));
				if ( $x ) {
					$searched_station_name = $x->station_name;
				}
			}
			foreach ( $items_on_station as $station_name => $total_items ) {

				if ( $searched_station_name && $station_name != $searched_station_name ) {
					continue;
				}
				$row['batch_number'] = $item->batch_number;
				$row['batch_creation_date'] = substr($item->batch_creation_date, 0, 10);
				$row['route_code'] = $item->route->batch_code;
				$row['route_name'] = $item->route->batch_route_name;
				$row['lines'] = $total_items;
				$row['current_station_name'] = $station_name;
				$row['current_station_description'] = $current_station_description;
				$row['current_station_since'] = substr($item->batch_creation_date, 0, 10);
				$row['next_station_name'] = $next_station_name;
				$row['next_station_description'] = $next_station_description;
				$row['min_order_date'] = substr($item->lowest_order_date->order_date, 0, 10);
				$row['batch_status'] = $batch_status;
				$row['current_station_item_count'] = $current_station_item_count;
				$rows[] = $row;
			}

		}
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
			return redirect(url('/stations/supervisor'))
			->withErrors(new MessageBag([
					'error' => 'Batch# '.$batch_number.' required supervisor action. ',
			]));
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

		$dept_station = DB::table('department_station')
						  ->where('station_id', Station::where('station_name', $station_name)
						  ->first()->id)
						  ->first();

		$department_id = $dept_station ? $dept_station->department_id : 0;

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
		$department_name = $department ? $department->department_name : 'NO DEPARTMENT IS SET';
		$stations = Helper::routeThroughStations($items[0]->batch_route_id, $station_name);

		#return $items;
		$count = 1;

		return view('routes.show', compact('items', 'bar_code', 'batch_number', 'rejection_reasons', 'statuses', 'route', 'stations', 'count', 'department_name', 'current_batch_station'));
	}

	// By Jewel
	public function changeBatchStation (Request $request, $batch_number)
	{

		if ( $request->has('station_name') && ( $request->ajax() ) ) {
			// Get From Station Name
			$current_station_name = $request->get('current_station_name');

			// Get To Station Name
			$toStationName = $request->get('station_name');


			// Check next shipping station exist in array then Insert itemes in Shipping table
			if ( in_array($current_station_name, Helper::$shippingStations) ) {

				return response()->json([
						'error' => false,
						'data'  => [
								'route' => url(sprintf("/batches/%s/%s", $batch_number, $current_station_name)),
						],
				], 200);
			}

			// Get all Batch on in Same Station.
			$items = Item::where('batch_number', $batch_number)
						 ->where('station_name', $current_station_name)
						 ->get();

			foreach ( $items as $item ) {
				$item->station_name = $toStationName;
				$item->save();
			}

			if ( in_array($toStationName, Helper::$shippingStations) ) {
				Helper::populateShippingData($items);
			}
			Helper::saveStationLog($items, $toStationName);

			return response()->json([
				'error' => false,
				'data'  => [
					'route' => url(sprintf("/batches/%s/%s", $batch_number, $toStationName)),
				],
			], 200);

		}
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

	/**
	 * Station change by Batch number and station next station name, Request come from batchs page by Done all
	 * @param Request $request
	 * @param string $batch_number
	 * @param string $station_name
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\$this|Ambigous <\Illuminate\Routing\Redirector, \Illuminate\Http\RedirectResponse>
	 */

	public function postBatchItems (Request $request, $batch_number, $station_name)
	{
		$action = $request->get('action');
		switch ( $action ) {
			case 'done':
				// Get All Lines by Batch numbe and station name
				$item = Item::where('batch_number', $batch_number)
							->where('station_name', $station_name)
							->first();

				// If nothing to change return to request coming page
				if ( count($item) == 0 ) {
					return redirect()->back();
				}

                // Get next station name for move next station.
				$next_station_name = Helper::getNextStationName($item->batch_route_id, $item->station_name);

				// Check next shipping station exist in array then Insert itemes in Shipping table
				if ( in_array($next_station_name, Helper::$shippingStations) ) {
					$items = Item::where('batch_number', $batch_number)
								 ->where('station_name', $station_name)
								 ->get();
					// Insert Items (Lines)
					Helper::populateShippingData($items);
				}

				// previousItems for insert instation log
				$previousItems = Item::where('batch_number', $batch_number)
									 ->where('station_name', $station_name)
									 ->get();

				if ( $next_station_name ) {
					Helper::saveStationLog($previousItems, $station_name);
				}

				// Set next station name in station_name for update
				$updates = [
						'station_name' => $next_station_name,
				];

				// Items satatus
				if ( $next_station_name == '' ) {
					$updates['item_order_status_2'] = 3;
					$updates['item_order_status'] = 'complete';
				} else {
					$updates['item_order_status'] = 'active';
				}

				// Update current stations by batch and station name
				$items = Item::where('batch_number', $batch_number)
							 ->where('station_name', $station_name)
							 ->update($updates);




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
						 		 'reached_shipping_station'  => 0,
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

		// Get list of Items from Item Table by Batch Number
		$items = Item::where('batch_number', $batch_id)
					 ->get();

		// If items not found belong to this Batch number then return to error page.
		if ( !$items ) {
			return view('errors.404');
		}

		// Get Batch Route Id from first Item, because all Items route id are same.
		$route_id = $items[0]->batch_route_id;

		// Get batch_route_id from templates table
		$route = BatchRoute::find($route_id);

		//echo "<pre>"; print_r($route); echo "</pre>";


		$template_id = $route->export_template;
		// Get templates information by template Id from templates table.
		$template = Template::with('exportable_options')
							->find($template_id);

		// Get all list of options name from template_options by options name.
		$columns = $template->exportable_options->lists('option_name')
												->toArray();

		// add graphic sku column immediately after the sku
		// if there is no sku in template, then that'll be inserted at the end of the array
		$key = array_search("sku", array_map("strtolower", $columns));
		if ( $key !== false ) {
			$place = $key + 1;
			array_splice($columns, $place, 0, [ 'graphic_sku' ]);
		}

		// change the sku to parent sku
		// change the graphic sku to sku
		foreach ( $columns as &$column ) {
			if ( strtolower($column) == "sku" ) {
				$column = "Parent SKU";
			} elseif ( strtolower($column) == "graphic_sku" ) {
				$column = "SKU";
			}
		}

		$file_path = sprintf("%s/assets/exports/batches/", public_path());
		$file_name = sprintf("%s.csv", $batch_id);
		$fully_specified_path = sprintf("%s%s", $file_path, $file_name);
		$csv = Writer::createFromFileObject(new \SplFileObject($fully_specified_path, 'w+'), 'w');
		$csv->insertOne($columns);

		set_time_limit(0);
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
					// previous line is commented after the sku became parent sku
					// and, graphic_sku became sku
					//} elseif ( str_replace(" ", "", strtolower($column->option_name)) == "parentsku" ) { // if the template value is sku
					$result = $item->item_code;
					// as the sku exists, the next column is the graphic sku
					// insert result to the row
					$row[] = $result;

					// get the graphic sku, and the result will be saving the graphic sku value
					$result = $this->getGraphicSKU($item);
					// this result will be inserted to the row array below

				} elseif ( str_replace(" ", "", strtolower($column->option_name)) == "po#" ) { // if string is po/batch number
					$result = $item->batch_number;
				} elseif ( str_replace(" ", "", strtolower($column->option_name)) == "orderdate" ) {//if the string is order date
					$result = substr($item->order->order_date, 0, 10);
				} elseif ( str_replace(" ", "", strtolower($column->option_name)) == "itemqty" ) {//if the string is item quantity = Item Qty
					$result = intval($item->item_quantity);
				} elseif ( str_replace(" ", "", strtolower($column->option_name)) == "itemdescription" ) {//if the string is item quantity = Item Qty
					$result = $item->item_description;
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
			}

			$csv->insertOne($row);
		}

		return response()->download($fully_specified_path);
	}

	private function getGraphicSKU ($item)
	{
		$child_sku = $item->child_sku;
		$option = Option::where('child_sku', $child_sku)
						->first();
		$graphic_sku = '';
		if ( !$option ) {
			return $child_sku;
		}

		return $option->graphic_sku;


		//previous code below
		$graphic_sku = $item->item_code;
		// if item has parameter option available with the store id
		// related to parameter options table
		/*if ( $item->parameter_options ) {

		}*/
		// get the item options from order
		$item_options = json_decode($item->item_option, true);
		// get the keys from that order options
		$item_option_keys = array_keys($item_options);

		$store_id = $item->store_id;
		// get the keys available as parameter
		$parameters = Parameter::where('store_id', $store_id)
							   ->lists('parameter_value')
							   ->toArray();

		$parameter_to_html_form_name = array_map(function ($element) {
			return Helper::textToHTMLFormName($element);
		}, $parameters);

		// get the common in the keys
		$options_in_common = array_intersect($parameter_to_html_form_name, $item_option_keys);
		//generate the new sku
		/*$child_sku_postfix = implode("-", array_map(function ($node) use ($item_options) {
			// replace the spaces with empty string
			// make the string lower
			// and the values from the item options
			return str_replace(" ", "", strtolower($item_options[$node]));
		}, $options_in_common));*/

		// make the new child sku
		//$child_sku = sprintf("%s-%s", $item->item_code, $child_sku_postfix);

		$parameter_options = Option::where('store_id', $store_id)
								   ->where('parameter_option', 'LIKE', sprintf("%%%s%%", $item->item_code))
								   ->get();

		// loop through the item parameter options
		foreach ( $parameter_options as $option_row ) {
			// decode the json value
			$decoded_options = json_decode($option_row->parameter_option, true);
			// if the code key exists
			//  and is equal to child sku newly generated
			// return the graphic sku

			/*if ( in_array("code", array_keys($decoded_options)) && trim($decoded_options['code']) == $child_sku ) {
				return $decoded_options['graphic_sku'];
			}*/
			$total_match = count($options_in_common);
			foreach ( $options_in_common as $common_key ) {
				$underscore_replaced_key = Helper::htmlFormNameToText($common_key);
				if ( in_array($underscore_replaced_key, array_keys($decoded_options)) && in_array($common_key, array_keys($item_options)) && $decoded_options[$underscore_replaced_key] == $item_options[$common_key] ) {
					--$total_match;
				}
			}

			if ( $total_match == 0 ) {
				return $decoded_options['graphic_sku'];
			}
		}
		// if it's not returned from the above,
		// return the default item code as graphic sku
		return '';
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
		$item->reached_shipping_station = 0;
		$item->supervisor_message = null;
		$item->save();

		return redirect()->back();
	}

	public function get_active_batch_by_sku (Request $request)
	{
		$items = Item::with('lowest_order_date', 'route.stations')
					 ->searchActiveByStation($request->get('station'))
					 ->where('batch_number', '!=', '0')
					 ->whereNull('tracking_number') // Make sure don't display whis alerady shipped
					 ->paginate(2000);

		$stations = Station::where('is_deleted', 0)
						   ->latest()
						   ->get()
						   ->lists('custom_station_name', 'station_name')
						   ->prepend('Select a station', '');
		$rows = [ ];
		$total_count = 0;

		// Jewel Update to child_sku
		foreach ( $items->groupBy('child_sku') as $sku => $sku_groups ) {
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
			->with('pagination', $items->render())
			->with('total_count', $total_count);
	}

	public function waiting_for_another_item (Request $request)
	{
		// SELECT id, order_id, COUNT( 1 ) AS counts FROM items GROUP BY order_id HAVING counts > 1
		// get the
		$rows = Item::with('order')
					->where('batch_number', '!=', 0)
					->groupBy('order_id')
					->having('row_count', '>', 1)
					->get([
						'*',
						DB::raw("COUNT(1) as row_count"),
					]);

		$items = $rows->filter(function ($row) {
			return Helper::itemsMovedToShippingTable($row->order_id);
		});

		return view('shipping.waiting_for_another_item')->with('items', $items);
	}

	public function partial_shipping (Request $request)
	{
		$item_id_array = $request->get('item_id', [ ]);
		if ( !$item_id_array ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'No item was selected',
				]);
		}

		$items = Item::with('order')
					 ->whereIn('id', $item_id_array)
					 ->get();
		if ( $items->count() ) {
			$unique_order_id = Helper::generateShippingUniqueId($items->first()->order);
			foreach ( $items as $item ) {
				Helper::insertDataIntoShipping($item, $unique_order_id);
			}

			return redirect()
				->back()
				->with('success', sprintf("New shipping is listed."));
		}

		return redirect()
			->back()
			->withErrors([
				'error' => 'No items are given to push to shipping',
			]);
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

	/**
	 * Function for change active/ not start items by statuon by limit
	 * @param Request $request
	 * @param string $sku
	 * @return \Illuminate\Http\$this|\Illuminate\Http\RedirectResponse
	 */

	public function changeStationBySKU (Request $request, $sku)
	{
		$items_to_shift = intval($request->get('item_to_shift'));
		#$station_id = $request->get('station');
		$station_id = $request->get('batch_stations');
		// Check station exist
		$station = Station::find($station_id);
		if ( !$station ) {
			return redirect()
				->back()
				->withErrors([
					'Not a valid station selected',
				]);
		}
		// Get one station Name
		$station_name = $station->station_name;

		// Get Items by condition.
		$items = Item::where('batch_number', '!=', 0)
					 ->whereNull('tracking_number')
					 ->whereNotNull('station_name')
					 ->where('station_name', '!=', '')
					 ->where('item_code', $sku)
					 ->get();

	// Insert station activity in station log table.
		foreach ( $items as $item ) {
			$station_log = new StationLog();
			$station_log->item_id = $item->id;
			$station_log->batch_number = $item->batch_number;
			$station_log->station_id = $station_id;
			$station_log->started_at = date('Y-m-d', strtotime("now"));
			$station_log->user_id = Auth::user()->id;
			$station_log->save();
		}

		// Update numbe of Station assign from items_to_shift
		Item::with('lowest_order_date', 'order')
			->where('batch_number', '!=', 0)
			->whereNull('tracking_number')
			->whereNotNull('station_name')
			->where('station_name', '!=', '')
			->where('item_code', $sku)
			->limit($items_to_shift)
			->update([
				'station_name' => $station_name,
			]);

		// After update station return to active_batch_group page
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
					$rejected_item->reached_shipping_station = 0;
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

	public function releaseBatches (Request $request)
	{
		$batch_numbers = $request->get('batch_number');

		$changes = [
			'batch_number'             => 0,
			'batch_route_id'           => null,
			'station_name'             => null,
			'item_order_status'        => null,
			'batch_creation_date'      => null,
			'tracking_number'          => null,
			'item_order_status_2'      => null,
			'previous_station'         => null,
			'item_status'              => null,
			'rejection_message'        => null,
			'rejection_reason'         => null,
			'supervisor_message'       => null,
			'reached_shipping_station' => 0,
		];

		Item::whereIn('batch_number', $batch_numbers)
			->update($changes);

		$message = sprintf("Batches: %s are released.", implode(", ", $batch_numbers));

		return redirect()
			->back()
			->with('success', $message);
	}
}
