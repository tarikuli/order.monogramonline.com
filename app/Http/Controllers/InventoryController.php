<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Monogram\Helper;
use App\Inventory;
use App\PurchasedInvProducts;

class InventoryController extends Controller
{
	private $inventory_indexes = [
		"All",
		"Local WH",
		"0-Expedited Orders ****** (7305)",
		"0-TO BE ASSIGNED TO A VENDOR (6328)",
		"0.0-MONOGRAM ORDERS (6271)",
		"1.0-GRAPHICS RUSH RED LASER*** (7038)",
		"1.0-GRAPHICS RUSH SUBMILATION *** (6784)",
		"1.0-GRAPHICS RUSH-GENERAL *** (6222)",
		"1.0-GRAPHICS RUSH-MONO *** (6255)",
		"1.0-GRAPHICS RUSH-NP *** (6258)",
		"1.0-JUAN REDO (6750)",
		"1.0-JUAN REPAIR (6749)",
		"1.1-APRON GRAPHICS (7282)",
		"3-CREATE BATCH (7835)",
		"3-GINA-GENERAL (6246)",
		"3-PATRICIO (6253)",
		"3-PATRICIO-SOLID GOLD (6254)",
		"4-American Personalized (7499)",
		"4-Baby Aspen (7504)",
		"4-Clay Design (7698)",
		"4-EMBROIDERY (7471)",
		"4-FRECKLE BOX (7465)",
		"4-Gift Basket Drop Shipping (7695)",
		"4-JDS Marketing (6329)",
		"4-Pro Gift Source (7431)",
		"4-Teals Prairie & Co.  (7694)",
		"5-GAVE TO JESSICA (7935)",
		"5-NEED CUSTOMER SERVICE  (6360)",
		"5-ORDER UPDATE (6273)",
		"5-REPAIRS (6398)",
		"5-WAITING FOR ANOTHER PC (6377)",
		"* 5-WAITING FOR ANOTHER PIECE 2 (7857)",
		"5-WAITING FOR INVENTORY (7799)",
		"6-READY TO SHIP (6376)",
		"7-Gift Boxes/Cleaner (6335)",
	];

	public function index ()
	{
		$inventories = Inventory::where('is_deleted', 0)
				->paginate(10);
		
		if ( !count($inventories) ) {
			return view('errors.404');
		}
		
		return view('inventories.index')
				->with('inventories', $inventories)
				->with('inventory_indexes', $this->inventory_indexes);
	}

	public function updateInventorie(Request $request, $inventorie_id)
	{
		$inventory = Inventory::find($inventorie_id);
		$inventory->re_order_qty = $request->re_order_qty;
		$inventory->min_reorder = $request->min_reorder;
		$inventory->adjustment = $request->adjustment;
		$inventory->save();
		return redirect(url('inventories#'.$inventorie_id))
		->with('success', sprintf("Update Success."));
		
	}
	
	public function create ()
	{
		//
	}

	public function store (Request $request)
	{
		//
	}

	public function show ($id)
	{
		//
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
		//
	}

	public function getStockNoUnique(Request $request)
	{
		$inventory = Inventory::where('stock_no_unique', $request->data)->first();
		$purchasedInvProducts = PurchasedInvProducts::where('stock_no', $request->data)->first();


		if(($inventory->count() <= 0) && ($purchasedInvProducts->count() <= 0) ){
			/**  Return Null Fields because not found **/
			return response()->json([
					'stock_name_discription' => '',
					'sku_weight' => '',
					're_order_qty' => '',
					'min_reorder' => '',
					'adjustment' => '',

					'unit' =>'',
					'unit_price' =>'',
					'vendor_id' =>'',
					'vendor_sku' =>'',
					'vendor_sku_name' =>'',
					'lead_time_days' =>'',
			]);
		}else{
			/**  Return Null Fields because  found **/
			return response()->json([
					'stock_name_discription' => $inventory->stock_name_discription,
					'sku_weight' => $inventory->sku_weight,
					're_order_qty' => $inventory->re_order_qty,
					'min_reorder' => $inventory->min_reorder,
					'adjustment' => $inventory->adjustment,

					'unit' =>	$purchasedInvProducts->unit,
					'unit_price' => $purchasedInvProducts->unit_price,
					'vendor_id' => $purchasedInvProducts->vendor_id,
					'vendor_sku' => $purchasedInvProducts->vendor_sku,
					'vendor_sku_name' => $purchasedInvProducts->vendor_sku_name,
					'lead_time_days' => $purchasedInvProducts->lead_time_days,
			]);
		}

	}
}
