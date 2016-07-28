<?php

namespace App\Http\Controllers;

use App\Product;
use App\Purchase;
use App\PurchaseProduct;
use App\PurchasedInvProducts;
use App\Vendor;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Monogram\Helper;

class PurchaseController extends Controller
{
	public function index ()
	{
		$purchases = Purchase::with('products', 'vendor_details')
							 ->where('is_deleted', 0)
							 ->latest()
							 ->paginate(50);
		$count = 1;

		return view('purchases.index', compact('purchases', 'count'));
	}

	public function create (Request $request)
	{
		$vendors = Vendor::where('is_deleted', 0)
						 ->lists('vendor_name', 'id')
						 ->prepend('Select a vendor', 0);

// 		$products = Product::where('is_deleted', 0)
// 						   ->lists('product_name', 'id')
// 						   ->prepend('Select a product', 0);

		$products = PurchasedInvProducts::where('is_deleted', 0)
							->lists('name', 'code')
							->prepend('Select a product', 0);

		$products = Helper::selectSort($products);

		return view('purchases.create', compact('vendors', 'products', 'request'));
	}

	public function store (Requests\PurchaseCreateRequest $request)
	{
		$purchase = new Purchase();
		$purchase->vendor_id = $request->get('vendor_id');
		$purchase->lc_number = trim($request->get('lc_number'));
		$purchase->insurance_number = trim($request->get('insurance_number'));
		$purchase->save();

		// one purchase may have many products
		$index = 0;
		// index is used to grab the array of products with details.
		$product_ids = $request->get('product_code');
		$quantities = $request->get('quantity');
		$prices = $request->get('price');

		foreach ( $product_ids as $product_code ) {
			// check if product id exists
// 			$product = Product::find($product_id);
			$product = PurchasedInvProducts::where('is_deleted', 0)
											->where('code',$product_code)
											->get();

			Helper::jewelDebug($product_code);
			Helper::jewelDebug($product);

			if ( !$product ) {
				// if doesn't exits, then stop and start again
				continue;
			}
			$quantity = array_key_exists($index, $quantities) && floatval($quantities[$index]) > 0 ? floatval($quantities[$index]) : 1;
			$price = array_key_exists($index, $prices) && floatval($prices[$index]) > 0 ? floatval($prices[$index]) : 0.1;
			$sub_total = $quantity * $price;

			$purchased_products = new PurchaseProduct();
			$purchased_products->purchase_id = $purchase->id;
			$purchased_products->product_code = $product_code;
			$purchased_products->quantity = $quantity;
			$purchased_products->price = $price;
			$purchased_products->sub_total = $sub_total;
			$purchased_products->save();

			++$index;
		}
		return $request->all();
		session()->flash('success', 'Purchase is added successfully.');

		return redirect()->route('purchases.index');
	}

	public function show ($id)
	{
// 		$purchase = Purchase::with('products.product_details', 'vendor_details')
// 							->find($id);

		$purchase = Purchase::with('products.product_details', 'vendor_details')
							->find($id);

		if ( !$purchase ) {
			return view('errors.404');
		}
		return view('purchases.show', compact('purchase'));
	}

	public function edit ($id)
	{
		//
	}

	public function update (Request $request, $id)
	{
		//
	}

	public function destroy ($id)
	{
		$purchase = Purchase::find($id);
		if ( !$purchase ) {
			return view('errors.404');
		}

		// remove the purchase along with purchase products;

		$purchase->is_deleted = 1;
		$purchase->save();

		PurchaseProduct::where('purchase_id', $id)
					   ->update([ 'is_deleted' => 1 ]);


		session()->flash('success', 'Purchase is deleted');

		return redirect()->route('purchases.index');
	}
}
