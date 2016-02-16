<?php

namespace App\Http\Controllers;

use App\Rule;
use App\RuleAction;
use App\RuleTrigger;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;

class RuleController extends Controller
{
	public $relations = [
		'<',
		'<=',
		'=',
		'>',
		'>=',
		'IN',
	];

	private $trigger_parameters = [
		''     => 'Select Parameter',
		'VAL'  => 'Items Value ($)',
		'OT'   => 'Order total ($)',
		'NUM'  => 'Number of items',
		'DOM'  => 'Domestic/International',
		'WGT'  => 'Weight (Lbs.)',
		'SHIP' => 'Selected shipping method by customer',
		'STAT' => 'Ship to state list',
		'SKU'  => 'SKUs list',
		'MKT'  => 'Store',
	];

	private $action_parameters = [
		''    => 'Select Parameter',
		'CAR' => 'Carrier',
		'CLS' => 'Shipping class',
		'INS' => 'Insurance',
		'PKG' => 'Package shape',
		'SIG' => 'Signature Confirmation',
		'ADW' => 'Add weight (Oz)',
	];

	private $trigger_parameter_keys = [
		"",
		"VAL",
		"OT",
		"NUM",
		"DOM",
		"WGT",
		"SHIP",
		"STAT",
		"SKU",
		"MKT",
	];

	private $action_parameter_keys = [
		"",
		"CAR",
		"CLS",
		"INS",
		"PKG",
		"SIG",
		"ADW",
	];


	public function index ()
	{
		$rules = Rule::where('is_deleted', 0)
					 ->orderBy(DB::raw('rule_display_order + 0'), 'asc')
					 ->paginate(50);
		$suggested_display_order = $this->next_display_order_value();

		return view('rules.index', compact('rules', 'suggested_display_order'));
	}

	public function getAddRule ()
	{

	}

	public function create ()
	{
		//
	}

	public function store (Request $request)
	{
		$rule_name = $request->get('rule_name');
		if ( !$rule_name ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([ 'error' => 'Rule name cannot be empty', ]));
		}

		$rule = new Rule();
		$rule->rule_name = $request->get('rule_name');
		$rule->rule_display_order = $request->get('rule_display_order') ? intval($request->get('rule_display_order')) : $this->next_display_order_value();
		$rule->save();

		return redirect(url('rules'));
	}

	public function show ($id)
	{
		$rule = Rule::with('triggers', 'actions')
					->find($id);
		if ( !$rule ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([ 'error' => 'Invalid rule selected', ]));
		}
		$trigger_parameters = $this->trigger_parameters;
		$action_parameters = $this->action_parameters;
		$trigger_relations = array_combine($this->relations, $this->relations);
		$that = $this;

		return view('rules.show', compact('rule', 'trigger_parameters', 'action_parameters', 'trigger_relations', 'that'));
	}

	public function edit ($id)
	{
	}

	public function update (Request $request, $id)
	{
		#return $request->all();
		$rule_name = $request->get('rule_name');
		if ( !$rule_name ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([ 'error' => 'Rule name cannot be empty', ]));
		}

		$rule = Rule::find($id);
		if ( !$rule ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([ 'error' => 'Invalid rule selected', ]));
		}

		$rule->rule_name = $request->get('rule_name');
		$rule->rule_display_order = $request->get('rule_display_order') ? intval($request->get('rule_display_order')) : $this->next_display_order_value();
		$rule->save();

		return redirect(url('rules'));
	}

	public function destroy ($id)
	{
		$rule = Rule::find($id);
		if ( !$rule ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([ 'error' => 'Invalid rule selected', ]));
		}
		$rule->is_deleted = 1;
		$rule->save();

		return redirect(url('rules'));
	}

	private function max_display_order_inserted ()
	{
		$rule = Rule::where('is_deleted', 0)
					->orderBy(DB::raw('rule_display_order + 0'), 'desc')
					->first();
		if ( $rule ) {
			return intval($rule->rule_display_order);
		}

		return 0;
	}

	private function next_display_order_value ()
	{
		return $this->max_display_order_inserted() + 1;
	}

	public function bulk_update (Request $request, $id)
	{
		$rule = Rule::find($id);
		if ( !$rule ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([ 'error' => 'Invalid rule selected', ]));
		}

		RuleTrigger::where('rule_id', $id)
				   ->delete();
		RuleAction::where('rule_id', $id)
				  ->delete();

		$index = 0;
		if ( $request->exists('trigger_type') ) {
			foreach ( $request->get('trigger_type') as $trigger_parameter ) {
				$rule_trigger_parameter = $trigger_parameter;
				$rule_trigger_relation = $request->get('trigger_relation')[$index];
				$rule_trigger_value = $request->get('trigger_value')[$index];
				if ( $rule_trigger_value ) {
					$rule_trigger = new RuleTrigger();
					$rule_trigger->rule_id = $rule->id;
					$rule_trigger->rule_trigger_parameter = $rule_trigger_parameter;
					$rule_trigger->rule_trigger_relation = $rule_trigger_relation;
					$rule_trigger->rule_trigger_value = $rule_trigger_value;
					$rule_trigger->save();
				}
				$index++;
			}
		}

		$index = 0;
		if ( $request->exists('action_type') ) {
			foreach ( $request->get('action_type') as $action_parameter ) {
				$rule_action_parameter = $action_parameter;
				$rule_action_value = $request->get('action_value')[$index];
				if ( $rule_action_value ) {
					$rule_action = new RuleAction();
					$rule_action->rule_id = $rule->id;
					$rule_action->rule_action_parameter = $rule_action_parameter;
					$rule_action->rule_action_value = $rule_action_value;
					$rule_action->save();
				}
				$index++;
			}
		}

		Session::flash('success', sprintf("Rule <b>%s</b> is updated successfully", $rule->rule_name));

		return redirect(url('rules'));
	}

	public function parameter_option (Request $request)
	{
		$option = $request->get('option');

		return $this->get_view_for_trigger_option($option);
	}

	public function get_view_for_trigger_option ($option, $value = null)
	{
		$domestic = [
			'YES' => 'Domestic',
			'NO' => 'International',
		];

		$selected_shipping_method_by_customer = [


			"EXPRESS MAIL"                        => 'EXPRESS MAIL',
			"USPS 1st class Mail"                 => 'USPS 1st class Mail',
			"USPS express"                        => 'USPS express',
			"USPS EXpress Mail"                   => 'USPS EXpress Mail',
			"USPS FIRST CLASS MAIL"               => 'USPS FIRST CLASS MAIL',
			"USPS FIRST CLASS MAIL w/o TRACKING"  => 'USPS FIRST CLASS MAIL w/o TRACKING',
			"USPS FIRST CLASS MAIL with TRACKING" => 'USPS FIRST CLASS MAIL with TRACKING',
			"USPS PRIORITY "                      => 'USPS PRIORITY',
			"USPS Priority Mail"                  => 'USPS Priority Mail',
			"USPS Priority with TRACKING"         => 'USPS Priority with TRACKING',
			"USPS Proirity MAIL"                  => 'USPS Proirity MAIL',
		];

		$store = [

			"amz-1080747"                    => 'Amazon/Monogram Online',
			"ebay-personalizedjewelrycenter" => 'eBay/personalizedjewelrycenter',
			"yhst-128796189915726"           => 'MonogramOnline.com',
			"yhst-132060549835833"           => 'ShopOnlineDeals.com',
			"wh-265"                         => 'WH/Monogramonline',
			"FB-265"                         => 'Facebook',
			"monogrammfg"                    => 'Etsy/Monog',
			"micalidesign"                   => 'Etsy/MICALIDesign',
			"originalpd"                     => 'Etsy/OriginalPd',
			"WYNnecklace"                    => 'Etsy/WYNnecklace',
		];

		if ( $option == '' || $option == 'VAL' || $option == 'OT' || $option == 'NUM' || $option == 'WGT' || $option == 'STAT' || $option == 'SKU' ) {
			return view('rules.select_parameter_text')->with('value', $value);
		} elseif ( $option == 'DOM' ) {
			return view('rules.select_parameter_domestic')
				->with('domestic', $domestic)
				->with('value', $value);
		} elseif ( $option == 'SHIP' ) {
			return view('rules.selected_shipping_method_by_customer')
				->with('selected_shipping_method_by_customer', $selected_shipping_method_by_customer)
				->with('value', $value);
		} elseif ( $option == 'MKT' ) {
			return view('rules.selected_store')
				->with('store', $store)
				->with('value', $value);
		}

	}

	public function rule_action (Request $request)
	{
		$option = $request->get('option');

		return $this->get_view_for_action($option);
	}

	public function get_view_for_action ($option, $value = null)
	{
		$carrier = [
			"USPS"     => 'USPS',
			"UPS"      => 'UPS',
			"FedEx"    => 'FedEx',
			"Express1" => 'EXP1',
			"DHL"      => 'DHL',
		];
		$shipping_class = [
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
		$insurance = [
			"ON"         => 'ON',
			"UspsOnline" => 'USPS Online',
			"OFF"        => 'OFF',
			"ENDICIA"    => 'ENDICIA',
			"U-PIC"      => 'U-PIC',
		];
		$package_shape = [
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
		$signature_confirmation = [
			"OFF" => 'NO',
			"ON"  => 'YES',
			"ADL" => 'Adult (UPS/FedEx)',
		];

		if ( $option == '' || $option == 'ADW' ) {
			return view('rules.select_action_text')->with('value', $value);
		} elseif ( $option == 'CAR' ) {
			return view('rules.action_carrier')
				->with('carrier', $carrier)
				->with('value', $value);
		} elseif ( $option == 'CLS' ) {
			return view('rules.action_shipping_class')
				->with('shipping_class', $shipping_class)
				->with('value', $value);
		} elseif ( $option == 'INS' ) {
			return view('rules.action_insurance')
				->with('insurance', $insurance)
				->with('value', $value);
		} elseif ( $option == 'PKG' ) {
			return view('rules.action_package_shape')
				->with('package_shape', $package_shape)
				->with('value', $value);
		} elseif ( $option == 'SIG' ) {
			return view('rules.action_signature_confirmation')
				->with('signature_confirmation', $signature_confirmation)
				->with('value', $value);
		}

	}
}
