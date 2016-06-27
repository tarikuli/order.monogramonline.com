<?php

namespace App\Http\Controllers;

use App\BatchRoute;
use App\Item;
use App\Order;
use App\Purchase;
Use App\Ship;
use App\SpecificationSheet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Monogram\AppMailer;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
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
		$batches = $request->exists('batch_number') && is_array($request->get('batch_number')) ? array_filter($request->get('batch_number')) : null;
		if ( !$batches || !is_array($batches) ) {
			#return view('errors.404');
			return redirect()
				->back()
				->withErrors([ 'error' => 'No batch is selected to print' ]);
		}
		$station_name = $request->get('station', '');

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
			$module = $this->batch_printing_module($batch_number, $station_name);
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
		$station_name = $request->get('station', '');

		$order_ids = Item::whereIn('batch_number', $batches)
						 ->searchByStation($station_name)
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

	private function batch_printing_module ($batch_number, $station_name)
	{
		$item = Item::with('shipInfo', 'order.customer', 'lowest_order_date', 'route.stations_list', 'groupedItems', 'order', 'station_details', 'product')
					->where('batch_number', '=', $batch_number)
					->groupBy('batch_number')
					->latest('batch_creation_date')
					->first();
		/*if ( !count($item) ) {*/
		if ( !$item ) {
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

		return view('prints.printing_module', compact('station_name', 'item', 'batch_status', 'next_station_name', 'current_station_name', 'batch_number', 'statuses', 'route', 'stations', 'count', 'department_name'));
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

	/**
	 * Send bulk Shipping Confirm
	 * @param Request $request
	 * @param AppMailer $appMailer
	 * @return void
	 */
	public function sendShippingConfirm (Request $request, AppMailer $appMailer)
	{

		// --- here I will send order one by one ---
		$order_ids = $request->exists('order_id') ? array_filter($request->get('order_id')) : null;

		if ( !$order_ids || !is_array($order_ids) ) {
			#return view('errors.404');
			return redirect()
			->back()
			->withErrors([ 'error' => 'No order_id is selected to send email.' ]);
		}

		$orders = $this->getOrderFromId($order_ids);

		if ( !$orders->customer->bill_email ) {
			return redirect()->to('/items')
			->withErrors([ 'error' => 'No Billing email address fount for order# '.$order_ids[0] ]);
		}

// return $orders->customer->bill_email ;

		$modules = $this->getDeliveryConfirmationEmailFromOrder($orders);

		// Send email. nortonzanini@gmail.com
		$subject = "Your USPS-Priority Tracking Number From MonogramOnline.com (Order # ".$orders->short_order.")";
		if($appMailer->sendDeliveryConfirmationEmail($modules, $orders->customer->bill_email, $subject)){
			return redirect()
							->back()
							->with('success', sprintf("Email sent to %s Order# %s.", $orders->customer->bill_email,$order_ids[0]));
		}

	}

	/**
	 * Send bulk Shipping Confirm
	 * @param Request $request
	 * @param AppMailer $appMailer
	 * @return void
	 */
	public function sendShippingConfirmByScript (AppMailer $appMailer)
	{

		$ships = Ship::whereNull('shipping_unique_id')
					->whereNotNull('tracking_number')
					->lists('order_number')
					->toArray();

		$orders = $this->getOrderFromId($ships);

		foreach ($orders as $order){
			if ( !$order->customer->bill_email ) {
				log::error('No Billing email address fount for order# '.$order->order_id);
			}else{
				// return $orders->customer->bill_email ;
				$modules = $this->getDeliveryConfirmationEmailFromOrder($order);
				// Send email. nortonzanini@gmail.com
				$subject = "Your USPS-Priority Tracking Number From MonogramOnline.com (Order # ".$order->short_order.")";
				if($appMailer->sendDeliveryConfirmationEmail($modules, $order->customer->bill_email, $subject)){
					Log::info( sprintf("Shipping Confirmation Email sent to %s Order# %s.", $order->customer->bill_email, $order->order_id) );

					// Update numbe of Station assign from items_to_shift
					Ship::where('order_number', 'LIKE', $order->order_id)
					->update([
					'shipping_unique_id' => 'send',
					]);
				}else{
					log::error('No Billing email address fount for order# '.$order->order_id);
				}
			}
		}
	}


	private function getDeliveryConfirmationEmailFromOrder ($params) // get each order row
	{
		$orders = [ ];
		if ( $params instanceof Collection ) {
			$orders = $params; // is this a collection? if yes, then it's an array
		} else {
			$orders[] = $params; // if it is not a collection, then it's a single order
		}

		$modules = [ ];
		foreach ( $orders as $order ) {
			$modules[] = view('prints.includes.email_spec_partial', compact('order'))->render();
		}

		return $modules;
	}

	/**
	 * Send bulk Order receive Confirm
	 * @param Request $request
	 * @param AppMailer $appMailer
	 * @return void
	 */
	public function sendOrderConfirm (Request $request, AppMailer $appMailer)
	{
		// --- here I will send order one by one ---
		$order_ids = $request->exists('order_id') ? array_filter($request->get('order_id')) : null;

		if ( !$order_ids || !is_array($order_ids) ) {
			Log::error('No order_id is selected to send email in Order confirmation.');

		}
		$orders = $this->getOrderFromId($order_ids);

		$orders->customer->bill_email;

		if ( !$orders->customer->bill_email ) {
			Log::error( 'No Billing email address fount for order# '.$order_ids[0] .' in Order confirmation.');
		}

		$modules = $this->getOrderConfirmationEmailFromOrder($orders);

		// Send email. nortonzanini@gmail.com
		$subject = $orders->customer->bill_full_name." - Your Order Status with MonogramOnline.com (Order # ".$orders->short_order.")";
		if($appMailer->sendDeliveryConfirmationEmail($modules, $orders->customer->bill_email, $subject)){
			Log::info( sprintf("Order Confirmation Email sent to %s Order# %s.", $orders->customer->bill_email,$order_ids[0]) );
		}

	}


	private function getOrderConfirmationEmailFromOrder ($params) // get each order row
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
			$modules[] = view('prints.includes.order_spec_partial', compact('order'))->render();
		}

		return $modules;
	}

}
