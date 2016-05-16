<?php

namespace App\Http\Controllers;

use App\BatchRoute;
use App\Item;
use App\Order;
use App\Purchase;
use App\SpecificationSheet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Monogram\Helper;

class PrintController extends Controller
{
	public function packing ($id)
	{
		$order = $this->getOrderFromId($id);

		if ( !$order ) {
			return view('errors.404');
		}
		$modules = $this->getPackingModulesFromOrder($order);

		return view('prints.packing', compact('modules'));
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
		/*https://www.4psitelink.com/setup/batch_print.php?ad[]=22602*/
		$batches = $request->exists('batch_number') ? array_filter($request->get('batch_number')) : null;
		if ( !$batches || !is_array($batches) ) {
			#return view('errors.404');
			return redirect()
				->back()
				->withErrors([ 'error' => 'No batch is selected to print' ]);
		}

		$modules = [ ];

		/*if ( count($batches) == 1 ) {
			$batch_number = $batches[0];
			$module = $this->batch_printing_module($batch_number);
			$modules[] = $module->render();
		} else {
			foreach ( $batches as $batch_number ) {
				$module = $this->batch_printing_module($batch_number);
				$modules[] = $module->render();
			}
		}*/
		foreach ( $batches as $batch_number ) {
			$module = $this->batch_printing_module($batch_number);
			$modules[] = $module->render();
		}

		return view('prints.batch_printer')->with('modules', $modules);
	}

	public function batch_packing_slip (Request $request)
	{
		$batches = $request->exists('batch_number') ? array_filter($request->get('batch_number')) : null;
		if ( !$batches || !is_array($batches) ) {
			#return view('errors.404');
			return redirect()
				->back()
				->withErrors([ 'error' => 'No batch is selected to print' ]);
		}

		$order_ids = Item::whereIn('batch_number', $batches)
						 ->lists('order_id')
						 ->toArray();

		$orders = $this->getOrderFromId($order_ids);

		$modules = $this->getPackingModulesFromOrder($orders);

		/*foreach ( $batches as $batch_number ) {
			$module = $this->batch_printing_module($batch_number);
			$modules[] = $module->render();
		}*/

		return view('prints.batch_printer')->with('modules', $modules);
	}

	private function batch_printing_module ($batch_number)
	{
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

		return view('prints.printing_module', compact('item', 'batch_status', 'next_station_name', 'current_station_name', 'batch_number', 'statuses', 'route', 'stations', 'count', 'department_name'));
	}

	private function getOrderFromId ($order_ids) // get an id or an array of order id
	{
		if ( is_array($order_ids) ) {
			$orders = Order::with('customer', 'items.shipInfo')
						   ->whereIn('order_id', $order_ids)
						   ->get();

			return $orders;
		} else {
			$order = Order::with('customer', 'items.shipInfo')
						  ->where('order_id', $order_ids)
						  ->first();

			return $order;
		}

	}

	private function getPackingModulesFromOrder ($params) // get each order row
	{
		#dd($params instanceof Collection);
		$orders = [ ];
		if ( $params instanceof Collection ) {
			$orders = $params; // is this a collection? if yes, then it's an array
		} else {
			$orders[] = $params; // if it is not a collection, then it's a single order
		}
		$modules = [ ];
		foreach ( $orders as $order ) {
			$modules[] = view('prints.includes.print_slip_partial', compact('order'))->render();
		}

		return $modules;
	}

	public function print_spec_sheet (Request $request)
	{
		$specs = SpecificationSheet::with('production_category')
								   ->whereIn('id', $request->get('spec_id'))
								   ->get();
		if ( !$specs ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'No spec sheet was chosen',
				]);
		}
		$modules = [ ];
		foreach ( $specs as $spec ) {
			$modules[] = $this->spec_print_module($spec);
		}

		return view('prints.spec_sheet')->with('modules', $modules);
	}

	private function spec_print_module ($spec)
	{
		return view('prints.includes.print_spec_partial')
			->with('spec', $spec)
			->render();
	}
}
