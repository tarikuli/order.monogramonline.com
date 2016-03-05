<?php

namespace App\Http\Controllers;

use App\Order;
use App\Purchase;
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
		$batches = $request->exists('batch_number') ? array_filter($request->get('batch_number')) : null;
		/*https://www.4psitelink.com/setup/batch_print.php?ad[]=22602*/
		if ( !$batches || !is_array($batches) ) {
			return view('errors.404');
		}

		return 'array';
	}
}