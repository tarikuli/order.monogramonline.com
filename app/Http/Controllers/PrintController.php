<?php

namespace App\Http\Controllers;

use App\BatchRoute;
use App\Item;
use App\Order;
use App\Customer;
use App\Purchase;
Use App\Ship;
use App\SpecificationSheet;
use App\Setting;
use App\Inventory;
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
		#return $request->all();
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
			$batch_number = explode('tarikuli', $batch_number);
			$batch_num = $batch_number[0];
			if(!$request->exists('station')){
				$station_name = $batch_number[1];
			}
			set_time_limit(0);
			$module = $this->batch_printing_module($batch_num, $station_name);
			$modules[] = $module->render();
		}
		return view('prints.batch_printer')->with('modules', $modules);
	}

	public function batch_packing_slip (Request $request)
	{
// 		return $request->all();
		$batches = $request->exists('batch_number') ? array_filter($request->get('batch_number')) : null;
		if ( !$batches || !is_array($batches) ) {
			#return view('errors.404');
			return redirect()
				->back()
				->withErrors([ 'error' => 'No batch is selected to print' ]);
		}
		$station_name = $request->get('station', '');

		$order_ids = [];
		foreach ( $batches as $batch_number ) {
			$batch_number = explode('tarikuli', $batch_number);
			$batch_num = $batch_number[0];
			if(!$request->exists('station')){
				$station_name = $batch_number[1];
			}
// Helper::jewelDebug($batch_number);
// Helper::jewelDebug($station_name);
			$order_id = Item::where('batch_number', $batch_number[0])
							->searchByStation($batch_number[1])
							->WhereNull('tracking_number')
							->where('is_deleted', 0)
							->lists('order_id')
							->toArray();
			$order_ids = array_merge($order_id,$order_ids);
		}

		$orders = $this->getOrderFromId($order_ids);
		$modules = $this->getPackingModulesFromOrder($orders);
// dd($batches,$order_ids, $modules);
		return view('prints.batch_printer')->with('modules', $modules);
	}

	public function print_stock_no_unique (Request $request)
	{
		$inventory = Inventory::find($request->get('stock_no_unique'));
// 		dd($request->all(), $inventory);
		return view('prints.print_stock_no_unique')->with('inventory', $inventory);
	}
	
	public function batch_packing_slip_small (Request $request)
	{
		// 		return $request->all();
		$batches = $request->exists('batch_number') ? array_filter($request->get('batch_number')) : null;
		if ( !$batches || !is_array($batches) ) {
			#return view('errors.404');
			return redirect()
			->back()
			->withErrors([ 'error' => 'No batch is selected to print' ]);
		}
		$station_name = $request->get('station', '');
	
		$order_ids = [];
		foreach ( $batches as $batch_number ) {
			$batch_number = explode('tarikuli', $batch_number);
			$batch_num = $batch_number[0];
			if(!$request->exists('station')){
				$station_name = $batch_number[1];
			}
	
			$order_id = Item::whereIn('batch_number', $batch_number)
			->searchByStation($station_name)
			->WhereNull('tracking_number')
			->lists('order_id')
			->toArray();
			$order_ids = array_merge($order_id,$order_ids);
		}
		$orders = $this->getOrderFromId($order_ids);
		$modules = $this->getSmallPackingModulesFromOrder($orders);
	
		return view('prints.batch_printer_small')->with('modules', $modules);
	}
	
	private function batch_printing_module ($batch_number, $station_name)
	{
// 		$item = Item::with('shipInfo', 'order.customer', 'lowest_order_date', 'route.stations_list', 'groupedItems', 'order', 'station_details', 'product')
		$item = Item::with('order.customer', 'lowest_order_date', 'route.stations_list', 'groupedItems', 'order', 'station_details', 'product')
					->where('batch_number', '=', $batch_number)
					->whereNull('tracking_number')
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
						   ->where('is_deleted', 0)
						   ->get();

			return $orders;
		} else {
			$order = Order::with('customer', 'items.shipInfo')
						  ->where('order_id', $order_ids)
						  ->where('is_deleted', 0)
						  ->first();

			return $order;
		}

	}

	private function getSmallPackingModulesFromOrder ($params) // get each order row
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
			$modules[] = view('prints.includes.print_slip_partial_small', compact('order'))->render();
		}
	
		return $modules;
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

		if ( !$orders->first()->customer->bill_email ) {
			return redirect()->to('/items')
			->withErrors([ 'error' => 'No Billing email address fount for order# '.$order_ids[0] ]);
		}

		$modules = $this->getDeliveryConfirmationEmailFromOrder($orders);

		// Send email. nortonzanini@gmail.com
		$subject = "Your USPS-Priority Tracking Number From MonogramOnline.com (Order # ".$orders->first()->short_order.")";
		if($appMailer->sendDeliveryConfirmationEmail($modules, $orders->first()->customer->bill_email, $subject)){
			return redirect()
							->back()
							->with('success', sprintf("Email sent to %s Order# %s.", $orders->first()->customer->bill_email,$order_ids[0]));
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
		// 0,30 9-17 * * * /php /var/www/order.monogramonline.com/artisan route:call --uri=prints/sendbyscript >> /dev/null 2>&1
	
//	SELECT *  FROM  `shipping` WHERE  `tracking_number` IS NOT NULL AND  `shipping_unique_id` LIKE  'pro'
							
//	UPDATE `shipping` SET  `shipping_unique_id` = NULL WHERE  `tracking_number` IS NULL AND  `shipping_unique_id` LIKE  'pro'			
//	UPDATE `shipping` SET  `shipping_unique_id` = 's' WHERE  `tracking_number` IS NOT NULL AND  `shipping_unique_id` LIKE  'pro';
		
		$ships = Ship::whereNull('shipping_unique_id')
					->whereNotNull('tracking_number')
					->whereNull('shipping_unique_id')
					->groupBy('unique_order_id')
					->orderBy('id', 'ASC')
					->lists('order_number')
					->take(500)
					->toArray();

		foreach ($ships as $ship){
			Ship::where('order_number', $ship)
				->update([
					'shipping_unique_id' => 'pro',
				]);
		}

// dd($ships, array_keys($ships));		
		$orders = $this->getOrderFromId($ships);

		foreach ($orders as $order){
			set_time_limit(0);
			if ( !$order->customer->bill_email ) {
				log::error('No Billing email address fount for order# '.$order->order_id);
					Ship::where('order_number',  $order->order_id)
					->update([
						'shipping_unique_id' => 'No Email',
					]);
			}else{
				// return $orders->customer->bill_email ;
				$modules = $this->getDeliveryConfirmationEmailFromOrder($order);
				// Send email. nortonzanini@gmail.com
				$subject = "Your USPS-Priority Tracking Number From MonogramOnline.com (Order # ".$order->short_order.")";
				
				if($appMailer->sendDeliveryConfirmationEmail($modules, $order->customer->bill_email, $subject)){
					Log::info( sprintf("Shipping Confirmation Email sent to %s Order# %s.", $order->customer->bill_email, $order->order_id) );

					// Update numbe of Station assign from items_to_shift
					Ship::where('order_number', $order->order_id)
						->update([
							'shipping_unique_id' => 's',
						]);
					#sleep(1);
				}else{
					Ship::where('order_number', $order->order_id)
					->update([
						'shipping_unique_id' => 'Not',
					]);
					log::error('No send email order# '.$order->order_id);
				}
			}
		}

// 		Helper::jewelDebug("Total ".count($orders)." email sent.");
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
	
	
	public function printShippingLableByOrderId(Request $request){
		$customer = $request->all();
// dd($customer);	
		$return = false;
		if((!$request->has('unique_order_id'))){
			return redirect()
			->back()
			->withErrors([
					'error' => 'No Unique Order Id found',
			]);
		}
		
		if(!isset($customer['ship_zip'])){
			return redirect()
			->back()
			->withErrors([
					'error' => 'No valide zip code found or Remove special charcter from shipping address',
			]);
		}
	
// 		$customer = Customer::where('order_id', $request->get('order_number'))
// 					->where('is_deleted', 0)
// 					->first();
	
// 		if(!Helper::getcountrycode($customer->ship_country)){
// 			return redirect()
// 			->back()
// 			->withErrors([
// 					'error' => 'Order number '.$request->order_number.' invalive country code <b>'. $customer->ship_country.'</b><br>Please update correct cuntory code formate like<br><b>US United States</b><br><b>CA Canada</b><br><b>VI Virgin Islands (U.S.)</b>',
// 			]);
// 		}
		// Start shipment
		$shipment = new \Ups\Entity\Shipment();
	
		// Set shipper
		$shipper = $shipment->getShipper();
		$shipper->setShipperNumber(env('SHIPPER_NUMBER'));
		$shipper->setName('Deal to win');
		$shipper->setAttentionName('Customer Service Dept');
		$shipperAddress = $shipper->getAddress();
		$shipperAddress->setAddressLine1('575 Underhill Blvd');
		$shipperAddress->setPostalCode('11791');
		$shipperAddress->setCity('Syosset');
		$shipperAddress->setCountryCode('US');
		$shipperAddress->setStateProvinceCode('NY');
		$shipper->setAddress($shipperAddress);
		$shipper->setEmailAddress('cs@monogramonline.com ');
		$shipper->setPhoneNumber('585-296-8810');
		$shipment->setShipper($shipper);
	
		// To address
		$address = new \Ups\Entity\Address();
		$address->setAddressLine1($customer['ship_address_1']);
		if(isset($customer['ship_address_2'])){
			$address->setAddressLine2($customer['ship_address_2']);
		}else{
			$address->setAddressLine2('');
		}
		$address->setAddressLine3('');
		$address->setPostalCode($customer['ship_zip']);
		$address->setCity($customer['ship_city']);
		$address->setCountryCode(Helper::getcountrycode($customer['ship_country']));
		$address->setStateProvinceCode($customer['ship_state']);
		$shipTo = new \Ups\Entity\ShipTo();
		$shipTo->setAddress($address);
		if($customer['ship_company_name']){
			$shipTo->setCompanyName($customer['ship_company_name']);
		}else{
			$shipTo->setCompanyName('-');
		}
		$shipTo->setAttentionName($customer['ship_full_name']);
		$shipTo->setEmailAddress($customer['ship_email']);
		$shipTo->setPhoneNumber($customer['ship_phone']);
		$shipment->setShipTo($shipTo);
	
		// Set service
		$service = new \Ups\Entity\Service;
// 		$service->setCode(\Ups\Entity\Service::S_GROUND);
$service->setCode(\Ups\Entity\Service::S_EXPEDITED_MAIL_INNOVATIONS);
		$service->setDescription($service->getName());
		
		$shipment->setService($service);
	
		// Mark as a return (if return)
		if ($return) {
			$returnService = new \Ups\Entity\ReturnService;
			$returnService->setCode(\Ups\Entity\ReturnService::PRINT_RETURN_LABEL_PRL);
			$shipment->setReturnService($returnService);
		}

	
		// Set description
		$shipment->setDescription($customer['unique_order_id'].' Gift Item');
		
$shipment->setShipmentUSPSEndorsement('2');
$shipment->setCostCenter('00001');
$short_order = explode("-", $customer['unique_order_id']);		
$shipment->setPackageID($short_order[1]);

		// Add Package
		$package = new \Ups\Entity\Package();
// 		$package->getPackagingType()->setCode(\Ups\Entity\PackagingType::PT_PACKAGE);
$package->getPackagingType()->setCode(\Ups\Entity\PackagingType::PT_IRREGULARS);		
		$package->getPackageWeight()->setWeight(5);
		$unit = new \Ups\Entity\UnitOfMeasurement;
// 		$unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_LBS);
$unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_OZS);		
		$package->getPackageWeight()->setUnitOfMeasurement($unit);
	
		// Set dimensions
		$dimensions = new \Ups\Entity\Dimensions();
		$dimensions->setHeight(1);
		$dimensions->setWidth(5);
		$dimensions->setLength(5);
		$unit = new \Ups\Entity\UnitOfMeasurement;
		$unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_IN);
		$dimensions->setUnitOfMeasurement($unit);
		$package->setDimensions($dimensions);
	
		// Add descriptions because it is a package
		$package->setDescription('Box/Envelope');
	
		// Add this package
		$shipment->addPackage($package);
	
		// Set payment information
		$shipment->setPaymentInformation(new \Ups\Entity\PaymentInformation('prepaid', (object)array('AccountNumber' => env('SHIPPER_NUMBER'))));
	
		// Ask for negotiated rates (optional)
		$rateInformation = new \Ups\Entity\RateInformation;
		$rateInformation->setNegotiatedRatesIndicator(1);
		$shipment->setRateInformation($rateInformation);
	
		try {
			// $api = new \Ups\Shipping($accessKey, $userId, $password);
			$api = new \Ups\Shipping(env('UPS_ACCESS_KEY'), env('UPS_USER_ID'), env('UPS_PASSWORD'));
			$confirm = $api->confirm(\Ups\Shipping::REQ_VALIDATE, $shipment);
			// 			var_dump($confirm); // Confirm holds the digest you need to accept the result
			if ($confirm) {
				$accept = $api->accept($confirm->ShipmentDigest);
	
// 				echo "<pre>";
// 					$result=$accept;
// 					var_dump((array) $accept); // Accept holds the label and additional information
// 				echo "</pre>";
				
// 				$trackingInfo['full_xml_source'] = Helper::generate_valid_xml_from_array($accept);
				Helper::saveUpsLabel(Helper::generate_valid_xml_from_array($accept),$customer['unique_order_id']);
				$trackingInfo['unique_order_id'] = $customer['unique_order_id'];
				$trackingInfo['order_number'] = $customer['order_number'];
				$trackingInfo['tracking_number'] = $accept->PackageResults->TrackingNumber;
				$trackingInfo['shipping_id'] =  $accept->PackageResults->USPSPICNumber;
				$trackingInfo['mail_class'] =  "UPS Expedited Mail Innovations";
				
				Helper::updateTrackingNumber($trackingInfo);
// 				Helper::jewelDebug(Helper::generate_valid_xml_from_array($accept));
				return view('prints.ups_shipping_lable2')->with('labelImage', $accept->PackageResults->LabelImage->GraphicImage);
			}
		} catch (\Exception $e) {
				Helper::jewelDebug($e->getMessage());
		}
	}

	
	public function reprintShippinglabel(Request $request){

// 		dd($request->all());
		return view('prints.ups_shipping_lable')->with('labelImage', "../".$request->get('graphicImage'));
	}
	
	public function getPackingSlipPrintByOrderId(Request $request) {
	
		return view ( 'prints.packingSlipPrintByOrderId' );
	}
	
	
	public function postPackingSlipPrintByOrderId(Request $request){
		
		$order_ids= [];
		$unique_order_ids = $request->get ( 'unique_order_id' );
		// remove newlines and spaces
		$unique_order_ids = trim ( preg_replace ( '/\s+/', ',', $unique_order_ids ) );
		
		$unique_orderArray = explode ( ",", $unique_order_ids ) ;
		
		foreach ($unique_orderArray as $unique_order_id){
			$order_id = Helper::getOrderNumber($unique_order_id);
			if($order_id != false){
				$order_ids[]= $order_id;
			}
		}
		
		$order_ids = array_unique($order_ids);
		$orders = $this->getOrderFromId($order_ids);
		$modules = $this->getPackingModulesFromOrder($orders);
		
		if(count($modules)== 0){
			return redirect()
				->back()
				->withErrors([ 'error' => 'No valide Order# found' ]);
		}	
		
		return view('prints.batch_printer')->with('modules', $modules);

	}

	public function getPrintImageByBatch(Request $request) {
		
		$destination['printer1'] = "Move to Printer#1";
		$destination['printer2'] = "Move to Printer#2";
		
		return view ( 'prints.printImageByBatch' )
					->with( 'destination', $destination )
					->with('destinationSelect', "printer1");
	}
	
	public function postPrintImageByBatch(Request $request){
		$order_ids= [];
		$batchNumbersNotFound =[];
		$batchFileNotFound = [];
		$batchMoveGood = [];
		
		$destination['printer1'] = "Move to Printer#1";
		$destination['printer2'] = "Move to Printer#2";
		
		$batchNumbers = $request->get ( 'batch_number' );
		// remove newlines and spaces
		$batchNumbers = trim ( preg_replace ( '/\s+/', ',', $batchNumbers ) );
		$uniqueBatchArray = array_unique(explode ( ",", $batchNumbers )) ;
		
		
		$imageSearchArray = [];
		$source_image_dir = "";
		// Set Source file name
		$settings = Setting::all()
								->where('is_deleted', '0')
								->toArray();
								
		
		// Put all Search Setting
		foreach ($settings as  $fileNameIndex => $fileName){
			// 			$this->logger("info", $fileName['supervisor_station']);
		
			switch ($fileName['supervisor_station']) {
				case "imageSearch":
					$imageSearchArray[$fileName['default_shipping_rule']] = $fileName['default_route_id'];
					break;
				case "source_image_dir":
					$source_image_dir = $fileName['default_route_id'];
					break;
			}
		
		}
		
		if($request->get ( 'destination' ) == "printer1"){
			$printerNumber = "";
		}else{
			$printerNumber = 1;
		}
// 		Helper::jewelDebug($move_to_soft_dir = "/media/c_print/Soft".$printerNumber."/");
// dd($request->all());
		// Get All  directory from
		if(file_exists ($source_image_dir)){
// Helper::jewelDebug($uniqueBatchArray);	
		
			foreach ($uniqueBatchArray as $batchNumber){
				
				if(empty($batchNumber)){
					return redirect()
					->back()
					->withErrors([ 'error' => 'Please Select Valide Batch number' ]);
					break;
				}
				
				$retval=[];
				$last_line = exec("find ".$source_image_dir." -name '".$batchNumber."*'", $retval);
// 				$last_line = exec("find /home/jewel/Documents/graphics_Done -name '".$batchNumber."*'", $retval);
				// find /media/Ji-share/graphics_Done -name '30853*'
				if(count($retval)== 0){
					$batchNumbersNotFound[] = $batchNumber;
					
				}else{
// 					Helper::jewelDebug($retval);
					$file_count_in_directory = 0;
					foreach ($retval as  $file_copy_from){
						if(is_file($file_copy_from)){
							$fileName = explode("/", $file_copy_from);
							$getLast =count($fileName);
							if(isset($fileName[$getLast-1])){
								$file_name =$fileName[$getLast-1];
								if (strpos($file_name, "soft") !== false) {
									#$move_to_soft_dir = "/media/Ji-share/graphics_Move_Done/sublimation/soft/";
									$move_to_soft_dir = "/media/c_print/Soft".$printerNumber."/";
									$move_to_soft_dir= $move_to_soft_dir.$file_name;
// 									Helper::jewelDebug("cp \"$file_copy_from\" \"$move_to_soft_dir\" > /dev/null 2>/dev/null &");
									shell_exec("cp \"$file_copy_from\" \"$move_to_soft_dir\" > /dev/null 2>/dev/null &");
									$file_count_in_directory ++;
								}if (strpos($file_name, "hard") !== false) {
									$move_to_soft_dir = "/media/c_print/Hard".$printerNumber."/";
									$move_to_soft_dir= $move_to_soft_dir.$file_name;
// 									Helper::jewelDebug("cp \"$file_copy_from\" \"$move_to_soft_dir\" > /dev/null 2>/dev/null &");
									shell_exec("cp \"$file_copy_from\" \"$move_to_soft_dir\" > /dev/null 2>/dev/null &");
									$file_count_in_directory ++;
								}
							}
// 							Helper::jewelDebug($fileName);
// 							Helper::jewelDebug(($getLast-1)." -- ".$fileName[$getLast-1]);
							
						}
					}
					
					if($file_count_in_directory == 0){
						$batchFileNotFound[] = $batchNumber;
	// 					Helper::jewelDebug("Total file count = ".$file_count_in_directory);
					}else{
						$batchMoveGood[] = $batchNumber;
					}
					
						
				}
			}
		} else {
			return redirect()
						->back()
						->withErrors([ 'error' => 'source_image_dir not found go to Setting Table' ]);
			
		}
		
// 		Helper::jewelDebug("No File found for Batch# ");
// 		Helper::jewelDebug($batchNumbersNotFound);

// 		Helper::jewelDebug("File Not found in Batch# ");
// 		Helper::jewelDebug($batchFileNotFound);
		
		

		return view('prints.printImageByBatch')
				->with('success', "File moves")
				->with('batchNumbersNotFound', $batchNumbersNotFound)
				->with('batchFileNotFound', $batchFileNotFound)
				->with('batchMoveGood', $batchMoveGood)
				->with('destination', $destination)
				->with('destinationSelect', $request->get ( 'destination' ));
	
	}
}
