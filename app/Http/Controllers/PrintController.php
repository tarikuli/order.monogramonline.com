<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
		$order = Order::find($id);
		if ( !$order ) {
			return view('errors.404');
		}

		return $order;
	}
}
