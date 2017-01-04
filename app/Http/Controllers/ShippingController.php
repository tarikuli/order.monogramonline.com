<?php

namespace App\Http\Controllers;

use App\Item;
use App\Ship;
use App\Customer;
use Illuminate\Http\Request;
use App\UpsTable;

use App\Note;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Monogram\Helper;
use Illuminate\Support\Facades\Auth;
use Ups\Shipping;

class ShippingController extends Controller
{
	public static $search_in = [
		'unique_order_id' => 'Unique Order Id',
		'item_id'    	  => 'Item id',
		'address_one'     => 'Address 1',
		'address_two'     => 'Address 2',
		'name'            => 'Name',
		'order_number'    => 'Order number',
		'package_shape'   => 'Package shape',
		'company'         => 'Company',
		'city'            => 'City',
		'state'           => 'State',
		'postal_code'     => 'Postal code',
		'country'         => 'Country',
		'email'           => 'Email',
		'phone'           => 'Phone',
		'transaction_id'  => 'Transaction id',
		'mail_class'      => 'Mail class',
		'tracking_number' => 'Tracking number',
	];

	public function index (Request $request)
	{
		foreach ( range(0, 1000) as $count ) {
			set_time_limit(0);
			
			$upsTables = UpsTable::where('is_deleted', '0')
									->take(1000)
									->skip($count * 1000)
									->get();
									
			foreach ($upsTables as $upsTable){
				$items = Item::with ( 'route.stations_list' )
								->where('is_deleted', 0)
								->whereNull('tracking_number')
// 								->where('order_id', 'LIKE', $upsTable->package_id)
								->where('order_id', 'LIKE', sprintf("%%%s%%", $upsTable->package_id))
								->get();
				
				Helper::jewelDebug($upsTable->id."---".$upsTable->package_id."--- ".$count * 1000);
				
				if(count($items) > 0){
					$unique_order_id = Helper::generateShippingUniqueId($items->first()->order);
					
					Ship::where ('order_number', $items->first()->order )
							->whereNull('tracking_number')
							->delete();
					
					foreach ($items as $item){
						$short_order = explode("-", $item->order_id);
						
						if($upsTable->package_id == $short_order[2]){
							$stations_in_route_ids = $item->route->stations_list->lists ( 'station_name' )->toArray ();
							$common_shipping_station = array_values(array_intersect(Helper::$shippingStations,$stations_in_route_ids));
							
							$item->previous_station = $item->station_name;
							$item->station_name = $common_shipping_station[0];
							$item->change_date = date('Y-m-d H:i:s', strtotime('now'));
							$item->item_taxable = 2;
							$item->reached_shipping_station = 1;
							$item->save ();
							

							Helper::insertDataIntoShipping($item, $unique_order_id);
							Helper::histort("Item#".$item->id." from ".$item->station_name." -> ".$common_shipping_station[0], $item->order_id);
							
							Helper::jewelDebug($upsTable->id."---".$upsTable->package_id."---".$upsTable->pic_tracking."---".$item->id."---".$item->order_id."---".$upsTable->pic_tracking);
							//$unique_order_id = Helper::generateShippingUniqueId($items->first()->order);
						}

						$trackingInfo['unique_order_id'] = $unique_order_id;
						$trackingInfo['order_number'] 	 = $items->first()->order;
						$trackingInfo['tracking_number'] = $upsTable->pic_tracking;
						$trackingInfo['shipping_id'] 	 = $upsTable->pic_tracking;
						$trackingInfo['mail_class'] 	 = "UPS Expedited Mail Innovations";
						
						Helper::updateTrackingNumber($trackingInfo);
						
					}
				}
				
				$upsTable->is_deleted = 1;
				$upsTable->save ();
			}
		}
		
		dd("Test");
		
		$ships = Ship::with('item.product')
					 ->where('is_deleted', 0)
					 ->searchTrackingNumberAssigned($request->get('shipped'))
					 ->searchCriteria($request->get('search_for_first'), $request->get('search_in_first'))
					 ->searchCriteria($request->get('search_for_second'), $request->get('search_in_second'))
					 ->searchWithinDate($request->get('start_date'), $request->get('end_date'))
					 // postmark_date transaction_datetime
					 ->latest('transaction_datetime')
// 					 ->toSql();
					 ->paginate(10);

		$counter = Ship::where('is_deleted', 0)
					   ->where('updated_at', 'LIKE', sprintf("%%%s%%", date("Y-m-d")))
					   ->first([
						   DB::raw('COUNT(*) - COUNT(tracking_number) AS unassigned_count'),
						   DB::raw('COUNT(tracking_number) AS assigned_count'),
					   ]);

		$tracking_number_not_assigned = $counter->unassigned_count;
		$tracking_number_assigned = $counter->assigned_count;

		return view('shipping.index', compact('ships', 'request'))
			->with('tracking_number_assigned', $tracking_number_assigned)
			->with('tracking_number_not_assigned', $tracking_number_not_assigned)
			->with('search_in', static::$search_in);
	}
	
	public function postShippingLableByOrderId (Request $request){
// 		return $request->all();
		return redirect(url('shippinglabel_print?unique_order_id='.$request->get('unique_order_id')));
	}
	
	public function shippingAddressUpdate (Request $request){

// return $request->all();
		
		if((!$request->has('unique_order_id')) && (!$request->has('order_number'))){
			return redirect()
			->back()
			->withErrors([
					'error' => 'No Unique Order Id found',
			]);
		}
		

		Customer::where('order_id', $request->get('order_number'))
				->where('is_deleted', 0)
				->update([
					'ship_address_1' => $request->get('address1'),
					'ship_city' => $request->get('city'),
					'ship_state' => $request->get('state_city'),
					'ship_zip' => $request->get('postal_code'),
					'ship_country' => $request->get('country'),																											
				]);
		
// 		Ship::where('order_number', $request->get('order_number'))
// 			->where('is_deleted', 0)
// 			->whereNull('tracking_number')
// 			->update([
// 					'address1' => $request->get('address1'),
// 					'city' => $request->get('city'),
// 					'state_city' => $request->get('state_city'),
// 					'postal_code' => $request->get('postal_code'),
// 					'country' => $request->get('country'),
// 			]);
			
// 		return redirect()
// 				->back()
// 				->with('success', 'Stations changed successfully.');
		
		return redirect(url('shippinglabel_print?unique_order_id='.$request->get('unique_order_id')))
		->with('success', 'Stations changed successfully.');
	}
	
	public function getShippingAddressByOrderId (Request $request){
		$errorMassage = [];
		$ambiguousAdress = [];
		$count = 1;
		$graphicImage = false;
		$counterWeight = 0;
		$ship = [];
		
		//return view('shipping.label_print_ajax');
		return view('shipping.label_print_ajax', compact('ship', 'errorMassage', 'ambiguousAdress', 'count', 'graphicImage', 'counterWeight'));
	}
	
	public function postShippingAddressByOrderId (Request $request){
// 		Helper::jewelDebug($request->ajax());
		if ( $request->has('unique_order_id') && ( $request->ajax() ) ) {
			
// 			Helper::jewelDebug($request->get('unique_order_id'));
			
			$ships = Ship::where('is_deleted', 0)
							->where('unique_order_id', $request->get('unique_order_id'))
							->get();
			$counterWeight = 0;
			if(count($ships)>0){
				$customer = [];
				foreach ( $ships as $ship ) {
					$counterWeight += $ship->actual_weight;
				}
					
				return response()->json([
						'order_number' 		=> $ships[0]->order_number,
						'unique_order_id' 	=> $ships[0]->unique_order_id,
						'mail_class' 		=> $ships[0]->mail_class,
						'tracking_number' 	=> $ships[0]->tracking_number,
						'name' 				=> $ships[0]->name,
						'company' 			=> $ships[0]->company,
						'address1' 			=> $ships[0]->address1,
						'address2' 			=> $ships[0]->address2,
						'city' 				=> $ships[0]->city,
						'state_city' 		=> $ships[0]->state_city,
						'postal_code' 		=> $ships[0]->postal_code,
						'country' 			=> $ships[0]->country,
						'phone'				=> $ships[0]->phone,
						'email'				=> $ships[0]->email,
						'counterWeight' 	=> $counterWeight,
				
				]);
			}else{
				return response()->json([
						'order_number' 		=> null,
// 						'unique_order_id' 	=> $ships[0]->unique_order_id,
						'mail_class' 		=> null,
						'tracking_number' 	=> null,
						'name' 				=> null,
						'company' 			=> null,
						'address1' 			=> null,
						'address2' 			=> null,
						'city' 				=> null,
						'state_city' 		=> null,
						'postal_code' 		=> null,
						'country' 			=> null,
						'phone'				=> null,
						'email'				=> null,
						'counterWeight' 	=> $counterWeight,
				
				]);
			}

			
		}
	}
	
	public function getShippingLableByOrderId (Request $request){
		
		$errorMassage = [];
		$ambiguousAdress = [];
		$count = 1;
		$graphicImage = false;
		$counterWeight = 0;
		
		$queryLog["before"] = strtotime('now');
		$ships = Ship::with('customer')
					->where('is_deleted', 0)
					->where('unique_order_id', $request->get('unique_order_id'))
					->get();
		
		$queryLog["after"] = strtotime('now');
// dd($ships);
		if(count($ships)>0){
			
			
			$counterWeight = 0;
			$customer = [];
			foreach ( $ships as $ship ) {
				$counterWeight += $ship->actual_weight;
			}
			
// 			$customer['order_id'] = $ships[0]->order_number;
// 			$customer['unique_order_id'] = $request->get('unique_order_id');
// 			$customer['ship_full_name'] = $ships[0]->name;
// 			$customer['ship_company_name'] = $ships[0]->company;
// 			$customer['ship_address_1'] = $ships[0]->address1;
// 			$customer['ship_address_2'] = $ships[0]->address2;
// 			$customer['ship_state'] = $ships[0]->state_city;
// 			$customer['ship_city'] = $ships[0]->city;
// 			$customer['ship_country'] = $ships[0]->country;
// 			$customer['ship_zip'] = $ships[0]->postal_code;
			
			$customer['order_id'] = $ships[0]->order_number;
			$customer['unique_order_id'] = $request->get('unique_order_id');
			$customer['ship_full_name'] = 		Helper::removeSpecial($ships[0]->customer->ship_full_name);
			$customer['ship_company_name'] = 	Helper::removeSpecial($ships[0]->customer->ship_company_name);
			$customer['ship_address_1'] = 		Helper::removeSpecial($ships[0]->customer->ship_address_1);
			$customer['ship_address_2'] = 		Helper::removeSpecial($ships[0]->customer->ship_address_2);
			$customer['ship_state'] = 			Helper::removeSpecial($ships[0]->customer->ship_state);
			$customer['ship_city'] = 			Helper::removeSpecial($ships[0]->customer->ship_city);
			$customer['ship_country'] = 		Helper::removeSpecial($ships[0]->customer->ship_country);
			$customer['ship_zip'] = 			Helper::removeSpecial($ships[0]->customer->ship_zip);
			
		
// 			$counterWeight = Ship::where('is_deleted', 0)
// 								->where('unique_order_id', $request->get('unique_order_id'))
// 								->first([
// 										DB::raw('COUNT(actual_weight) AS assigned_count'),
// 								]);
								
// 			$counterWeight = $counterWeight->assigned_count; 
			
// 			$validateStatus = $this->validateAddress($ships[0]->order_number);

			$validateStatus = $this->validateAddress($customer);
			
			if(!$validateStatus['validateAddress']){
				$errorMassage[] ='Please call Customer Service Department for Update correct Shipping address.';
			}
			
			if(!$validateStatus['error']){
				$errorMassage[] = $validateStatus['error'];
			}
			
			if($counterWeight > 48){
				$errorMassage[] ='Can not Ship more than 48 ounces (3 pound) ';
			}
			
			if($validateStatus['isAmbiguous']){
				$ambiguousAdress = $validateStatus['ambiguousAdress'];
			}
			
			// Check If it UPS mail innovation
			if(substr($ships[0]->shipping_id, 0, 5) == "92748"){
				$xml = simplexml_load_string($ships[0]->full_xml_source);
				$json = json_encode($xml);
				$array = json_decode($json,TRUE);
				if($array['PackageResults']['LabelImage']['GraphicImage']){
					$graphicImage = $array['PackageResults']['LabelImage']['GraphicImage'];
				}
				// Grab Shipping label photo from
				$graphicImage = "assets/images/shipping_label/".$request->get('unique_order_id').".gif";
			}
			

// 			Helper::jewelDebug($array['PackageResults']['LabelImage']['GraphicImage']);
// 			dd($json, $array);
// 			dd($counterWeight, $ship, $customer, $validateStatus);
// 			return view('shipping.label_print', compact('ship', 'errorMassage', 'ambiguousAdress', 'count', 'graphicImage', 'counterWeight'));
		}else{
			$ship =[];
		}

		return view('shipping.label_print', compact('ship', 'errorMassage', 'ambiguousAdress', 'count', 'graphicImage', 'counterWeight', 'queryLog'));
	}

	public function validateAddress($customer){
		
		$validateStatus['validateAddress'] = true;
		$validateStatus['isAmbiguous'] = false;
		$validateStatus['ambiguousAdress'] = [];
		$validateStatus['error'] = true;
		
// 		$customer = Customer::where('order_id', $order_id)
// 							->where('is_deleted', 0)
// 							->first();
		
		if(!Helper::getcountrycode($customer['ship_country'])){
			$validateStatus['error'] = 'Order number '.$customer['order_id'].' invalive country code <b>'. $customer['ship_country'].'</b><br>Please update correct cuntory code formate like<br><b>US United States</b><br><b>CA Canada</b><br><b>VI Virgin Islands (U.S.)</b>';
			return $validateStatus;
		}
		
		$address = new \Ups\Entity\Address();
		$address->setAttentionName($customer['ship_full_name']);
		$address->setBuildingName($customer['ship_company_name']);
		$address->setAddressLine1($customer['ship_address_1']);
		$address->setAddressLine2($customer['ship_address_2']);
		$address->setAddressLine3('');
		$address->setStateProvinceCode($customer['ship_state']);
		$address->setCity($customer['ship_city']);
		$address->setCountryCode(Helper::getcountrycode($customer['ship_country']));
		$address->setPostalCode($customer['ship_zip']);
		// shipmentDigest
		// Ptondereau\LaravelUpsApi\UpsApiServiceProvider
		$xav = new \Ups\AddressValidation(env('UPS_ACCESS_KEY'), env('UPS_USER_ID'), env('UPS_PASSWORD'));
		$xav->activateReturnObjectOnValidate(); //This is optional
		try {
			$response = $xav->validate($address, $requestOption = \Ups\AddressValidation::REQUEST_OPTION_ADDRESS_VALIDATION, $maxSuggestion = 5);
		
			if (!$response->isValid()) {
				$validateStatus['validateAddress'] = false;
			}
		
			if ($response->isAmbiguous()) {
				$validateStatus['isAmbiguous'] = true;
				$candidateAddresses = $response->getCandidateAddressList();
				foreach($candidateAddresses as $address) {
					
					//Present user with list of candidate addresses so they can pick the correct one
// 					echo "<pre>";
// 					echo "isAmbiguous+++++++++++\n";
// 					// Dump array with object-arrays
// 					print_r($address);
// 					echo "</pre>";
					$validateStatus['ambiguousAdress'][] = (array)$address;
				}
			}
			

// dd($validateStatus);
			return $validateStatus;		
		} catch (Exception $e) {
// 			var_dump($e);
			$validateStatus['error'] = $e->getMessage();
		}
	} 
	
	public function removeTrackingNumber (Request $request)
	{
		
		if(!$request->has('order_number')){
			return redirect()
			->back()
			->withErrors([
					'error' => 'No Order Number found',
			]);
		}
		$item_ids = [];
		if($request->has('unique_order_id')){
			$item_ids =Ship::where('is_deleted', 0)
						->where('unique_order_id', trim($request->get('unique_order_id')))
						->whereNotNull('tracking_number')
						->orderBy('item_id')
						->lists('item_id','id')
						->toArray();
			$order_number = $request->get('order_number');
			$tracking_numbers = $request->get('tracking_numbers', [ ]);
		}elseif($request->has('item_id')){
			$ship =Ship::where('is_deleted', 0)
						->whereNotNull('tracking_number')
						->where('item_id', trim($request->get('item_id')))
						->first();
			
			if ( $ship ) {
				$item_ids[] = $ship->item_id;
				$order_number = $ship->order_number;
				$tracking_numbers[] = $ship->tracking_numbers;
			}else{
				return redirect()
				->back()
				->withErrors([
						'error' => 'No item_id found',
				]);
			}
		}else {
			return redirect()
			->back()
			->withErrors([
					'error' => 'No Order Number found',
			]);
		}
		
		
		
// dd($request->all(), $item_ids);
				
		
		if ( count($tracking_numbers) ) {
			
			foreach ($item_ids AS $item_id){
				Ship::where('item_id', $item_id)
					->update([
						'tracking_number' => null,
						'shipping_unique_id' => null,
						'full_xml_source' =>"",
						'status' => null,
						'shipping_id' => null
					]);
		
				Helper::histort("Back ".$item_id." previous Tracking# ".implode(", ",$tracking_numbers), $order_number);
			}

		}

		return redirect()
			->back()
			->with('success', "Items# ".implode(", ",$item_ids)." successfully moved to shipping list");
	}

	public function updateTrackingNumber(Request $request)
	{
		
		$order_number = $request->get('order_number_update');
		$tracking_number_update = $request->get('tracking_number_update');
		$item_id = $request->get('item_id');
		
		if ( $order_number && $tracking_number_update) {

// 			Ship::where('order_number', $order_number)
// 			->update([
// 				'tracking_number' => $tracking_number_update,
// 			]);

			Ship::where('item_id', $item_id)
				->update([
					'tracking_number' => $tracking_number_update,
					'shipping_id'     	   => $tracking_number_update,
					'mail_class'     	   => "Manual",
					'shipping_unique_id'   => null,
					'postmark_date'        => date("Y-m-d"),
					'transaction_datetime' => date("Y-m-d H:i:s")
				]);
			
			// Add note history by order id
			Helper::histort("Manualy Update Tracking# ".$tracking_number_update, $order_number);

			return redirect()
			->back()
			->with('success', "Tracking # successfully Updated");
		}

		return redirect()
		->back()
		->withErrors([
				'error' => 'Can not Tracking # Updated',
		]);
	}
	
// groupByUniqueOrderId	

// https://github.com/dkirsche/UPS	
// https://docs.rocketship.it/php/1-0/ups-mail-innovations.html
	public function addressValidation (Request $request)
	{
		if(!$request->has('order_id')){
			return redirect()
			->back()
			->withErrors([
					'error' => 'No Order Number found',
			]);
		}
		
		$customer = Customer::where('order_id', $request->order_id)
							->where('is_deleted', 0)
							->first();
		
		if(!Helper::getcountrycode($customer->ship_country)){
			return redirect()
			->back()
			->withErrors([
					'error' => 'Order number '.$request->order_id.' invalive country code <b>'. $customer->ship_country.'</b><br>Please update correct cuntory code formate like<br><b>US United States</b><br><b>CA Canada</b><br><b>VI Virgin Islands (U.S.)</b>',
			]);
		}
		
		$address = new \Ups\Entity\Address();
		$address->setAttentionName($customer->ship_full_name);
		$address->setBuildingName($customer->ship_company_name);
		$address->setAddressLine1($customer->ship_address_1);
		$address->setAddressLine2($customer->ship_address_2);
		$address->setAddressLine3('');
		$address->setStateProvinceCode($customer->ship_state);
		$address->setCity($customer->ship_city);
		$address->setCountryCode(Helper::getcountrycode($customer->ship_country));
		$address->setPostalCode($customer->ship_zip);
// shipmentDigest
// Ptondereau\LaravelUpsApi\UpsApiServiceProvider
		$xav = new \Ups\AddressValidation(env('UPS_ACCESS_KEY'), env('UPS_USER_ID'), env('UPS_PASSWORD'));
		$xav->activateReturnObjectOnValidate(); //This is optional
		try {
			$response = $xav->validate($address, $requestOption = \Ups\AddressValidation::REQUEST_OPTION_ADDRESS_VALIDATION, $maxSuggestion = 15);

			if ($response->isValid()) {
				$validAddress = $response->getValidatedAddress();

				//Show user validated address or update their address with the 'official' address
				//Or do something else helpful...
				echo "<pre>";
				// Dump array with object-arrays
				echo "**********\n";
				print_r($validAddress);
				echo "</pre>";
				
				echo '<iframe width="640" height="480" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.it/maps?q='.$customer->ship_address_1.', '.$customer->ship_state.', '.$customer->ship_zip.'&output=embed"></iframe>';
				$this->getShippingLable($customer);
			}else{
				echo "Not valide address";
			}

			if ($response->isAmbiguous()) {
				$candidateAddresses = $response->getCandidateAddressList();
				foreach($candidateAddresses as $address) {
					//Present user with list of candidate addresses so they can pick the correct one
					echo "<pre>";
					echo "isAmbiguous+++++++++++\n";
					// Dump array with object-arrays
					print_r($address);
					echo "</pre>";
				}
			}
			dd($request->all(),$customer, $address);

		} catch (Exception $e) {
			var_dump($e);
		}
	}
	
	// $shipper->setShipperNumber('XX');
    public function getShippingLable(Request $request){	
// UPS Expedited Mail Innovations   M4 (service label)
// Irregulars 
// OZS 1 to < 16 (48)
// Top will show forward service ( Endorsement )
// Cost center 
// Package ID
// 92748 (Referance)
// 
    	$customer = Customer::where('order_id', $order_id)
			    	->where('is_deleted', 0)
			    	->first();
    	// Start shipment
    	$shipment = new \Ups\Entity\Shipment();
    	
    	// Set shipper
    	$shipper = $shipment->getShipper();
    	$shipper->setShipperNumber(env('SHIPPER_NUMBER'));
    	$shipper->setName('Deal to win');
    	$shipper->setAttentionName('Pablo');
    	$shipperAddress = $shipper->getAddress();
    	$shipperAddress->setAddressLine1('575 Underhill Blvd');
    	$shipperAddress->setPostalCode('11791');
    	$shipperAddress->setCity('Syosset');
    	$shipperAddress->setCountryCode('US');
    	$shipperAddress->setStateProvinceCode('NY');
    	$shipper->setAddress($shipperAddress);
    	$shipper->setEmailAddress('shlomi@monogramonline.com ');
    	$shipper->setPhoneNumber('718-609-1165');
    	$shipment->setShipper($shipper);
    	
    	// $address->setAttentionName($customer->ship_full_name);
    	// $address->setBuildingName($customer->ship_company_name);
    	// $address->setAddressLine1($customer->ship_address_1);
    	// $address->setAddressLine2($customer->ship_address_2);
    	// $address->setAddressLine3('');
    	// $address->setStateProvinceCode($customer->ship_state);
    	// $address->setCity($customer->ship_city);
    	// $address->setCountryCode(Helper::getcountrycode($customer->ship_country));
    	// $address->setPostalCode($customer->ship_zip);
    	// To address
    	$address = new \Ups\Entity\Address();
    	$address->setAddressLine1($customer->ship_address_1);
    	$address->setAddressLine2($customer->ship_address_2);
    	$address->setAddressLine3('');
    	$address->setPostalCode($customer->ship_zip);
    	$address->setCity($customer->ship_city);
    	$address->setCountryCode(Helper::getcountrycode($customer->ship_country));
    	$address->setStateProvinceCode($customer->ship_state);
    	$shipTo = new \Ups\Entity\ShipTo();
    	$shipTo->setAddress($address);
    	$shipTo->setCompanyName($customer->ship_full_name);
    	$shipTo->setAttentionName($customer->ship_full_name);
    	$shipTo->setEmailAddress($customer->ship_email);
    	$shipTo->setPhoneNumber('917-907-1711');
    	$shipment->setShipTo($shipTo);
    	
    	// 		// From address
    	// 		$address = new \Ups\Entity\Address();
    	// 		$address->setAddressLine1('575 Underhill Blvd');
    	// 		$address->setPostalCode('11791');
    	// 		$address->setCity('Syosset');
    	// 		$address->setCountryCode('US');
    	// 		$address->setStateProvinceCode('NY');
    	// 		$shipFrom = new \Ups\Entity\ShipFrom();
    	// 		$shipFrom->setAddress($address);
    	// 		$shipFrom->setName('Monogram-Online');
    	// 		$shipFrom->setAttentionName($shipFrom->getName());
    	// 		$shipFrom->setCompanyName($shipFrom->getName());
    	// 		$shipFrom->setEmailAddress('tarikuli@yahoo.com');
    	// 		$shipFrom->setPhoneNumber('917-907-1711');
    	// 		$shipment->setShipFrom($shipFrom);
    	
    	// 		// Sold to
    	// 		$address = new \Ups\Entity\Address();
    	// 		$address->setAddressLine1('12348 LAX AVE');
    	// 		$address->setPostalCode('11356');
    	// 		$address->setCity('COLLEGE POINT');
    	// 		$address->setCountryCode('US');
    	// 		$address->setStateProvinceCode('NY');
    	// 		$soldTo = new \Ups\Entity\SoldTo;
    	// 		$soldTo->setAddress($address);
    	// 		$soldTo->setAttentionName('Israt Sharmin');
    	// 		$soldTo->setCompanyName($soldTo->getAttentionName());
    	// 		$soldTo->setEmailAddress('ntazmiri@gmail.com');
    	// 		$soldTo->setPhoneNumber('917-421-0533');
    	// 		$shipment->setSoldTo($soldTo);
    	
    	// Set service
    	$service = new \Ups\Entity\Service;
    	$service->setCode(\Ups\Entity\Service::S_GROUND);
    	$service->setDescription($service->getName());
    	$shipment->setService($service);
    	
    	// Mark as a return (if return)
    	$return = false;
    	if ($return) {
    		$returnService = new \Ups\Entity\ReturnService;
    		$returnService->setCode(\Ups\Entity\ReturnService::PRINT_RETURN_LABEL_PRL);
    		$shipment->setReturnService($returnService);
    	}
    	
    	// Set description
    	$shipment->setDescription($customer->order_id.' Gift Item');
    	
    	// Add Package
    	$package = new \Ups\Entity\Package();
    	$package->getPackagingType()->setCode(\Ups\Entity\PackagingType::PT_PACKAGE);
    	$package->getPackageWeight()->setWeight(5);
    	$unit = new \Ups\Entity\UnitOfMeasurement;
    	$unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_LBS);
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
    	$package->setDescription('Gift Item2');
    	
    	// Add this package
    	$shipment->addPackage($package);
    	
    	$order_id = "1651651616";
    	$return_id = "XX00000";
    	// 		// Set Reference Number BarCodeIndicator
    	// 		$referenceNumber = new \Ups\Entity\ReferenceNumber;
    	// 		if ($return) {
    	// 			$referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_RETURN_AUTHORIZATION_NUMBER);
    	// 			$referenceNumber->setValue($return_id);
    	// 		} else {
    	// 			$referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_I																																																	NVOICE_NUMBER);
    		// 			$referenceNumber->setValue($order_id);
    		// // dd($referenceNumber);
    		// 		}
    		// 		$shipment->setReferenceNumber($referenceNumber);
    	
    		// $shipment->Service->Code = '03';
    		// Set payment information
    		$shipment->setPaymentInformation(new \Ups\Entity\PaymentInformation('prepaid', (object)array('AccountNumber' => env('SHIPPER_NUMBER'))));
    	
    		// Ask for negotiated rates (optional)
    		$rateInformation = new \Ups\Entity\RateInformation;
    		$rateInformation->setNegotiatedRatesIndicator(1);
    		$shipment->setRateInformation($rateInformation);
    	
    		// dd($shipment);
    		// GraphicImage
    		// Get shipment info
    		try {
    			// $api = new \Ups\Shipping($accessKey, $userId, $password);
    			$api = new \Ups\Shipping(env('UPS_ACCESS_KEY'), env('UPS_USER_ID'), env('UPS_PASSWORD'));
    			$confirm = $api->confirm(\Ups\Shipping::REQ_VALIDATE, $shipment);
    			// 			var_dump($confirm); // Confirm holds the digest you need to accept the result
    			if ($confirm) {
    				$accept = $api->accept($confirm->ShipmentDigest);
    				$result=$accept;
    				Helper::jewelDebug("Jewel---------------------------");
    				echo "<pre>";
    				var_dump((array) $accept); // Accept holds the label and additional information
    				echo "</pre>";
    				// 				echo '<div> <img style="width: 150mm;  height: auto;" src="data:image/gif;base64,'. $result->PackageResults->LabelImage->GraphicImage. '"/></div>';
    				echo '<div style="height: 150mm;  width: auto;"> <img  height="100%" width="100%" src="data:image/gif;base64,'. $result->PackageResults->LabelImage->GraphicImage. '"/></div>';
    	
    			}
    		} catch (\Exception $e) {
    			echo "<pre>";
    			var_dump($e->getMessage());
    			echo "</pre>";
    		}
    	
    }

}
