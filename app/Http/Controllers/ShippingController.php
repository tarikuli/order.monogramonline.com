<?php

namespace App\Http\Controllers;

use App\Item;
use App\Ship;
use Illuminate\Http\Request;

use App\Note;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Monogram\Helper;
use Illuminate\Support\Facades\Auth;

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
		$ships = Ship::with('item.product')
					 ->where('is_deleted', 0)
					 ->searchTrackingNumberAssigned($request->get('shipped'))
					 ->searchCriteria($request->get('search_for_first'), $request->get('search_in_first'))
					 ->searchCriteria($request->get('search_for_second'), $request->get('search_in_second'))
					 ->searchWithinDate($request->get('start_date'), $request->get('end_date'))
					 ->latest('postmark_date')
// 					 ->toSql();
					 ->paginate(10);
 return $ships;

		$counter = Ship::where('is_deleted', 0)
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

	public function removeTrackingNumber (Request $request)
	{
		if(!$request->has('order_number')){
			return redirect()
			->back()
			->withErrors([
					'error' => 'No Order Number found',
			]);
		}

		$order_number = $request->get('order_number');
		$tracking_numbers = $request->get('tracking_numbers', [ ]);
		if ( count($tracking_numbers) ) {

			Ship::whereIn('tracking_number', $tracking_numbers)
				->update([
				'tracking_number' => null,
			]);

			Item::whereIn('tracking_number', $tracking_numbers)
				->update([
				'tracking_number' => null,
			]);

			// Add note history by order id
			$note = new Note();
			$note->note_text = "Back to Temp Shipping station for Update Tracking# ".implode(", ",$tracking_numbers);
			$note->order_id = $order_number;
			$note->user_id = Auth::user()->id;
			$note->save();
		}

		return redirect()
			->back()
			->with('success', "Items successfully moved to shipping list");
	}

	public function updateTrackingNumber(Request $request)
	{
		$order_number = $request->get('order_number_update');
		$tracking_number_update = $request->get('tracking_number_update');
		if ( $order_number ) {

			Ship::where('order_number', $order_number)
			->update([
				'tracking_number' => $tracking_number_update,
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
}
