<?php namespace Monogram;

use App\BatchRoute;
use App\Customer;
use App\Item;
use App\MasterCategory;
use App\Option;
use App\Order;
use App\Parameter;
use App\Product;
use App\Rule;
use App\RuleAction;
use App\RuleTrigger;
use App\Setting;
use App\Ship;
use App\Station;
use App\StationLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DNS1D;
use Illuminate\Support\Facades\Log;

class Helper
{
	public static $column_names = [ ];
	public static $columns = [ ];
	public static $error = '';
	public static $SKU_CONVERSION_EXTRA_COLUMNS = [
		"ID Catalog",
		'Parent SKU',
		'Child SKU',
		"Graphic SKU",
		'Allow mixing',
		'Batch route',
	];

	private static $carrier = [
		"USPS"     => 'USPS',
		"UPS"      => 'UPS',
		"FedEx"    => 'FedEx',
		"Express1" => 'EXP1',
		"DHL"      => 'DHL',
	];
	private static $shipping_class = [
		'C1'                                    => '--- USPS DOM ---',
		'Express'                               => 'Express Mail',
		'PriorityExpress'                       => 'Priority Mail Express',
		'First'                                 => 'First Class Mail',
		'LibraryMail'                           => 'Package Services, Library Mail',
		'MediaMail'                             => 'Package Services, Media Mail',
		'StandardPost'                          => 'Package Services, StandardPost',
		'Priority'                              => 'Priority Mail',
		'ParcelSelect'                          => 'Parcel Select',
		'ParcelSelectMI'                        => 'UPS MI via Endicia',
		'DHLGMSMParcelPlusExpedited'            => 'DHLGM SM Parcel Plus Expedited',
		'DHLGMSMParcelPlusGround'               => 'DHLGM SM Parcel Plus Ground',
		'DHLGMSMParcelsExpedited'               => 'DHLGM SM Parcels Expedited',
		'DHLGMSMParcelsGround'                  => 'DHLGM SM Parcels Ground',
		'D1'                                    => '--- USPS INT ---',
		'ExpressMailInternational'              => 'Express Mail International',
		'FirstClassPackageInternationalService' => 'First Class Package International',
		'PriorityMailInternational'             => 'Priority Mail International',
		'GXG'                                   => 'Global Express Guaranteed',
		'IPA'                                   => 'International Priority Airmail',
		'ISAL'                                  => 'International Surface Air Lift',
		'CONSINTL'                              => 'Consolidator International',
		'CommercialePacket'                     => 'Commercial E-Packet',
		'C2'                                    => '--- UPS DOM ---',
		'02'                                    => 'UPS 2nd Day Air',
		'03'                                    => 'UPS Ground',
		'12'                                    => 'UPS 3 Day Select',
		'13'                                    => 'UPS Next Day Air Saver',
		'14'                                    => 'UPS Next Day Air Early A.M. SM',
		'01'                                    => 'UPS Next Day Air',
		'59'                                    => 'UPS Second Day Air A.M.',
		'65'                                    => 'UPS Saver',
		'M4'                                    => 'UPS Expedited Mail Innovations',
		'D2'                                    => '--- UPS INT ---',
		'07'                                    => 'UPS Worldwide Express',
		'08'                                    => 'UPS Worldwide Expedited',
		'54'                                    => 'UPS Worldwide Express Plus',
		'11'                                    => 'UPS Standard',
		'M5'                                    => 'UPS Priority Mail Innovations',
		'M6'                                    => 'UPS Economy Mail Innovations',
		'C3'                                    => '--- FedEx DOM ---',
		'PRIORITY_OVERNIGHT'                    => 'PRIORITY OVERNIGHT',
		'STANDARD_OVERNIGHT'                    => 'STANDARD OVERNIGHT',
		'FEDEX_2_DAY'                           => 'FEDEX 2 DAY',
		'FEDEX_EXPRESS_SAVER'                   => 'FEDEX EXPRESS SAVER',
		'FIRST_OVERNIGHT'                       => 'FIRST OVERNIGHT',
		'FEDEX_GROUND'                          => 'FEDEX_GROUND',
		'GROUND_HOME_DELIVERY'                  => 'GROUND HOME DELIVERY',
		'SMART_POST'                            => 'SMARTPOST',
		'ONER_PRIORITY_OVERNIGHT'               => 'FedEx One Rate (PRIORITY OVERNIGHT)',
		'ONER_STANDARD_OVERNIGHT'               => 'FedEx One Rate (STANDARD OVERNIGHT)',
		'ONER_FEDEX_2_DAY'                      => 'FedEx One Rate (FEDEX 2 DAY)',
		'ONER_FEDEX_EXPRESS_SAVER'              => 'FedEx One Rate (FEDEX EXPRESS SAVER)',
		'ONER_FIRST_OVERNIGHT'                  => 'FedEx One Rate (FIRST OVERNIGHT)',
		'D3'                                    => '--- FedEx INT ---',
		'INTERNATIONAL_ECONOMY'                 => 'INTERNATIONAL ECONOMY',
		'INTERNATIONAL_FIRST'                   => 'INTERNATIONAL FIRST',
		'INTERNATIONAL_PRIORITY'                => 'INTERNATIONAL PRIORITY',
		'D4'                                    => '--- DHL INT ---',
		'P'                                     => 'Worlddwide Express',
		'H'                                     => 'ECONOMY SELECT',
		'Y'                                     => 'EXPRESS 12:00',
	];
	private static $insurance = [
		"ON"         => 'ON',
		"UspsOnline" => 'USPS Online',
		"OFF"        => 'OFF',
		"ENDICIA"    => 'ENDICIA',
		"U-PIC"      => 'U-PIC',
	];
	private static $package_shape = [
		'C1'                     => '--- USPS ---',
		'Parcel'                 => 'Parcel',
		'FlatRateBox'            => 'Flat Rate Box',
		'MediumFlatRateBox'      => 'Medium Flat Rate Box',
		'FlatRateEnvelope'       => 'Flat Rate Envelope',
		'SmallFlatRateEnvelope'  => 'Flat Rate Small Envelope',
		'IrregularParcel'        => 'Irregular Parcel',
		'LargeFlatRateBox'       => 'Large Flat Rate Box',
		'LargeParcel'            => 'Large Parcel',
		'OversizedParcel'        => 'Oversized Parcel',
		'SmallFlatRateBox'       => 'Small Flat Rate Box',
		'FlatRatePaddedEnvelope' => 'Flat Rate Padded Envelope',
		'FlatRateLegalEnvelope'  => 'Legal Flat Rate Envelope',
		'Letter-RG'              => 'Envelope / rigid object',
		'Card'                   => 'Card',
		'Letter'                 => 'Letter',
		'Flat'                   => 'Flat',
		'RegionalRateBoxA'       => 'Regional Rate Box A',
		'RegionalRateBoxB'       => 'Regional Rate Box B',
		'RegionalRateBoxC'       => 'Regional Rate Box C',
		'C2'                     => '--- UPS ---',
		'01'                     => 'UPS Letter',
		'02'                     => 'Customer Supplied Package',
		'03'                     => 'Tube',
		'04'                     => 'PAK',
		'21'                     => 'UPS Express Box',
		'24'                     => 'UPS 25KG Box',
		'25'                     => 'UPS 10KG Box',
		'2a'                     => 'Small Express Box',
		'2b'                     => 'Medium Express Box',
		'2c'                     => 'Large Express Box',
		'61'                     => 'MI Machinables',
		'62'                     => 'MI Irregulars',
		'63'                     => 'MI Parcel Post',
		'65'                     => 'MI Media Mail',
		'C3'                     => '--- FedEx ---',
		'FEDEX_BOX'              => 'FedEx Box',
		'FEDEX_ENVELOPE'         => 'FedEx Envelope',
		'FEDEX_PAK'              => 'FedEx Pak',
		'FEDEX_TUBE'             => 'FedEx Tube',
		'FEDEX_EXTRA_LARGE_BOX'  => 'FedEx XL Box',
		'FEDEX_LARGE_BOX'        => 'FedEx Large Box',
		'FEDEX_MEDIUM_BOX'       => 'FedEx Medium Box',
		'FEDEX_SMALL_BOX'        => 'FedEx Small Box',
		'YOUR_PACKAGING'         => 'YOUR PACKAGING',
		'C4'                     => '--- DHL ---',
		'EE'                     => 'DHL Express Envelope',
		'PA'                     => 'Parcel',
		'YP'                     => 'Your packaging',
	];
	private static $signature_confirmation = [
		"OFF" => 'NO',
		"ON"  => 'YES',
		"ADL" => 'Adult (UPS/FedEx)',
	];
	private static $statuses = [
		'1' => 'To be processed',
		'2' => 'In progress',
		'3' => 'Complete',
	];
	private static $batchStatuses = [
		'not started' => 'Not started',
		'active'      => 'Active',
		'complete'    => "Complete",
	];

	public static $specSheetSampleDataArray = [
		'Yes'         => 'Yes',
		'No'          => 'No',
		'Redo Sample' => 'Redo Sample',
		'Complete'    => 'Complete',
	];

	public static $webImageStatus = [
		'Select web image status',
		'Temporary',
		'Create Web Image',
		'Update Web Image',
		'Web Image Approval',
		'Publish Web image',
		'Complete - Final Image Uploaded',
	];

	public static $shippingStations = [
		'J-SHP',
		'R-SHP',
		'S-SHP',
		'H-SHP',
	];

	public static function tracking_number_formatter ($shippingInfo, $new_line_formatter = '<br/>')
	{
		if ( !$shippingInfo ) {
			return;
		}

		$tracking_numbers = $shippingInfo->lists('tracking_number')
										 ->toArray();

		return implode($new_line_formatter, $tracking_numbers);
	}

	public static function scrollableCheckbox ($name, $options, $value = null)
	{
		$container = <<<Container
<div style="height: 12em; width: 20em; overflow: auto;">
				<div class="checkbox">
Container;

		foreach ( $options as $optionKey => $optionValue ) {
			$checked = '';
			if ( is_array($value) ) {
				$values = array_values($value);
				if ( in_array($optionKey, $values) ) {
					$checked = 'checked';
				}
			} elseif ( !is_null($value) ) {
				if ( $optionKey == $value ) {
					$checked = 'checked';
				}
			}
			$input = <<<INPUT
					<label>
						<input type="checkbox" value="{$optionKey}" name="{$name}" {$checked}>
						{$optionValue}
					</label>
INPUT;
			$container .= $input;
		}
		$container .= <<<APPEND
				</div>
</div>
APPEND;

		return $container;
	}

	public static function getProductCount ($category_id)
	{
		#return Product::where('product_master_category', $category_id)->count();
		return Product::searchMasterCategory($category_id)
					  ->count();
	}

	public static function orderIdFormatter ($order, $column_name = 'id')
	{
		return sprintf("%06d", $order->$column_name);
	}

	public static function orderNameFormatter ($order)
	{
		if ( strpos($order->order_id, "yhst-128796189915726") !== false ) {
			return "M-" . $order->short_order;
		} else {
			return "S-" . $order->short_order;
		}
	}

	public static function getAllOrdersFromOrderId ($order_id)
	{
		// get all the items of an order,
		// order by reached shipping station flag in descending order

		// joining is done, because, one or more items may be added on the shipping table before
		$items = Item::where('order_id', $order_id)
					 ->orderBy('items.reached_shipping_station', 'DESC')
					 ->get();
		// if the first item has the value of 1
		// then at least one item reached the shipping stations
		// return the items
		$first_item = $items->first();
		// if ( $first_item->reached_shipping_station == 1 || ( $first_item->reached_shipping_station == 0 && $first_item->item_id == null ) ) {
		if ( $first_item->reached_shipping_station == 1 ) {
			return $items->filter(function ($row) {
				return !Ship::where('item_id', $row->id)
							->first();
			});
		}
		// no item reached the shipping station,
		// return nothing
		return [ ];
	}

	public static function generateShippingUniqueId ($order)
	{
		return sprintf("%s-%s", static::orderNameFormatter($order), Ship::where('order_number', $order->order_id)
																		->count());
	}

	public static function itemsMovedToShippingTable ($order_id)
	{
		// get all the items with the order id
		$items = Item::where('order_id', $order_id)
					 ->get();
		// if any item has reached shipping station
		// then it is not moved to shipping table
		// otherwise, it's moved to shipping station
		return $items->groupBy('reached_shipping_station')
					 ->get(0);
	}

	public static function getHtmlBarcode ($value, $width = 1)
	{
		#return DNS1D::getBarcodeHTML($value, "C39", $width);
		return static::getImageBarcodeSource($value, $width);
	}

	public static function getImageBarcodeSource ($value, $width = 1)
	{
		return '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($value, "C39", $width) . '" alt="barcode"   />';
		#return '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG("4", "C39+") . '" alt="barcode"   />';
	}

	public static function getBatchStatusList ()
	{
		return static::$batchStatuses;
	}

	public static function getBatchStatus ($index = null)
	{
		return $index && array_key_exists($index, static::$batchStatuses) ? static::$batchStatuses[$index] : 'not started';
	}

	public static function getCategoryHierarchy ($category_id, &$holder)
	{
		$category = MasterCategory::find($category_id);
		if ( $category ) {
			$holder->push($category);
			if ( $category->parent != 0 ) {
				self::getCategoryHierarchy($category->parent, $holder);
			}
		}
	}

	public static function jsonTransformer ($json, $separator = "\n")
	{
		/*if ( null === $separator ) {
			$separator = "\n";
		}*/
		$formatted_string = '';
		$json_array = json_decode($json, true);

		if ( $json_array ) {
			foreach ( $json_array as $key => $value ) {
				$formatted_string .= sprintf("%s = %s%s", str_replace("_", " ", $key), $value, $separator);
			}
		}

		return $formatted_string ?: "";
	}

	public static function dateTransformer ($date)
	{
		return date("F j, Y / g:i a", strtotime($date));
	}

	public static function getNextStationName ($batch_route_id, $current_station_name)
	{
		$batch_route_id = $batch_route_id;
		$current_station_name = $current_station_name;
		$current_station = Station::where('station_name', $current_station_name)
								  ->first();
		if ( !$current_station ) {
			return null;
		}
		$current_station_id = $current_station->id;

		$next_stations = DB::select(sprintf("SELECT * FROM batch_route_station WHERE batch_route_id = %d and id > ( SELECT id FROM batch_route_station WHERE batch_route_id = %d AND station_id = %d)", $batch_route_id, $batch_route_id, $current_station_id));

		if ( count($next_stations) ) {
			return Station::find($next_stations[0]->station_id)->station_name;
		} else {
			return null;
		}
	}

	public static function getSupervisorStationName ()
	{
		return Setting::first()->supervisor_station;
	}

	public static function getStationIdFromName ($station_name)
	{
		$station = Station::where('station_name', $station_name)
						  ->where('is_deleted', 0)
						  ->first();

		return $station ? $station->id : null;
	}

	public static function getStationLog ($batch_number, $station_name)
	{
		$station_id = static::getStationIdFromName($station_name);
		if ( $station_id ) {
			$log = StationLog::where('batch_number', $batch_number)
							 ->where('station_id', $station_id)
							 ->first();
			if ( $log ) {
				return substr($log->started_at, 0, 10);
			}

			return "N/A";
		}

		return "N/A";
	}

	public static function getDefaultRouteId ()
	{
		return Setting::first()->default_route_id;
	}

	public static function validateSkuImportFile ($store_id, $row)
	{
		$parameters = Parameter::where('store_id', $store_id)
							   ->where('is_deleted', 0)
							   ->orderBy('id')
							   ->get();
		self::$column_names = array_merge(static::$SKU_CONVERSION_EXTRA_COLUMNS, $parameters->lists('parameter_value')
																							->toArray());
		self::$columns = $parameters->lists('id', 'parameter_value')
									->toArray();
		#$parameters->lists('parameter_value')->toArray()
		$different = array_diff(self::$column_names, $row);
		if ( $different ) {
			self::$error = sprintf("%s columns are not present in uploaded CSV file.", implode(", ", $different));
		}

		return count($parameters) && !$different;
	}

	public static function textToHTMLFormName ($text)
	{
		// double underscore is for protection
		// in case a single underscore found on string won't be replaced
		return str_replace(" ", "_", trim($text));
	}

	public static function htmlFormNameToText ($text)
	{
		return str_replace("_", " ", $text);
	}

	public static function routeThroughStations ($route_id, $station_name = null)
	{
		if ( !$route_id ) {
			return '';
		}
		$route = BatchRoute::with('stations')
						   ->find($route_id);

		$stations = implode(" > ", array_map(function ($elem) {
			return $elem['station_name'];
		}, $route->stations->toArray()));

		if ( $station_name ) {
			$stations = str_replace($station_name, sprintf("<strong>%s</strong>", $station_name), $stations);
		}

		return $stations;
	}

	private static function getDefaultShippingRule ()
	{
		return Setting::first()->default_shipping_rule;
	}

	public static function populateShippingData ($items)
	{
		if ( $items instanceof Item ) {
			// set
			static::setShippingFlag($items);
		} else {
			foreach ( $items as $item ) {
				static::setShippingFlag($item);
			}
		}
	}

	public static function setShippingFlag ($item)
	{
		// set reached_shipping_station to 1, as it reaches the shipping station
		$item->reached_shipping_station = 1;
		$item->save();

		// if all items in a same order does pass shipping stations
		$order_id = $item->order_id;
		$items = Item::with('order')
					 ->where('order_id', $order_id)
					 ->get();

		$reached_shipping_station_count = 0;
		foreach ( $items as $current ) {
			if ( $current->reached_shipping_station ) {
				++$reached_shipping_station_count;
			}
		}

		if ( $items->count() && ( $items->count() == $reached_shipping_station_count ) ) { // move to shipping table
			// get the item id from the shipping table
			$items_exist_in_shipping = Ship::where('order_number', $order_id)
										   ->lists('item_id');
			// filter the item ids those are available in shipping table
			$items = $items->filter(function ($row) use ($items_exist_in_shipping) {
				// return false if the shipping table has the item id
				return $items_exist_in_shipping->contains($row->id) ? false : true;
			});
			#dd($items);
			// generate new order id
			$unique_order_id = static::generateShippingUniqueId($items->first()->order);
			foreach ( $items as $current_item ) {
				// push all the items to shipping table with the unique order id
				static::insertDataIntoShipping($current_item, $unique_order_id);
			}
			/*foreach ( $items->groupBy('order_id') as $order_id => $move_able_items ) {
				$unique_order_id = static::generateShippingUniqueId($item->order);
				foreach ( $move_able_items as $current_item ) {
					static::insertDataIntoShipping($current_item, $unique_order_id);
				}
			}*/
		} elseif ( $items->count() ) {
			// order has more than 0
			// any of the items has not reached the shipping station
			Order::where('order_id', $order_id)
				 ->update([
					 'order_status' => 9,
					 // WAITING FOR ANOTHER PC
				 ]);
		}
	}

	public static function insertDataIntoShipping ($item, $unique_order_id = null)
	{
		$post_value = $item->item_quantity * $item->item_unit_price;
		$sku = $item->item_code;

		$triggers = RuleTrigger::where('rule_trigger_parameter', 'SKU')
							   ->where('rule_trigger_value', 'REGEXP', $sku)#'MS-GFT172'
							   ->get();
		$rule_id = 0;

		if ( $triggers->count() ) {
			$rule_id = $triggers->first()->rule_id;
		} else {
			$rule = Rule::where('id', static::getDefaultShippingRule())
						->first();
			if ( $rule ) {
				$rule_id = $rule->id;
			} else {
				$rule_id = 1;
			}
		}

		$actions = RuleAction::where('rule_id', $rule_id)
							 ->get();

		$product = Product::where('product_model', $sku)
						  ->first();

		$product_weight = 0.0;
		$final_weight = 0.0;
		$add_weight = 0.0;

		if ( $product ) {
			$product_weight = $product->ship_weight;
		}

		$mail_class = '';
		$package_shape = '';
		$tracking_type = '';
		$carrier = 'carrier';

		foreach ( $actions as $action ) {
			// From RuleController@getAction
			if ( $action->rule_action_parameter == 'ADW' ) {
				$add_weight = $action->rule_action_value;
			} elseif ( $action->rule_action_parameter == 'CAR' ) {
				$tracking_type = static::$carrier[$action->rule_action_value];
			} elseif ( $action->rule_action_parameter == 'CLS' ) {
				$mail_class = static::$shipping_class[$action->rule_action_value];
			} elseif ( $action->rule_action_parameter == 'PKG' ) {
				$package_shape = static::$package_shape[$action->rule_action_value];
			}
		}

		$final_weight = $add_weight + $product_weight;

		$order_number = $item->order_id;
		$customer = Customer::where('order_id', $order_number)
							->first();

		$name = sprintf("%s %s", $customer->ship_first_name, $customer->ship_last_name);
		if ( !trim($name) ) {
			$name = sprintf("%s %s", $customer->bill_first_name, $customer->bill_last_name);
		}

		$last_name = $customer->ship_last_name;
		$company = trim($customer->ship_company_name) ? $customer->ship_company_name : $customer->bill_company_name;
		$address_1 = trim($customer->ship_address_1) ? $customer->ship_address_1 : $customer->bill_address_1;
		$address_2 = trim($customer->ship_address_2) ? $customer->ship_address_2 : $customer->bill_address_2;
		$city = trim($customer->ship_city) ? $customer->ship_city : $customer->bill_city;
		$state_city = trim($customer->ship_state) ? $customer->ship_state : $customer->bill_state;
		$postal_code = trim($customer->ship_zip) ? $customer->ship_zip : $customer->bill_zip;
		$country = trim($customer->ship_country) ? $customer->ship_country : $customer->bill_country;
		$email = trim($customer->ship_email) ? $customer->ship_email : $customer->bill_email;
		$phone = trim($customer->ship_phone) ? $customer->ship_phone : $customer->bill_phone;
		$item_id = $item->id;

		#$unique_order_id = sprintf("%06s", $item->order->id);
		$unique_order_id = $unique_order_id ?: static::generateShippingUniqueId($item->order);

		$ship = new Ship();
		$ship->order_number = $order_number;
		$ship->unique_order_id = $unique_order_id;
		$ship->item_id = $item_id;
		$ship->mail_class = $mail_class;
		$ship->package_shape = $package_shape;
		$ship->tracking_type = $tracking_type;
		$ship->post_value = $post_value;
		$ship->name = $name;
		$ship->last_name = $last_name;
		$ship->company = $company;
		$ship->address1 = $address_1;
		$ship->address2 = $address_2;
		$ship->city = $city;
		$ship->state_city = $state_city;
		$ship->postal_code = $postal_code;
		$ship->actual_weight = $product_weight ?: 0;
		$ship->billed_weight = $final_weight;
		$ship->country = $country;
		$ship->email = $email;
		$ship->phone = $phone;
		$ship->carrier = $carrier;
		$ship->save();
	}

	public static function getItemOrderStatusArray ()
	{
		return static::$statuses;
	}

	public static function getItemOrderStatus ($index)
	{
		return $index && array_key_exists($index, static::$statuses) ? static::$statuses[$index] : null;
	}

	public static function getItemCount ($items)
	{
		$total = 0;
		foreach ( $items as $item ) {
			$total += $item->item_quantity;
		}

		return $total;
	}

	public static function getUniquenessRule ($model, $id, $field)
	{
		return sprintf("uniqueness_in_model:%s,%d,%s", $model, $id, $field);
	}

	/***
	 * Function for count Possible Batches.
	 */
	public static function countPossibleBatches ()
	{
		// items, orders, parameter_options
		return Item::join('parameter_options', 'items.child_sku', '=', 'parameter_options.child_sku')
				   ->join('orders', 'items.order_id', '=', 'orders.order_id')
				   ->where('items.batch_number', '=', '0')
				   ->whereNull('items.tracking_number')
				   ->where('items.is_deleted', '=', '0')
				   ->where('orders.is_deleted', '=', '0')
				   ->whereNotIn('orders.order_status', [
					   3,
					   7,
					   8,
				   ])
				   ->where('parameter_options.batch_route_id', '!=', 115)
				   ->whereNotNull('parameter_options.batch_route_id')
				   ->count();
		// 		->first([DB::raw('COUNT(*) AS countPossobleBatches')]);
		// 		->get([DB::raw('COUNT(*) AS countPossobleBatches')]);
	}

	public static function createAbleBatches ($paginate = false)
	{
		return BatchRoute::with([
			'stations_list',
			'itemGroups' => function ($q) use ($paginate) {
				$joining = $q->join('items', 'items.child_sku', '=', 'parameter_options.child_sku')
							 ->join('orders', 'orders.order_id', '=', 'items.order_id')
							 ->where('orders.is_deleted', 0)
							 ->where('items.batch_number', '0')
							 ->where('items.is_deleted', 0)
							 ->whereNotIn('orders.order_status', [ // don't create batch, if the following order statuses are there
																   3,
																   // On hold
																   7,
																   // returned
																   8,
																   // cancelled
							 ])
							 ->where(function ($query) {
								 return $query->where('parameter_options.batch_route_id', '!=', 115)
											  ->whereNotNull('parameter_options.batch_route_id');
							 })
							 ->addSelect([
								 DB::raw('items.id AS item_table_id'),
								 'items.item_id',
								 'items.item_code',
								 'items.order_id',
								 'items.item_quantity',
								 'items.item_thumb',
								 DB::raw('orders.id as order_table_id'),
								 'orders.order_id',
								 'orders.order_date',
							 ]);

				return $paginate ? $joining->get() : $joining->paginate(10000);
			},
		])
						 ->where('batch_routes.is_deleted', 0)
						 ->where('batch_routes.batch_max_units', '>', 0)
						 ->get();

		/*
		 * PREVIOUS QUERY
		 return BatchRoute::with([
			'stations_list',
			'itemGroups' => function ($q) use ($paginate) {
				$joining = $q->join('items', 'products.product_model', '=', 'items.item_code')
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
							 ]);//->paginate(50000);
				return $paginate ? $joining->get() : $joining->paginate(10000);
			},
		])
						 ->where('batch_routes.is_deleted', 0)
						 ->where('batch_routes.batch_max_units', '>', 0)
						 ->get();*/
	}

	public static function saveStationLog ($items, $new_station_name)
	{
		foreach ( $items as $item ) {
			$station_log = new StationLog();
			$station_log->item_id = $item->id;
			$station_log->batch_number = $item->batch_number;
			$station_log->station_id = Station::where('station_name', $new_station_name)
											  ->first()->id;

			$station_log->started_at = date('Y-m-d', strtotime("now"));
			$station_log->user_id = Auth::user()->id;
			$station_log->save();
		}
	}

	public static function getChildSku ($item)
	{
		// related to parameter options table
		// get the item options from order
		$item_options = json_decode($item->item_option, true);
		// Check is item_options an array
		if ( !is_array($item_options) ) {
			return $item->item_code;
		}

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

		$parameter_options = Option::where('parent_sku', $item->item_code)
								   ->get();

		// get the common in the keys
		$options_in_common = array_intersect($parameter_to_html_form_name, $item_option_keys);
		//generate the new sku
		$child_sku = static::generateChildSKU($options_in_common, $parameter_options, $item, $store_id);

		return $child_sku;
	}

	private static function generateChildSKU ($matches, $parameter_options, $item, $store_id)
	{
		// parameter options is an array of rows
		$item_options = json_decode($item->item_option, true);

		// 20160515 remove (+ character from child_sku
		$explode_values = [ ];
		foreach ( $item_options as $item_key => $item_value ) {
			$explode_values = explode("(", $item_value);
			if ( count($explode_values) > 0 ) {
				$item_options[$item_key] = $explode_values[0];
			}
		}

		foreach ( $parameter_options as $option ) {
			// item options has replaced space with underscore
			// parameter options has spaces intact
			$parameter_option_json_decoded = json_decode($option->parameter_option, true);
			$match_broken = false;
			foreach ( $matches as $match ) {
				// matches are underscored
				// i,e: form name
				// convert to text for parameter options
				// if ( $parameter_option_json_decoded[Helper::htmlFormNameToText($match)] != $item_options[$match] ) {
				if ( !array_key_exists(Helper::htmlFormNameToText($match), $parameter_option_json_decoded) || !array_key_exists($match, $item_options) || ( $parameter_option_json_decoded[Helper::htmlFormNameToText($match)] != $item_options[$match] ) ) {
					$match_broken = true;
					break;
				}
			}
			// if the inner loop
			// executes thoroughly
			// then the match_broken will be false always
			// break the outer loop
			// return the value
			// if the match is not broken.
			// if all the matches are found
			// will not
			if ( !$match_broken ) {
				return $option->child_sku;
				//break;
			}
		}
		// child sku suggestion
		// no option was found matching
		// suggest a new child sku
		$child_sku_postfix = implode("-", array_map(function ($node) use ($item_options) {
			// replace the spaces with empty string
			// make the string lower
			// and the values from the item options
			return str_replace(" ", "", strtolower($item_options[$node]));
		}, $matches));
		$child_sku = empty( $child_sku_postfix ) ? $item->item_code : sprintf("%s-%s", $item->item_code, $child_sku_postfix);
		// should have to match the previous check.
		// again check if the child sku is present or not

		$option = Option::where('child_sku', $child_sku)
						->first();
		if ( !$option ) {
			$option = new Option();
			$option->child_sku = $child_sku;
		} else {
			return $option->child_sku;
		}
		// no child sku was found
		// insert into database

		$option->store_id = $store_id;
		$option->unique_row_value = static::generateUniqueRowId();
		$option->id_catalog = $item->item_id;
		$option->parent_sku = $item->item_code;
		$option->graphic_sku = 'NeedGraphicFile';
		$option->allow_mixing = 1;
		$option->batch_route_id = static::getDefaultRouteId();
		$option_array = [ ];
		// add the found parameters
		foreach ( $matches as $match ) {
			$option_array[static::htmlFormNameToText($match)] = $item_options[$match];
		}

		$option->parameter_option = json_encode($option_array);
		$option->save();

		return $child_sku;
	}

	public static function generateUniqueRowId ()
	{
		return sprintf("%s_%s", strtotime("now"), str_random(5));
	}

	public static function specialCharsRemover ($text)
	{
		$specialChars = [
			':',
			'&nbsp;',
		];

		return str_replace($specialChars, "", trim($text));
	}

	public static function crawledOptionValueSplitter ($options)
	{
		return array_filter($options, function ($value) {
			return strtolower(trim($value['text'])) !== "please select";
		});
	}

	public static function getOnlyValuesByKey ($data, $key)
	{
		$values = array_map(function ($node) use ($key) {
			return $node[$key];
		}, $data);

		return array_combine($values, $values);
	}

	public static function generateChildSKUCombination (array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
	{
		$keys = array_keys($data);
		if ( isset( $value ) === true ) {
			#$value = str_replace(" ", "", strtolower($value));
			array_push($group, $value);
		}

		if ( $i >= count($data) ) {
			$array = [
				'nodes'      => $group,
				'suggestion' => implode("-", array_map(function ($value) {
					return $value = str_replace(" ", "", strtolower($value));
				}, $group)),
			];
			array_push($all, $array);
		} else {
			$currentKey = $keys[$i];
			$currentElement = $data[$currentKey];
			foreach ( $currentElement as $val ) {
				static::generateChildSKUCombination($data, $all, $group, $val, $i + 1);
			}
		}

		return $all;
	}

	public static function getProductInformation ($id_catalog)
	{
		// generate the url
		$url = url(sprintf("/crawl?id_catalog=%s", $id_catalog));
		// pass to the phantom class to get the data
		$phantom = new Phantom($url);
		// generate response
		$response = $phantom->request()
							->getResponse();
		// instantiate the dom reader
		$reader = new DOMReader($response);
		//
		$crawled_data = json_decode($reader->readCrawledData(), true);

		return $crawled_data;
	}
}