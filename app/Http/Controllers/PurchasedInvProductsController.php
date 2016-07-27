<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PurchasedInvProducts;

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
    	$purchasedInvProducts = PurchasedInvProducts::where('is_deleted', 0)
    	->latest()
    	->paginate(50);

    	return view('purchased_inv_products.index', compact('purchasedInvProducts', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    	return view('purchased_inv_products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function store (Requests\PurchasedInvProductsCreateRequest $request)
	{
		$purchasedInvProducts = new PurchasedInvProducts();
		$purchasedInvProducts->code = trim($request->get('code'));
		$purchasedInvProducts->name = trim($request->get('name'));
		$purchasedInvProducts->unit = trim($request->get('unit'));
		$purchasedInvProducts->price = trim($request->get('price'));
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
    	if ( !$purchasedInvProducts ) {
    		return view('errors.404');
    	}

    	return view('purchased_inv_products.edit', compact('purchasedInvProducts'));
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
 		$purchasedInvProducts->code = trim($request->get('code'));
		$purchasedInvProducts->name = trim($request->get('name'));
		$purchasedInvProducts->unit = trim($request->get('unit'));
		$purchasedInvProducts->price = trim($request->get('price'));
		$purchasedInvProducts->save();
		session()->flash('success', 'Purchase Inventory Products created successfully.');

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
