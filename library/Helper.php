<?php namespace Monogram;

use App\BatchRoute;
use App\Customer;
use App\Item;
use App\Parameter;
use App\Product;
use App\Rule;
use App\RuleAction;
use App\RuleTrigger;
use App\Setting;
use App\Ship;
use App\Station;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class Helper
{
	public static $column_names = [ ];
	public static $columns = [ ];

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

	public static $shippingStations = [
		'J-SHP',
		'R-SHP',
		'S-SHP',
		'H-SHP',
	];

	public static function jsonTransformer ($json, $separator = null)
	{
		if ( null === $separator ) {
			$separator = "\n";
		}
		$formatted_string = '';

		foreach ( json_decode($json, true) as $key => $value ) {
			$formatted_string .= sprintf("%s = %s%s", str_replace("_", " ", $key), $value, $separator);
		}

		return $formatted_string;
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

	public static function getDefaultRouteId ()
	{
		return Setting::first()->default_route_id;
	}

	public static function validateSkuImportFile ($store_id, $row)
	{
		$parameters = Parameter::where('store_id', $store_id)
							   ->where('is_deleted', 0)
							   ->get();
		self::$column_names = $parameters->lists('parameter_value')
										 ->toArray();
		self::$columns = $parameters->lists('id', 'parameter_value')
									->toArray();

		#$parameters->lists('parameter_value')->toArray()
		return count($parameters) && ( self::$column_names == $row );
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
			static::insertDataIntoShipping($items);
		} else {
			foreach ( $items as $item ) {
				static::insertDataIntoShipping($item);
			}
		}
	}

	private static function insertDataIntoShipping ($item)
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

		$ship = new Ship();
		$ship->order_number = $order_number;
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
		$ship->actual_weight = $product_weight;
		$ship->billed_weight = $final_weight;
		$ship->country = $country;
		$ship->email = $email;
		$ship->phone = $phone;
		$ship->carrier = $carrier;
		$ship->save();
	}
}