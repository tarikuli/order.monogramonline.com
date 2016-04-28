<?php

namespace App\Http\Controllers;

use App\Ship;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ShippingController extends Controller
{
	public static $search_in = [
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
		$ships = Ship::where('is_deleted', 0)
					 ->searchCriteria($request->get('search_for_first'), $request->get('search_in_first'))
					 ->searchCriteria($request->get('search_for_second'), $request->get('search_in_second'))
					 ->searchWithinDate($request->get('start_date'), $request->get('end_date'))
					 ->orderBy('unique_order_id')
					 ->paginate(50);

		return view('shipping.index', compact('ships', 'request'))->with('search_in', static::$search_in);
	}
}
