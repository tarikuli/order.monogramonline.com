<?php

namespace App\Http\Controllers;

use App\BatchRoute;
use App\Item;
use App\Order;
use App\Purchase;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Monogram\Helper;

class PrintController extends Controller
{
	public function packing ($id)
	{
		$order = Order::with('customer', 'items.shipInfo')
					  ->where('order_id', $id)
					  ->first();
		if ( !$order ) {
			return view('errors.404');
		}

		return view('prints.packing', compact('order'));
	}

	public function invoice ($id)
	{
		$order = Order::with('customer', 'items.shipInfo')
					  ->where('order_id', $id)
					  ->first();
		if ( !$order ) {
			return view('errors.404');
		}

		return view('prints.invoice', compact('order'));
	}

	public function purchase ($purchase_id)
	{
		$purchase = Purchase::with('vendor_details', 'products.product_details')
							->where('id', $purchase_id)
							->first();
		if ( !$purchase ) {
			return view('errors.404');
		}

		#return $purchase;
		return view('prints.purchase', compact('purchase'));
	}

	public function batches (Request $request)
	{
		$batches = $request->exists('batch_number') ? array_filter($request->get('batch_number')) : null;
		/*https://www.4psitelink.com/setup/batch_print.php?ad[]=22602*/
		if ( !$batches || !is_array($batches) ) {
			return view('errors.404');
		}

		if ( count($batches) == 1 ) {
			$batch_number = $batches[0];

			$item = Item::with('shipInfo', 'order.customer', 'lowest_order_date', 'route.stations_list', 'groupedItems', 'order', 'station_details', 'product')
						->where('batch_number', '=', $batch_number)
						->groupBy('batch_number')
						->latest('batch_creation_date')
						->first();
			if ( !count($item) ) {
				return view('errors.404');
			}

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

			$next_station_name = '';

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
			}

			#$bar_code = Helper::getHtmlBarcode($batch_number);
			$statuses = Helper::getBatchStatusList();
			$route = BatchRoute::with('stations', 'template')
							   ->find($item->batch_route_id);
			$stations = Helper::routeThroughStations($item->batch_route_id);

			#return compact('items', 'bar_code', 'batch_number', 'statuses', 'route', 'stations', 'count', 'department_name');
			$count = 1;

			return view('prints.single_batch_print', compact('item', 'batch_status', 'next_station_name', 'current_station_name', 'batch_number', 'statuses', 'route', 'stations', 'count', 'department_name'));
		} else {
			foreach ( $batches as $batch ) {

			}
		}
	}
}
