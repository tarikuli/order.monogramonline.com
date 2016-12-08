<?php

namespace App\Http\Controllers;

use App\BatchRoute;
use App\Department;
use App\Item;
use App\Order;
use App\Note;
use App\StationLog;
use App\RejectionReason;
use App\Status;
use Illuminate\Http\Request;
use App\Station;
use App\Ship;
use App\Http\Requests\StationCreateRequest;
use App\Http\Requests\StationUpdateRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use League\Csv\Writer;
use Monogram\Helper;
use Illuminate\Support\Facades\Session;
use Route;


class StationController extends Controller {

	private $station_name = [];
	
	private $statuses = [
			'not started' => 'Not started',
			'active' => 'Active',
			'completed' => 'Completed'
	];

	public function index() {
		$count = 1;
		$stations = Station::with ( 'departments_list' )->where ( 'is_deleted', 0 )->orderBy ( 'station_name', 'asc' )->paginate ( 200 );

		$departments = Department::where ( 'is_deleted', 0 )->lists ( 'department_name', 'id' )->prepend ( 'No department assigned', '' );

		return view ( 'stations.index', compact ( 'stations', 'count', 'departments' ) );
	}

	public function create() {
		return view ( 'stations.create' );
	}

	public function store(StationCreateRequest $request) {
		$station = new Station ();
		$station->station_name = trim ( $request->get ( 'station_name' ) );
		$station->station_description = $request->get ( 'station_description' );
		$station->save ();

		session ()->flash ( 'success', 'Station is successfully added' );

		return redirect ( url ( 'stations' ) );
	}

	public function show($id) {
		$station = Station::where ( 'is_deleted', 0 )->find ( $id );

		if (! $station) {
			return view ( 'errors.404' );
		}

		return view ( 'stations.show', compact ( 'station' ) );
	}

	public function edit($id) {
		$station = Station::where ( 'is_deleted', 0 )->find ( $id );

		if (! $station) {
			return view ( 'errors.404' );
		}

		return view ( 'stations.edit', compact ( 'station' ) );
	}

	public function update(StationUpdateRequest $request, $id) {
		$station = Station::where ( 'is_deleted', 0 )->find ( $id );

		if (! $station) {
			return view ( 'errors.404' );
		}

		$station->station_name = trim ( $request->get ( 'station_name' ) );
		$station->station_description = $request->get ( 'station_description' );
		$station->station_status = $request->get ( 'station_status' );
		$station->save ();

		session ()->flash ( 'success', 'Station is successfully updated.' );
		$url = sprintf ( "%s#%s", redirect ()->getUrlGenerator ()->previous (), $station->station_name );

		return redirect ( $url );
		// return redirect(url('stations'));
	}

	public function destroy($id) {
		$station = Station::where ( 'is_deleted', 0 )->find ( $id );

		if (! $station) {
			return view ( 'errors.404' );
		}

		$station->is_deleted = 1;
		$station->save ();

		return redirect ( url ( 'stations' ) );
	}

	public function status(Request $request) {
		$station_name = $request->get ( 'station_name' );
		$stations = Station::where ( 'is_deleted', 0 )->lists ( 'station_description', 'station_name' );
		$stations->prepend ( 'All', 'all' );
		$items = [ ];
		if ($station_name && $station_name != 'all') {
			$items = Item::where ( 'station_name', $station_name )->where ( 'is_deleted', 0 )->paginate ( 50 );
		}

		return view ( 'stations.status', compact ( 'station_name', 'stations', 'items', 'request' ) );
	}

	public function my_station() {
		$station_id = session ( 'station_id' );
		$station = Station::find ( $station_id );
		if (! $station) {
			return view ( 'errors.404' );
		}
		$items = Item::where ( 'station_name', $station->station_name )->where ( 'is_deleted', 0 )->paginate ( 50 );
		$station_description = $station->station_description;

		return view ( 'stations.my_station', compact ( 'items', 'station_description' ) );
	}

	/**
	 * From Batch page change station by done or reject
	 * @param Request $request
	 * @return Ambigous <\Illuminate\View\View>
	 */
	public function change(Request $request) {

// "_token" => "mXfbcS6KiNmKpzhG9DoB6alyyGxMJC8lCGNTriW5" Export batch
// "item_id" => "53506"
// "action" => "done"
		#return $request->all();
		$action = $request->get ( 'action' );
		$item_id = $request->get ( 'item_id' );

		// $item = Item::find($item_id);
		$item = Item::with ( 'order' )
				->where ( 'id', $item_id )
				->first ();

		if (! $item) {
// 			return view ( 'errors.404' );
			return redirect ()->back ()->withErrors ( sprintf ( "No Item# %s Found by",  $item_id ) );
		}

		$batch_route_id = $item->batch_route_id;
		$current_station_name = $item->station_name;
		

		$note = new Note();

		if ($action == 'done') {

			$next_station_name = Helper::getNextStationName ( $batch_route_id, $current_station_name );
			
			if (in_array ( $next_station_name, Helper::$shippingStations )) {
				Helper::populateShippingData ( $item );
			}
			$item->station_name = $next_station_name;
			Item::where ( 'batch_number', $item->batch_number )->update ( [
					'item_order_status' => 'active'
			] );
			// $item->item_order_status = "active";

			if ($next_station_name == '') {
				$item->item_order_status_2 = 3;
				$item->item_order_status = "complete";
			} else {
// 				$station_log = new StationLog ();
// 				$station_log->item_id = $item->id;
// 				$station_log->batch_number = $item->batch_number;
// 				$station_log->station_id = Station::where ( 'station_name', $next_station_name )->first ()->id;
// 				$station_log->started_at = date ( 'Y-m-d', strtotime ( "now" ) );
// 				$station_log->user_id = Auth::user ()->id;
// 				$station_log->save ();
			}
			$item->save ();
			$note->note_text = "Click Single Done for Move to ".$next_station_name." Sation";

		} elseif ($action == 'move_to_shipping') {

			// Get All station in Route
			$route = BatchRoute::with('stations')
								->find($batch_route_id);

// echo "<pre>";
// print_r($route->stations->toArray());
// echo "</pre>";

			// Put stations in an Array
			$stations =  array_map(function ($elem) {
				return $elem['station_name'];
			}, $route->stations->toArray());

			$current_route_shp_station = null;
			foreach(Helper::$shippingStations as $key=>$val){
				if(in_array($val,$stations)){
					$current_route_shp_station[] = $val;
				}
			}

			if(!$current_route_shp_station){
				return redirect(url('batches/'.$item->batch_number.'/'.$item->station_name))
				->withErrors(new MessageBag([
						'error' => 'In Route dont have correct Shipping station.',
				]));
			}

			if (count($current_route_shp_station)>0) {
				Helper::populateShippingData ( $item );
			} else {
				return redirect(url('batches/'.$item->batch_number.'/'.$item->station_name))
				->withErrors(new MessageBag([
						'error' => 'In Route dont have correct Shipping station.',
				]));
			}

			$item->station_name = $current_route_shp_station[0];
			$item->change_date = date('Y-m-d H:i:s', strtotime('now'));
			
			Item::where ( 'batch_number', $item->batch_number )->update ( [
					'item_order_status' => 'active'
			] );

			if ($current_route_shp_station[0] == '') {
				$item->item_order_status_2 = 3;
				$item->item_order_status = "complete";
			} else {
// 				$station_log = new StationLog ();
// 				$station_log->item_id = $item->id;
// 				$station_log->batch_number = $item->batch_number;
// 				$station_log->station_id = Station::where ( 'station_name', $current_route_shp_station[0] )->first ()->id;
// 				$station_log->started_at = date ( 'Y-m-d', strtotime ( "now" ) );
// 				$station_log->user_id = Auth::user ()->id;
// 				$station_log->save ();
			}
			$item->save ();
			$note->note_text = "Click move_to_shipping for Move to ".$current_route_shp_station[0]." Sation";

		} elseif ($action == 'reject') {
			$rules = [
					'rejection_reason' => 'required|exists:rejection_reasons,id',
					'rejection_message' => 'required'
			];
			$validation = Validator::make ( $request->all (), $rules );
			if ($validation->fails ()) {
				return redirect ()->back ()->withErrors ( $validation );
			}
			$item->previous_station = $item->station_name;
			$item->station_name = Helper::getSupervisorStationName ();
			$item->change_date = date('Y-m-d H:i:s', strtotime('now'));
			$item->rejection_reason = $request->get ( 'rejection_reason' );
			$item->rejection_message = trim ( $request->get ( 'rejection_message' ) );
			$item->reached_shipping_station = 0;
			$item->save ();

			$rejection_reasons = RejectionReason::where('id', $request->get ( 'rejection_reason' ))->get();
			$note->note_text = "Click reject for Move to ".Helper::getSupervisorStationName ()." Sation, Reject reason: ".$rejection_reasons->first()->rejection_message.", Reject Message: ".$item->rejection_message;
		}elseif ($action == 'back_to_qc') {

			// Get All station in Route
			$route = BatchRoute::with('stations')
			->find($batch_route_id);

			// Put stations in an Array
			$stations =  array_map(function ($elem) {
				return $elem['station_name'];
			}, $route->stations->toArray());

			// Find Shipping Station from Route
			$current_route_shp_station = null;
			foreach(Helper::$shippingStations as $key=>$val){
				if(in_array($val,$stations)){
					$current_route_shp_station[] = $val;
				}
			}

			if(!$current_route_shp_station){
				return redirect(url('batches/'.$item->batch_number.'/'.$item->station_name))
				->withErrors(new MessageBag([
						'error' => 'In Route dont have correct Shipping station.',
				]));
			}

			// Check QDC station exist in current Route and Shipping station > 0
			if (count($current_route_shp_station)>0 && ($item->tracking_number == null)) {
// 				$qdc_station = substr($current_route_shp_station[0], 0, 1)."-QCD";
				$qdc_station = explode("-",$current_route_shp_station[0]);
				$qdc_station = $qdc_station[0]."-QCD";

				if (in_array($qdc_station, $stations)) {

					$items = Item::where('id', $item->id)
								 ->where('is_deleted',0)
								 ->whereNull('tracking_number')
					->update([
							'station_name'      => $qdc_station,
							'change_date' => date('Y-m-d H:i:s', strtotime('now')),
							'previous_station'  => $current_route_shp_station[0],
							'reached_shipping_station'  => 0,
							'item_order_status_2' => 2,
							'item_order_status' => "active",
					]);

					Ship::where('item_id', $item_id)
						 ->whereNull('tracking_number')
						 ->delete();

//  					$station_log = new StationLog ();
//  					$station_log->item_id = $item->id;
//  					$station_log->batch_number = $item->batch_number;
//  					$station_log->station_id = Station::where ( 'station_name', $current_route_shp_station[0] )->first ()->id;
//  					$station_log->started_at = date ( 'Y-m-d', strtotime ( "now" ) );
//  					$station_log->user_id = Auth::user ()->id;
//  					$station_log->save ();

 					$note->note_text = "Click back_to_qc for Move to ".$qdc_station." Sation";

 					return redirect ( url ( 'batches/'.$item->batch_number.'/'.$qdc_station ) );
				}else{
					return redirect(url('batches/'.$item->batch_number.'/'.$item->station_name))
					->withErrors(new MessageBag([
							'error' => $qdc_station.' Station Not Found.',
					]));
				}
			} else {
				return redirect(url('batches/'.$item->batch_number.'/'.$item->station_name))
				->withErrors(new MessageBag([
						'error' => 'In Route dont have correct Shipping station and QCD Station.',
				]));
			}
		}

		$batch_item_count = Item::where ( 'batch_route_id', $batch_route_id )
								->where ( 'station_name', $current_station_name )
								->where ( 'is_deleted', 0 )
								->count ();

		// Add note history by order id
		$note->order_id = $item->order_id;
		$note->user_id = Auth::user()->id;
		$note->save();
		// Add note history by order id

		if ($request->has ( 'return_to' ) && $request->get ( 'return_to' ) == "back") {
			return redirect ()->back ();
		}

		if ($batch_item_count) {
// 			return redirect ()->back ();
			return redirect ()->back ()->with('success', "Success");
		} else {
			return redirect ( url ( 'items/grouped' ) );
		}
	}


	public function supervisor(Request $request) {

		$routes = BatchRoute::where ( 'is_deleted', 0 )
							->orderBy ( 'batch_route_name' )
							->latest ()
							->lists ( 'batch_route_name', 'id' )
							->prepend ( 'Select a route', 'all' );

		$stations = Station::where ( 'is_deleted', 0 )
							->whereNotIn( 'station_name', Helper::$shippingStations)
							->orderBy ( 'station_name', 'asc' )
							->lists ( 'station_description', 'id' )
							->prepend ( 'Select a station', 'all' );

		// $statuses = (new Collection($this->statuses))->prepend('Select status', 'all');
		$statuses = (new Collection ( Helper::getBatchStatusList () ))->prepend ( 'Select status', 'all' );
		$item_statuses = Status::where ( 'is_deleted', 0 )->lists ( 'status_name', 'id' );

		$items = null;
		if (count ( $request->all () )) {
			$items = Item::with ( 'route.stations_list', 'order' )
					->searchBatch ( $request->get ( 'batch' ) )
					->searchRoute ( $request->get ( 'route' ) )
					->searchStatus ( $request->get ( 'status' ) )
					->where ( 'station_name', Helper::getSupervisorStationName () )
// 					->searchStation ( $request->get ( 'station' ) )
					->searchOptionText ( $request->get ( 'option_text' ) )
					->searchOrderIds ( $request->get ( 'order_id' ) )
					->where ( 'is_deleted', 0 )
					->paginate ( 25 );
		} else {
			$items = Item::with ( 'route.stations_list', 'order' )
					->where ( 'is_deleted', 0 )
// 					->whereNotNull ( 'batch_number' )
					->where ( 'station_name', Helper::getSupervisorStationName () )
					->paginate ( 25 );
		}

		return view ( 'stations.supervisor', compact ( 'items', 'request', 'routes', 'stations', 'statuses', 'item_statuses' ) );
	}


	public function on_change_apply(Request $request) {
		$item_id = $request->get ( 'item_id' );
		$item = Item::find ( $item_id );
		if (! $item) {
			return redirect ()->back ();
		}
		if ($request->has ( 'station_name' )) {
			$station_name = $request->get ( 'station_name' );

			// Jewel from Supervisor station Implement validation move to Shipping station.
			if (in_array ( $station_name, Helper::$shippingStations )) {
				return redirect()
				->back()
				->withErrors([
						'error' => 'You can not Move in Shipping Station',
				]);
			}

			$item->station_name = $station_name;
			$item->previous_station = $item->station_name;
			$item->change_date = date('Y-m-d H:i:s', strtotime('now'));
			$supervisor_message = trim ( $request->get ( 'supervisor_message' ) );
			$item->supervisor_message = ! empty ( $supervisor_message ) ? $supervisor_message : null;
			$item->rejection_message = null;
			$item->rejection_reason = null;
			$item->save ();

			// Add note history by order id
			$note = new Note();
			$note->note_text = "Supervisor move to ".$station_name." station and message is: ".$item->supervisor_message;
			$note->order_id = $item->order_id;
			$note->user_id = Auth::user()->id;
			$note->save();


		} elseif ($request->has ( 'order_status' )) {
			$order_status = $request->get ( 'order_status' );
			$order = Order::where ( 'order_id', $item->order_id )->first ();
			if ($order) {
				$order->order_status = $order_status;
				$order->save ();
			}
		} elseif ($request->has ( 'item_order_status_2' )) {
			$item_order_status_2 = $request->get ( 'item_order_status_2' );
			$item->item_order_status_2 = $item_order_status_2;
			$item->save ();
		}

		return redirect ()->back ();
	}
	/**
	 * Show Station Summery
	 * @return Ambigous <\Illuminate\View\View, \Illuminate\Contracts\View\Factory>
	 */
	public function summary(Request $request) {

		if(!$request->start_date){
			$start_date = "2016-06-01";
		}else{
			$start_date = $request->start_date;
		}
		
		if(!$request->end_date){
			$end_date = "2020-12-31";
		}else {
			$end_date = $request->end_date;
		}
		
		$items = Item::rightJoin('orders', 'items.order_id', '=', 'orders.order_id')
							->where('orders.is_deleted', 0)
							->whereNotIn('orders.order_status', Helper::$orderStatus)
							->where('orders.order_date', '>=', sprintf("%s 00:00:00", $start_date))
							->where('orders.order_date', '<=', sprintf("%s 23:59:59", $end_date))	
							->where('items.is_deleted', 0)							
							->where('items.batch_number', '!=', '0')
							->whereNull('items.tracking_number')
							->groupBy ( 'items.station_name' )
							->orderBy('items.station_name')
// 							->take(5)
							->lists('items.station_name');
// 							->get ();
		
		
// dd($items);		

		$summaries = [ ];
		$total_lines = 0;
		$total_items = 0;

		set_time_limit(0);

		$stations = Station::all('id', 'station_name', 'station_description' )->toArray();
		$stations_arrays = [];
		foreach ( $stations as $stations_array ) {
			$stations_arrays[$stations_array['station_name']] = $stations_array;
		}
		
		foreach ( $items as $station_name ) {
			$summary = [ ];

// 			$station_name = $item->station_name;
// 			$station_name = $item;
			#$lines_count = Helper::getItemsByStationAndDate($station_name, $request->get('cutoff_date'));
			$lines_count = Helper::getItemsByStationAndDate($station_name, $start_date, $end_date);
			$order_ids = array_unique($lines_count->lists ( 'order_id' )->toArray ());
			$items_count = array_sum($lines_count->lists ( 'item_quantity' )->toArray ());


			if (count($order_ids) > 0) {
				#$earliest_batch_creation_date = Helper::getEarliest($lines_count->lists ( 'batch_creation_date' )->toArray ());
				$earliest_batch_creation_date = Helper::getEarliest($lines_count->lists ( 'change_date' )->toArray ());
				$earliest_order_date = Helper::getEarliest($lines_count->lists ( 'order_date' )->toArray ());

				$summary ['station_id'] = $stations_arrays[$station_name]['id']; //$station->id;
				$summary ['station_description'] = $stations_arrays[$station_name]['station_description']." ( ".date('H:i:s', strtotime('now'))." )"; //$station->station_description;
				$summary ['station_name'] = $station_name;
				
				
				$summary ['lines_count'] = count($order_ids);
				$summary ['items_count'] = $items_count;
				$summary ['earliest_order_date'] = substr ( $earliest_order_date, 0, 10 );
				$summary ['earliest_batch_creation_date'] = substr ( $earliest_batch_creation_date, 0, 10 );
// 				$summary ['link'] = url ( sprintf ( "/items/active_batch_group?station=%s&cutoff_date=%s", $station_name, $request->get('cutoff_date') ) );
				$summary ['link'] = url ( sprintf ( "/items/active_batch_group?station=%s&start_date=%s&end_date=%s", $station_name,  $start_date, $end_date ) );

				$summaries [] = $summary;
				$total_lines += count($order_ids);
				$total_items += $items_count;
			}
		}

		return view ( 'stations.summary', compact ( 'summaries', 'total_lines', 'total_items', 'start_date', 'end_date' ))->withRequest($request);
	}
	
	
	public function itemshippingstationchange(Request $request) {
	
		return view ( 'items.item_shipping_station_change' );
	}
	
	public function postItemShippingStationchange(Request $request) {
		$errors = [];
		
		$unique_order_ids = $request->get ( 'unique_order_id' );
		$order_id = Helper::getOrderNumber($unique_order_ids);
		
		if($order_id){
			
			$items = Item::with ( 'route.stations_list' ) 							
							->where('is_deleted', 0) 							
							->whereNull('tracking_number') 							
							->where ('order_id', $order_id ) 
							->where('batch_number', '!=', '0')
							->get ();
			
			foreach ($items as $item){
				$stations_in_route_ids = $item->route->stations_list->lists ( 'station_name' )->toArray ();
	
				if(count($stations_in_route_ids)>0){
					$common_shipping_station = array_values(array_intersect(Helper::$shippingStations,$stations_in_route_ids));
				
					if(count($common_shipping_station)>0){
						if(!strpos($item->station_name, '-SHP')){
							$item->previous_station = $item->station_name;
							$item->station_name = $common_shipping_station[0];
							$item->change_date = date('Y-m-d H:i:s', strtotime('now'));
							$item->save ();
							Helper::populateShippingData ( $item );
							Helper::histort("Item#".$item->id." from ".$item->station_name." -> ".$common_shipping_station[0], $item->order_id);
						}else{
							$errors[] = sprintf ("Already in Shipping Station Item# ".$item->id." Batch# ".$item->batch_number." and Route ".$item->route->batch_code." -> ".$item->route->batch_route_name);
						}
					}else{
						$errors[] = sprintf ("Batch# ".$item->batch_number." and Route ".$item->route->batch_route_name." -> ".$item->route->batch_route_name." Need SHP Station.");
					}
				}else{
						$errors[] = sprintf ("Batch# ".$item->batch_number." and Route ".$item->route->batch_route_name." -> ".$item->route->batch_route_name." Need SHP Station.");
				}
			}
			
			if (count ( $errors ) > 0) {
				return redirect ()->back ()->withErrors ( $errors );
			}
			
			return redirect ()->back ()->with('success', "Success move to Shipping Station Order# ".$order_id);
		}
		
		return redirect ()->back ()->withErrors ( "No Order#".$unique_order_ids." found, Scan correctly." );
	}
	
	
	
	public function getItemStationChange(Request $request) {
	
		return view ( 'items.item_station_change' );
	}
	
	public function postItemStationChange(Request $request) {
		$message = [];
		$errors = [];
		$success_wav = false;
		$order_items_info = [];
		$inWaitingStation =0;
// 		return $request->all();
		
		// station exists
		// divide the given batches
		$posted_item_id = $request->get ( 'item_id' );
		// remove newlines and spaces
		$posted_item_id = trim ( preg_replace ( '/\s+/', ',', $posted_item_id ) );
		
		$item_ids = array_map ( function ($batch) {
			$integer_value_of_batch_number = intval ( $batch );
			// safety check
			// if the integer value of a batch number is 0,
			// table having 0 as batch number has different meaning
			// thus returns -1, table will never have any value -1;
			return $integer_value_of_batch_number ?  : - 1;
		}, explode ( ",", $posted_item_id ) );
		
		
		foreach ($item_ids as $item_id){

			// reached_shipping_station
			// Get all Items in a Batch
			$item = Item::where('is_deleted', 0)
							->where('batch_number', '!=', '0')
							->whereNull('tracking_number')
							->where ( 'id', $item_id )
							->first ();
// 							->get ();

			// If Item exist change_date
			if(count($item) > 0){
				
				
				Item::where ( 'id', $item_id )
				->update([
					'station_name' => "WAP",
					'previous_station' => $item->station_name,
					'reached_shipping_station' => 0,
					'change_date' => date('Y-m-d H:i:s', strtotime('now'))
				]);
					
				if (in_array ( $item->station_name, Helper::$shippingStations )) {
					Ship::where('order_number', $item->order_id)
					->whereNull('tracking_number')
					->delete();
				}
				
				
// 				Helper::jewelDebug("Wap: Used Move waiting Station ItemID #".$item_id." ".$item->order_id);
				Helper::histort("Used Move waiting Station ItemID #".$item_id,$item->order_id);
				
				// Count again which are not ship yet and now in WAP station
				$items = Item::where('is_deleted', 0)
								->where('batch_number', '!=', '0')
								->whereNull('tracking_number')
								->where ( 'order_id', $item->order_id )
// 								->where ( 'station_name', 'WAP')
								->get ();
				
				$inWaitingStation =0;
				foreach($items as $itemm){
					if ( trim($itemm->station_name) == "WAP" ) {
						$inWaitingStation ++;
					}
					$order_items_info[]= "Order# ".$itemm->order_id." Batch# ".$itemm->batch_number." Item# ".$itemm->id." Current Station :&nbsp;&nbsp;".$itemm->station_name."&nbsp;&nbsp; at :".$itemm->change_date;
				}
// dd($inWaitingStation, $items->count() );				
				if($items->count() == ($inWaitingStation)){
					$success_wav = true;
// 					Helper::jewelDebug("WAP: Order# ".$item->order_id." Total: ".$inWaitingStation." Item now ready for Ship");
					
// 					return view('items.item_station_change', compact('success_wav', 'order_items_info'))
// 					->with('success', "Success Order# ".$item->order_id." Total: ".$inWaitingStation." Item now ready for Ship");
				
					$request->session()->flash('success', "Success Order# ".$item->order_id." Total: ".$inWaitingStation." Item now ready for Ship");
					return view('items.item_station_change',  compact('success_wav', 'order_items_info'));
				}
// 				$items = Item::with ( 'route.stations_list' )
// 								->where('is_deleted', 0)
// 								->where('batch_number', '!=', '0')
// 								->whereNull('tracking_number')
// 								->where ( 'order_id', $item->order_id )
// 								->get ();
				
// 				$inShipStation =0;
// 				$notInShipStation =0;
// 				$inWaitingStation =0;
// 				foreach($items as $itemm){
// 					if ( in_array($itemm->station_name, Helper::$shippingStations) ) {
// 						$inShipStation ++;
// 					}elseif ( trim($itemm->station_name) == "WAP" ) {
// 						$inWaitingStation ++;
// 					}else{
// 						$notInShipStation ++;
// 					}
					
// 				}

// 				// If Orher Item All ready in Shipping Station except this Line Item
// 				if($items->count() == ($inShipStation)){
// 					return redirect ()->back ()->with('success', sprintf ( "Item# %s Order# %s All Item Already in Shiping Station put in shipping buskate.",  $item_id, $itemm->order_id ));
// 				}else
// 				if($items->count() == ($inWaitingStation)){
// 					Helper::populateShippingData ($items);
// 					Helper::jewelDebug("Wap: Order# ".$item->order_id ." Total: ".$items->count()." Items and in WAP ".$inWaitingStation." items in waiting station so all move to Shipping Station");
// 				}elseif($items->count() == ($inWaitingStation+1)){
// 					Helper::populateShippingData ($items);
// 					Helper::jewelDebug("Wap: Order# ".$item->order_id ." Total: ".$items->count()." Items and in WAP ".$inWaitingStation." items Plus ".$item_id." in waiting station so all move to Shipping Station");
// 				}elseif($items->count() == ($inShipStation+1)){
// 					Helper::setShippingFlag ($item);
// 					Helper::jewelDebug("Wap: Order# ".$item->order_id ." Total: ".$items->count()." Items and in Shipping ".$inShipStation." items Plus ".$item_id." in waiting station so all move to Shipping Station");
// // 					$stationsArray = [];
					
// // 					foreach($itemm->route->stations_list as $stations){
// // 						$stationsArray[] = $stations->station_name;
// // 					}
					
// // 					$commonShipStatio = array_values(array_intersect(Helper::$shippingStations, $stationsArray));
					
// // 					// Update Shipping Station in Item Table
// // 					if(($commonShipStatio[0])){
// // 					 	Item::where ( 'id', $item_id )
// // 								->update([
// // 									'station_name' => $commonShipStatio[0],
// // 						]);
// // 					}
					
// // 					// get the item id from the shipping table
// // 					$items_exist_in_shipping = Ship::where('order_number', $itemm->order_id)
// // 													->lists('unique_order_id');
// // 					Helper::jewelDebug("**** ".$itemm->station_name." ------- ".$itemm->id."---Total: ".$items->count()."---	".$inShipStation." -- ".$notInShipStation);
// 				}else{
// 				 	Item::where ( 'id', $item_id )
// 							->update([
// 								'station_name' => "WAP",
// 								'previous_station' => $item->station_name,
// 								'reached_shipping_station' => 0
// 					]);
					
// 					if (in_array ( $item->station_name, Helper::$shippingStations )) {
// 						Ship::where('item_id', $item_id)
// 						->whereNull('tracking_number')
// 						->delete();
// 					}
// 					Helper::jewelDebug("Wap: Used Move waiting Station ItemID #".$item_id." ".$item->order_id);
// 					Helper::histort("Used Move waiting Station ItemID #".$item_id,$item->order_id);
// // 					Helper::jewelDebug($itemm->station_name." ------- ".$item_id."---Total: ".$items->count()."---	".$inShipStation." -- ".$notInShipStation);
				
			}else{
				$errors[] = sprintf ( "Already Shipped Item# %s Not moved to Waiting Station",  $item_id );
				if (count ( $errors ) > 0) {
					return redirect ()->back ()->withErrors ( $errors );
				}
			}
// 			Helper::jewelDebug( "WAP: move to WAP Station Item# ".$item_id);
// 			return redirect ()->back ()->with('success', "Success move to WAP Station Item# ".$item_id);
// 			$request->session()->flash('success', "Success move to WAP Station Item# ".$item_id." Out of ".$items->count()." waiting ". $inWaitingStation+1);
			$request->session()->flash('success', "Success move to WAP Station Item# ".$item_id." Out of ".$items->count()." in waiting station ". $inWaitingStation);
			return view('items.item_station_change',  compact('success_wav', 'order_items_info'));
// 			->with('success', "Success move to WAP Station Item# ".$item_id);

		}

	}
	
	
	public function getSingleChange(Request $request) {
		
		$stations = Station::where ( 'is_deleted', 0 )
							->whereNotIn( 'station_name', Helper::$shippingStations)
							->get ()
							->lists ( 'custom_station_name', 'station_name' )
							->prepend ( 'Select a station', 0 );
	
		// 	return $stations;
		return view ( 'items.single_change' )->with ( 'stations', $stations );
	}
	
	public function moveAwaySingleChange(Request $request) {

		if ( $request->has('station') ) {
			Session::put('station', $request->get('station'));
			$posted_station = trim ( $request->get ( 'station' ) );
		}
	
		$station = Station::where ( 'is_deleted', 0 )
		->where ( 'station_name', '=', trim ( $request->get ( 'station' ) ) )
		->first ();
	
		if (! $station) {
			return redirect ()->back ()->withInput ()->withErrors ( [
					'error' => 'Selected station is not valid'
			] );
		}
	
		// station exists
		// divide the given batches
		$posted_batches = $request->get ( 'batches' );
		// remove newlines and spaces
		$posted_batches = trim ( preg_replace ( '/\s+/', ',', $posted_batches ) );
		$batches = array_map ( function ($batch) {
			$integer_value_of_batch_number = intval ( $batch );
			// safety check
			// if the integer value of a batch number is 0,
			// table having 0 as batch number has different meaning
			// thus returns -1, table will never have any value -1;
			return $integer_value_of_batch_number ?  : - 1;
		}, explode ( ",", $posted_batches ) );
		// Helper::jewelDebug($batches);
		$errors = [ ];
		// search the items
		// if any item from a batch is splitted, then cannot be moved
		// if the batch route don't have that station, then, cannot be moved.
		$batches = array_filter ( $batches, function ($batch) use(&$errors, $station) {
			if ($batch == - 1) {
				return false;
			}
	
			// Get all Items in a Batch
			$items = Item::with ( 'route.stations_list' )
			->where('is_deleted', 0)
			->whereNull('tracking_number')
			->where ( 'batch_number', $batch )
			->get ();
	
			$count = $items->groupBy ( 'station_name' )->count ();
	
			if ($count != 1) {
				$errors [] = sprintf ( "Batch %s either has more than one stations assigned or not a valid batch number", $batch );
	
				return false;
			}
	
			$first_item = $items->first ();
			$stations_in_route_ids = $first_item->route->stations_list->lists ( 'station_id' )->toArray ();
				
			if (! in_array ( $station->id, $stations_in_route_ids )) {
				$errors [] = sprintf ( "Batch %s Route: %s doesn't have \"%s (%s) \" station in its route.", $batch, $first_item->route->batch_route_name, $station->station_description, $station->station_name );
	
				return false;
			}
	
			return true;
		} );
	
			$items = Item::with ( 'order' )
			->whereIn ( 'batch_number', $batches )
			->where('is_deleted', 0)
			->whereNull('tracking_number')
			->get ();
// dd($request->all(), $batches, $items, $posted_station);	
			if ($items->count () == 0) {
				return redirect ()->back ()->withInput ()->withErrors ( $errors );
			}

			$changed = $this->apply_station_change ( $items, $posted_station );
	
			// redirect with errors if any error found
			if (count ( $errors )) {
				return redirect ()->back ()->withErrors ( $errors );
			}
	
			return redirect(url('stations/single'))
						->with ( 'success', sprintf ( "Batch# %s Total %d items moved to station: %s", $posted_batches, $changed, $posted_station ) );
	
	}
	
	public function postSingleChange(Request $request) {
		
		if ( $request->has('station') ) {
			Session::put('station', $request->get('station'));
			$posted_station = trim ( $request->get ( 'station' ) );
		}
		
		$station = Station::where ( 'is_deleted', 0 )
							->where ( 'station_name', '=', trim ( $request->get ( 'station' ) ) )
							->first ();
		
		if (! $station) {
			return redirect ()->back ()->withInput ()->withErrors ( [
					'error' => 'Selected station is not valid'
			] );
		}
		
		// station exists
		// divide the given batches
		$posted_batches = $request->get ( 'batches' );
		// remove newlines and spaces
		$posted_batches = trim ( preg_replace ( '/\s+/', ',', $posted_batches ) );
		
		$batches = array_map ( function ($batch) {
			$integer_value_of_batch_number = intval ( $batch );
			// safety check
			// if the integer value of a batch number is 0,
			// table having 0 as batch number has different meaning
			// thus returns -1, table will never have any value -1;
			return $integer_value_of_batch_number ?  : - 1;
		}, explode ( ",", $posted_batches ) );
// Helper::jewelDebug($batches);		
		$errors = [ ];
		// search the items
		// if any item from a batch is splitted, then cannot be moved
		// if the batch route don't have that station, then, cannot be moved.
		$batches = array_filter ( $batches, function ($batch) use(&$errors, $station) {
			if ($batch == - 1) {
				return false;
			}

			// Get all Items in a Batch
			$items = Item::with ( 'route.stations_list' )
							->where('is_deleted', 0)
							->whereNull('tracking_number')
							->where ( 'batch_number', $batch )
							->get ();
	
			$count = $items->groupBy ( 'station_name' )->count ();
		
			if ($count != 1) {
				$errors [] = sprintf ( "Batch %s either has more than one stations assigned or not a valid batch number", $batch );
		
				return false;
			}
		
			$first_item = $items->first ();
		
			$stations_in_route_ids = $first_item->route->stations_list->lists ( 'station_id' )->toArray ();
			$stations_code_route = $first_item->route->stations_list->lists ( 'station_name' )->toArray ();
			if(array_search($station->station_name, $stations_code_route) <= (array_search($first_item->station_name, $stations_code_route))){
				// $errors [] = sprintf ( "You moveing back station? You can't moving from here.<br>Batch %s Route: %s doesn't have \"%s (%s) \" station from %s Station in its route.", $batch, $first_item->route->batch_route_name, $station->station_description, $station->station_name, $first_item->station_name );
				$errors [] = sprintf ( "You are moving back from %s Station to %s Station!!!
						<br>Click 
						<a href = ".url(sprintf('stations/moveawaysingle?batches=%d&station=%s&move=1',$batch, $station->station_name)).">here</a> 
						for move anyaway.
						<br>Batch# %d Last moved at %s", $station->station_name, $first_item->station_name, $batch, $first_item->change_date );
				return false;
			}
// dd($items, $stations_code_route, $station->station_name, $first_item->station_name);			
			
			
			if (! in_array ( $station->id, $stations_in_route_ids )) {
				$errors [] = sprintf ( "Batch %s Route: %s doesn't have \"%s (%s) \" station in its route.", $batch, $first_item->route->batch_route_name, $station->station_description, $station->station_name );
		
				return false;
			}
		
			return true;
		} );
	
		$items = Item::with ( 'order' )
			->whereIn ( 'batch_number', $batches )
			->where('is_deleted', 0)
			->whereNull('tracking_number')
			->get ();
		
		if ($items->count () == 0) {
			return redirect ()->back ()->withInput ()->withErrors ( $errors );
		}
		
		$changed = $this->apply_station_change ( $items, $posted_station );
	
		// redirect with errors if any error found
		if (count ( $errors )) {
			return redirect ()->back ()->withErrors ( $errors );
		}
	
		return redirect ()->back ()->with ( 'success', sprintf ( "Batch# %s Total %d items moved to station: %s", $posted_batches, $changed, $posted_station ) );
		
	}
	
	public function getBulkChange() {
		
		$stations = Station::where ( 'is_deleted', 0 )
							->whereNotIn( 'station_name', Helper::$shippingStations)
							->get ()
							->lists ( 'custom_station_name', 'station_name' )
							->prepend ( 'Select a station', 0 );

// 		return $stations;
		return view ( 'items.bulk_change' )->with ( 'stations', $stations );
	}

	public function postBulkChange(Request $request) {
// return $request->all();		
		$posted_station = trim ( $request->get ( 'station' ) );
		// check if station exists
		$station = Station::where ( 'is_deleted', 0 )->where ( 'station_name', '=', $posted_station )->first ();

		if (! $station) {
			return redirect ()->back ()->withInput ()->withErrors ( [
					'error' => 'Selected station is not valid'
			] );
		}

		// station exists
		// divide the given batches
		$posted_batches = $request->get ( 'batches' );
		// remove newlines and spaces
		$posted_batches = trim ( preg_replace ( '/\s+/', ',', $posted_batches ) );

		$batches = array_map ( function ($batch) {
			$integer_value_of_batch_number = intval ( $batch );
			// safety check
			// if the integer value of a batch number is 0,
			// table having 0 as batch number has different meaning
			// thus returns -1, table will never have any value -1;
			return $integer_value_of_batch_number ?  : - 1;
		}, explode ( ",", $posted_batches ) );
		$errors = [ ];
		// search the items
		// if any item from a batch is splitted, then cannot be moved
		// if the batch route don't have that station, then, cannot be moved.
		$batches = array_filter ( $batches, function ($batch) use(&$errors, $station) {
			if ($batch == - 1) {
				return false;
			}

			// Get all Items in a Batch
			$items = Item::with ( 'route.stations_list' )
							->where('is_deleted', 0)
							->whereNull('tracking_number')
							->where ( 'batch_number', $batch )
							->get ();

			$count = $items->groupBy ( 'station_name' )->count ();

			if ($count != 1) {
				$errors [] = sprintf ( "Batch %s either has more than one stations assigned or not a valid batch number", $batch );

				return false;
			}

			$first_item = $items->first ();

			$stations_in_route_ids = $first_item->route->stations_list->lists ( 'station_id' )->toArray ();

			if (! in_array ( $station->id, $stations_in_route_ids )) {
				$errors [] = sprintf ( "Batch %s Route: %s doesn't have \"%s (%s) \" station in its route.", $batch, $first_item->route->batch_route_name, $station->station_description, $station->station_name );

				return false;
			}

			return true;
		} );

		$items = Item::with ( 'order' )
						->where('is_deleted', 0)
						->whereNull('tracking_number')
						->whereIn ( 'batch_number', $batches )
						->get ();
		
		if ($items->count () == 0) {
			return redirect ()->back ()->withInput ()->withErrors ( $errors );
		}
		$changed = $this->apply_station_change ( $items, $posted_station );

		// redirect with errors if any error found
		if (count ( $errors )) {
			return redirect ()->back ()->withErrors ( $errors );
		}

		return redirect ()->back ()->with ( 'success', sprintf ( "Total of: %d items moved to station: %s", $changed, $posted_station ) );
	}

	private function apply_station_change($items, $station_name) {
		foreach ( $items as $item ) {
			$item->previous_station = $item->station_name;
			$item->station_name = $station_name;
			$item->change_date = date('Y-m-d H:i:s', strtotime('now'));
			$item->save ();
			if (in_array ( $station_name, Helper::$shippingStations )) {
				Helper::populateShippingData ( $item );
			}
			Helper::histort("Item#".$item->id." from ".$item->station_name." -> ".$station_name, $item->order_id);
		}

		return $items->count ();
	}

	public function getExportStationLog(Request $request) {
		$start = trim ( $request->get ( 'start_date' ) );
		$output = null;

		if (! empty ( $start )) {
			$month_starts = "01";
			$month_ends = date ( 't', strtotime ( $request->get ( 'start_date' ) ) );

			$first_day_of_the_month = sprintf ( "%s-%s", $start, $month_starts );
			$end_day_of_the_month = sprintf ( "%s-%s", $start, $month_ends );

			$station_logs = StationLog::with ( 'user', 'station' )->searchWithinMonthGroupLog ( $first_day_of_the_month, $end_day_of_the_month )->orderBy ( 'started_at' )->get ( [
					'started_at',
					'station_id',
					'user_id',
					DB::raw ( 'SUM(1) as item_count' )
			] );
			$dates = $this->range_date ( $first_day_of_the_month, $end_day_of_the_month );
			$header = array_merge ( [
					'station'
			],
					// uncomment user if user is required
					// 'user',
					$dates, [
							'total'
					] );
			$output [] = $header;

			foreach ( $station_logs as $log ) {
				$station_name = $log->station->station_name;
				// uncomment user if user is required
				// $user = $log->user->username;
				$row = [ ];
				$row [] = $station_name;
				// uncomment user if user is required
				// $row[] = $user;
				$month_total_task_per_station = 0;
				foreach ( $dates as $date ) {
					$per_day = 0;
					if ($date == $log->started_at) {
						$per_day = $log->item_count;
					}
					$row [] = $per_day;
					$month_total_task_per_station += $per_day;
				}
				$row [] = $month_total_task_per_station;
				$output [] = $row;
			}
		}

		return view ( 'stations.export_station' )->with ( 'request', $request )->with ( 'output', $output );
	}

	public function postExportStationLog(Request $request) {
		// grab the month
		$start = trim ( $request->get ( 'start_date' ) );
		// $end = trim($request->get('end_date'));

		if (empty ( $start )) {
			return redirect ()->back ()->withInput ()->withErrors ( [
					'error' => 'Date is not selected'
			] );
		}
		$month_starts = "01";
		$month_ends = date ( 't', strtotime ( $request->get ( 'start_date' ) ) );

		$first_day_of_the_month = sprintf ( "%s-%s", $start, $month_starts );
		$end_day_of_the_month = sprintf ( "%s-%s", $start, $month_ends );

		/*
		 * DB QUERY: SELECT station_id, started_at, SUM( 1 ) FROM `station_logs` WHERE started_at >= '2016-03-01' AND started_at <= '2016-03-31' GROUP BY station_id, started_at ORDER BY station_id
		 * SELECT station_id, started_at, user_id, sum(1) FROM `station_logs` where started_at >= '2016-03-01' and started_at <= '2016-03-31' group by station_id, user_id, started_at order by started_at
		 */

		$station_logs = StationLog::with ( 'user', 'station' )->searchWithinMonthGroupLog ( $first_day_of_the_month, $end_day_of_the_month )->orderBy ( 'started_at' )->get ( [
				'started_at',
				'station_id',
				'user_id',
				DB::raw ( 'SUM(1) as item_count' )
		] );
		$dates = $this->range_date ( $first_day_of_the_month, $end_day_of_the_month );
		$header = array_merge ( [
				'station'
		],
				// uncomment user if user is required
				// 'user',
				$dates, [
						'total'
				] );

		/*
		 * File write operation
		 */

		$file_path = sprintf ( "%s/assets/exports/station_log/", public_path () );
		$file_name = sprintf ( "station_log-%s-%s.csv", date ( "y-m-d", strtotime ( 'now' ) ), str_random ( 5 ) );
		$fully_specified_path = sprintf ( "%s%s", $file_path, $file_name );
		$csv = Writer::createFromFileObject ( new \SplFileObject ( $fully_specified_path, 'w+' ), 'w' );
		$csv->insertOne ( $header );
		foreach ( $station_logs as $log ) {
			$station_name = $log->station->station_name;
			// uncomment user if user is required
			// $user = $log->user->username;
			$row = [ ];
			$row [] = $station_name;
			// uncomment user if user is required
			// $row[] = $user;
			$month_total_task_per_station = 0;
			foreach ( $dates as $date ) {
				$per_day = 0;
				if ($date == $log->started_at) {
					$per_day = $log->item_count;
				}
				$row [] = $per_day;
				$month_total_task_per_station += $per_day;
			}
			$row [] = $month_total_task_per_station;
			$csv->insertOne ( $row );
		}

		return response ()->download ( $fully_specified_path );
	}

	private function range_date($first, $last) {
		$arr = array ();
		$now = strtotime ( $first );
		$last = strtotime ( $last );

		while ( $now <= $last ) {
			$arr [] = date ( 'Y-m-d', $now );
			$now = strtotime ( '+1 day', $now );
		}

		return $arr;
	}
	
	public function getBackPrevious(Request $request) {
	
		return view ( 'items.back_prev_station' );
	}
	
	public function postBackPrevious(Request $request) {
		
		
	
		$posted_item_id = $request->get ( 'item_id' );
		// remove newlines and spaces
		$posted_item_id = trim ( preg_replace ( '/\s+/', ',', $posted_item_id ) );
		
		$item_ids = array_map ( function ($batch) {
			$integer_value_of_batch_number = intval ( $batch );
			// safety check
			// if the integer value of a batch number is 0,
			// table having 0 as batch number has different meaning
			// thus returns -1, table will never have any value -1;
			return $integer_value_of_batch_number ?  : - 1;
		}, explode ( ",", $posted_item_id ) );
		
		
		foreach ($item_ids as $item_id){
			var_dump($item_id);
			
			$item = Item::where('is_deleted', 0)
							->where('batch_number', '!=', '0')
							->whereNull('tracking_number')
// 							->whereNotIn( 'station_name', Helper::$shippingStations)
							->where ( 'id', $item_id )
							->first ();
			
			// If Item exist
			if(count($item) > 0){
				
				if($item->previous_station != ""){
					Item::where ( 'id', $item_id )
							->update([
							'station_name' => $item->previous_station,
							'previous_station' => $item->station_name,
							'reached_shipping_station' => 0
							]);

					if (in_array ( $item->station_name, Helper::$shippingStations )) {
						Ship::where('item_id', $item_id)
						->whereNull('tracking_number')
						->delete();
					}
					
// 					Helper::jewelDebug("Wap: Used Back to Previous Station ItemID #".$item_id." ".$item->order_id);
					Helper::histort("Wap: Used Back to Previous Station ItemID #".$item_id,$item->order_id);
					return redirect ()->back ()->with ( 'success', sprintf ( "Order# %s and Item# %d back to station: %s from station: %s", $item->order_id, $item_id, $item->previous_station, $item->station_name ) );
								
				}else{
					
					$qdc_station = explode("-",$item->station_name);
					$qdc_station = $qdc_station[0]."-QCD";
					
					Item::where ( 'id', $item_id )
						->where('is_deleted', 0)
						->update([
						'station_name' => $qdc_station,
						'previous_station' => $item->station_name,
						'reached_shipping_station' => 0,
						'change_date' => date('Y-m-d H:i:s', strtotime('now'))
					]);
						
					if (in_array ( $item->station_name, Helper::$shippingStations )) {
						Ship::where('item_id', $item_id)
							->where('is_deleted', 0)
							->whereNull('tracking_number')
							->delete();
					}
					
// 					Helper::jewelDebug("Wap: Used Back to Previous Station ItemID #".$item_id." ".$item->order_id);
					Helper::histort("Wap: Used Back to Previous Station ItemID #".$item_id,$item->order_id);
					return redirect ()->back ()->with ( 'success', sprintf ( "Order# %s and Item# %d back to station: %s from station: %s", $item->order_id, $item_id, $item->previous_station, $qdc_station ) );
					
				}
				
			}
		}
		
// 		return view ( 'items.back_prev_station' );
		return redirect ()->back ()->withErrors ( "No Item# ". $posted_item_id. " Found");
	}
}
