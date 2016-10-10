<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PurchasedInvProducts;
use Monogram\Helper;
// purchased_products
class PurchasedInvProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$count = 1;
    	$purchasedInvProducts = PurchasedInvProducts::with('purchasedInvProduct_details')->where('is_deleted', 0)
											    	->latest()
											    	->paginate(50);

//     	return $purchasedInvProducts;

    	return view('purchased_inv_products.index', compact('purchasedInvProducts', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //.
    	$stock_number = Inventory::where('is_deleted', 0)
							    	->orderBy('stock_no_unique')
							    	->lists('stock_no_unique', 'stock_no_unique')
							    	->prepend('Select a Stock Number', 'Select a Stock Number');
    	
    	return view('purchased_inv_products.create', compact('stock_number'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function store (Requests\PurchasedInvProductsCreateRequest $request)
	{
// dd($request->all());

		// Check if new_stock_number exist and push
		if(!empty($request->stock_no)){
			// Check new_stock_number exist in inventories Table
			// Check new_stock_number exist in purchased_inv_products Table
// 			Helper::insert_stock_number($request->stock_no);
		}else{
			$request->stock_number = $request->stock_number;
		}

		$inventorie = Inventory::where('is_deleted', 0)
					->where('stock_no_unique', $request->get('stock_no'))
					->get();

		
				
		if($inventorie->count() <= 0 ){
			/**  Add a new  stock_no_unique in inventories Table **/
			$inventorie = new Inventory();
			$inventorie->stock_no_unique = trim($request->get('stock_no'));
			$inventorie->stock_name_discription = trim($request->get('stock_name_discription'));
			$inventorie->sku_weight = trim($request->get('sku_weight'));
			$inventorie->re_order_qty = trim($request->get('re_order_qty'));
			$inventorie->min_reorder = trim($request->get('min_reorder'));
			$inventorie->adjustment = trim($request->get('adjustment'));
			$inventorie->save();
		}else{
			/**  Update  stock_no_unique in inventories Table **/
			$inventorie[0]->stock_name_discription = trim($request->get('stock_name_discription'));
			$inventorie[0]->sku_weight = trim($request->get('sku_weight'));
			$inventorie[0]->re_order_qty = trim($request->get('re_order_qty'));
			$inventorie[0]->min_reorder = trim($request->get('min_reorder'));
			$inventorie[0]->adjustment = trim($request->get('adjustment'));
			$inventorie[0]->save();
		}

		/**  Add a new  stock_no_unique in inventories Table **/
		$purchasedInvProducts = new PurchasedInvProducts();
		$purchasedInvProducts->stock_no = trim($request->get('stock_no'));
		$purchasedInvProducts->unit = trim($request->get('unit'));
		$purchasedInvProducts->unit_price = trim($request->get('unit_price'));
		$purchasedInvProducts->vendor_id = trim($request->get('vendor_id'));
		$purchasedInvProducts->vendor_sku = trim($request->get('vendor_sku'));
		$purchasedInvProducts->vendor_sku_name = trim($request->get('vendor_sku_name'));
		$purchasedInvProducts->lead_time_days = trim($request->get('lead_time_days'));
		$purchasedInvProducts->save();

		session()->flash('success', 'Purchase Inventory Products created successfully.');

		return redirect()->route('purchasedinvproducts.index');
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    	$purchasedInvProducts = PurchasedInvProducts::find($id);
    	$inventorie = Inventory::where('is_deleted', 0)
					->where('stock_no_unique', $purchasedInvProducts->stock_no)
					->get();
// return $inventorie[0];
    	if ( !$purchasedInvProducts ) {
    		return view('errors.404');
    	}

    	return view('purchased_inv_products.edit', compact('purchasedInvProducts','inventorie'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update (Requests\PurchasedInvProductsUpdateRequest $request, $id)
    {

    	$purchasedInvProducts = PurchasedInvProducts::find($id);
    	if ( !$purchasedInvProducts ) {
    		return view('errors.404');
    	}
		$purchasedInvProducts->stock_no = trim($request->get('stock_no'));
		$purchasedInvProducts->unit = trim($request->get('unit'));
		$purchasedInvProducts->unit_price = trim($request->get('unit_price'));
		$purchasedInvProducts->vendor_id = trim($request->get('vendor_id'));
		$purchasedInvProducts->vendor_sku = trim($request->get('vendor_sku'));
		$purchasedInvProducts->vendor_sku_name = trim($request->get('vendor_sku_name'));
		$purchasedInvProducts->lead_time_days = trim($request->get('lead_time_days'));
		$purchasedInvProducts->save();


		/**  Update  stock_no_unique in inventories Table **/
		$inventorie = Inventory::where('is_deleted', 0)
								->where('stock_no_unique', $request->get('stock_no'))
								->get();
		$inventorie[0]->stock_name_discription = trim($request->get('stock_name_discription'));
		$inventorie[0]->sku_weight = trim($request->get('sku_weight'));
		$inventorie[0]->re_order_qty = trim($request->get('re_order_qty'));
		$inventorie[0]->min_reorder = trim($request->get('min_reorder'));
		$inventorie[0]->adjustment = trim($request->get('adjustment'));
		$inventorie[0]->save();


		session()->flash('success', 'Purchase Inventory Products Update successfully.');

		return redirect()->route('purchasedinvproducts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	$purchasedInvProducts = PurchasedInvProducts::find($id);
    	if ( !$purchasedInvProducts ) {
    		return view('errors.404');
    	}

    	$purchasedInvProducts->is_deleted = 1;
    	$purchasedInvProducts->save();

    	session()->flash('success', 'Purchase Inventory Products successfully deleted.');

    	return redirect()->route('purchasedinvproducts.index');
    }
}
