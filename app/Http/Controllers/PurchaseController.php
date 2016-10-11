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
// return $purchases->all();

		return view('purchases.index', compact('purchases', 'count'));
	}

	public function create (Request $request)
	{
		$vendors = Vendor::where('is_deleted', 0)
						 ->lists('vendor_name', 'id')
						 ->prepend('Select a vendor', 0);
// return $vendors;		
		$new_purchase_number = sprintf("%06d", Purchase::count() + 1);

		return view('purchases.create', compact('vendors','new_purchase_number'));
	}

	public function store (Requests\PurchaseCreateRequest $request)
	{
		$purchase = new Purchase();
		$purchase->po_number = $request->get('po_number');
		$purchase->po_date = trim($request->get('po_date'));
		$purchase->vendor_id = trim($request->get('vendor_id'));
		$purchase->grand_total = trim($request->get('grand_total'));
		$purchase->save();

		// one purchase may have many products
		$index = 0;
		// index is used to grab the array of products with details.
		$purchase_ids = $request->get('purchase_id');
		$product_ids = $request->get('product_id');
		$stock_nos = $request->get('stock_no');
		$vendor_skus = $request->get('vendor_sku');
		$quantitys = $request->get('quantity');
		$prices = $request->get('price');
		$sub_totals = $request->get('sub_total');
		$receive_dates = $request->get('receive_date');
		$receive_quantitys = $request->get('receive_quantity');
		$balance_quantitys = $request->get('balance_quantity');
		//----------

		foreach ( $purchase_ids as $purchase_id ) {
			if(!empty($vendor_skus[$index]) && !empty($quantitys[$index]) && !empty($sub_totals[$index])){			
				$purchased_products = new PurchaseProduct();
				$purchased_products->purchase_id 		= $request->get('po_number');
				$purchased_products->product_id 		= $product_ids[$index];
				$purchased_products->stock_no 			= $stock_nos[$index];
				$purchased_products->vendor_sku 		= $vendor_skus[$index];
				$purchased_products->quantity 			= $quantitys[$index];
				$purchased_products->price 				= $prices[$index];
				$purchased_products->sub_total 			= $sub_totals[$index];
				$purchased_products->receive_date 		= $receive_dates[$index];
				$purchased_products->receive_quantity 	= $receive_quantitys[$index];
				$purchased_products->balance_quantity 	= $balance_quantitys[$index];
				$purchased_products->save();
				Helper::addInventoryByStockNumber($stock_nos[$index], null);
				++$index;
			}
		}
		session()->flash('success', 'Purchase is added successfully.');

		return redirect()->route('purchases.index');
	}

	public function show ($id)
	{
		$purchase = Purchase::with('products.product_details', 'vendor_details')
							->find($id);
		
// 		$purchaseProduct = PurchaseProduct::where('purchase_id', $purchase->po_number)->get();
// 		return $purchase;

		if ( !$purchase ) {
			return view('errors.404');
		}
		
		return view('purchases.show', compact('purchase'));
	}

	public function edit ($id)
	{
		//
		$purchase = Purchase::with('products.product_details', 'vendor_details')
								->where('po_number', $id)
								->where('is_deleted', 0)
								->get();

// 		$purchase = Purchase::with('products.product_details', 'vendor_details')
// 		->find($id);

		if ( count($purchase) == 0 ) {
			return view('errors.404');
		}
		$purchase = $purchase[0];
// 		return $purchase;
		return view('purchases.edit', compact('purchase'));

	}

	public function update (Request $request, $id)
	{
		$purchase = Purchase::where('po_number', $id)
					->where('is_deleted', 0)
					->get();

		$purchase = $purchase[0];

		if ( count($purchase) == 0 ) {
			return view('errors.404');
		}

		$purchase->po_number = $request->get('po_number');
		$purchase->po_date = trim($request->get('po_date'));
		$purchase->vendor_id = trim($request->get('vendor_id'));
		$purchase->grand_total = trim($request->get('grand_total'));
		$purchase->save();

		PurchaseProduct::where('purchase_id', $id)
						->delete();

		// one purchase may have many products
		$index = 0;
		// index is used to grab the array of products with details.
		$purchase_ids = array_values(($request->get('purchase_id')));
		$product_ids = array_values(($request->get('product_id')));
		$stock_nos = array_values(($request->get('stock_no')));
		$vendor_skus = array_values(($request->get('vendor_sku')));
		$quantitys = array_values(($request->get('quantity')));
		$prices = array_values(($request->get('price')));
		$sub_totals = array_values(($request->get('sub_total')));
		$receive_dates = array_values(($request->get('receive_date')));
		$receive_quantitys = array_values(($request->get('receive_quantity')));
		$balance_quantitys = array_values(($request->get('balance_quantity')));
		//----------

		foreach ( $purchase_ids as $purchase_id ) {
			if(!empty($vendor_skus[$index]) && !empty($quantitys[$index]) && !empty($sub_totals[$index])){
				$purchased_products = new PurchaseProduct();
				$purchased_products->purchase_id 		= $request->get('po_number');
				$purchased_products->product_id 		= $product_ids[$index];
				$purchased_products->stock_no 			= $stock_nos[$index];
				$purchased_products->vendor_sku 		= $vendor_skus[$index];
				$purchased_products->quantity 			= $quantitys[$index];
				$purchased_products->price 				= $prices[$index];
				$purchased_products->sub_total 			= $sub_totals[$index];
				$purchased_products->receive_date 		= $receive_dates[$index];
				$purchased_products->receive_quantity 	= $receive_quantitys[$index];
				$purchased_products->balance_quantity 	= $balance_quantitys[$index];			
				$purchased_products->save();
				Helper::addInventoryByStockNumber($stock_nos[$index], null);
				++$index;
			}
		}
		session()->flash('success', 'Purchases is successfully updated.');

		return redirect()
				->back()
				->with('success', 'Purchases is successfully updated.');

// 		return redirect()->route('purchases.index');
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

	public function getVendorById(Request $request){
		$vendor = Vendor::find($request->vendor_id);
		if(!$vendor){
			/**  Return Null Fields because not found **/
			return response()->json([
					'vendor_name' => '',
					'email' => '',
					'zip_code' => '',
					'state' => '',
					'phone_number' => '',
			]);
		}else{
			/**  Return Null Fields because  found **/
			return response()->json([
					'vendor_name' => $vendor->vendor_name,
					'email' => $vendor->email,
					'zip_code' => $vendor->zip_code,
					'state' => $vendor->state,
					'phone_number' => $vendor->phone_number,
			]);
		}
	}

	public function getPurchasedInvProducts(Request $request){
		$purchasedInvProducts = PurchasedInvProducts::where('vendor_id', $request->vendor_id)
													->where('vendor_sku', $request->vendor_sku)
													->first();

		if(!$purchasedInvProducts){
			/**  Return Null Fields because not found **/
			return response()->json([
					'product_id' => '',
					'stock_no' => '',
					'vendor_sku' => '',
					'price' => '',
					'vendor_sku_name' => '',
			]);
		}else{
			/**  Return Null Fields because  found **/
			return response()->json([
					'product_id' => $purchasedInvProducts->id,
					'stock_no' => $purchasedInvProducts->stock_no,
					'vendor_sku' => $purchasedInvProducts->vendor_sku,
					'price' => $purchasedInvProducts->unit_price,
					'vendor_sku_name' => $purchasedInvProducts->vendor_sku_name,
			]);
		}
	}

	public function autoComplete(Request $request) {
//dd($request->all());
		$query = $request->get('serchTxt','');

		$vendors=Vendor::where('is_deleted', 0)->where('vendor_name','LIKE','%'.$query.'%')->get()->take(5);

// 		$vendors = Vendor::where('is_deleted', 0)
// 							->where('vendor_name','LIKE','%'.$query.'%')
// 							->lists('vendor_name', 'id')
// 							->take(5);
// 		->prepend('Select a vendor', 0);

// 		dd($vendors);

		$data=array();
		foreach ($vendors as $key => $vendor) {
			$data[]=array('value'=>$vendor->id." - ".$vendor->vendor_name, 'id'=>$vendor->id);
		}
		if(count($data))
			return response()->json($data);
		else
			return response()->json(['value'=>'No Result Found','id'=>'']);
	}
}
