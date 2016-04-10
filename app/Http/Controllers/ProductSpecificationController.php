<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductionCategory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProductSpecificationController extends Controller
{
	public function getSteps (Request $request, $id = 1)
	{
		/*
		 * Specification has two steps.
		 * if none of them
		 * exit
		 */
		switch ( $id ) {
			case 1:
				$production_categories = ProductionCategory::where('is_deleted', 0)
														   ->get()
														   ->lists('description_with_code', 'id')
														   ->prepend('Select a production category', '0');

				return view('product_specifications.spec_sheet_step_1')->with('production_categories', $production_categories);
			case 2:
				/*
				 * If the proposed sku is not found in the url
				 * and, proposed sku doesn't match the session stored sku
				 * redirect to home
				 */
				if ( !$request->has('sku') ) {
					return redirect()
						->to('/')
						->withErrors([
							'error' => 'SKU cannot be generated or modified by user',
						]);
				}

				return view('product_specifications.spec_sheet_step_2')->with('sku', $request->get('sku'));
				break;
			default:
				break;
		}
	}

	public function postSteps (Request $request, $id)
	{
		switch ( $id ) {
			case 1:
				$production_category = ProductionCategory::find($request->get('production_category'));
				if ( !$production_category ) {
					return redirect()
						->to('/')
						->withErrors([
							'error' => 'Not a valid production category',
						]);
				}
				$proposed_sku = $this->generateSKU($production_category->production_category_code, $request->get('gift-wrap'));
				session()->put('proposed_sku', $proposed_sku);

				return redirect()->to(sprintf("products_specifications/step/2?sku=%s", $proposed_sku));
			default:
				return redirect()
					->to('/')
					->withErrors([
						'error' => 'Invalid request',
					]);
		}
	}

	private function generateSKU ($production_category_code, $is_gift_wrapped = false)
	{
		$sku = sprintf("%s", $production_category_code);

		$products_stored = Product::where('product_model', 'LIKE', sprintf("%s%%", $production_category_code))
								  ->count();
		$total = $products_stored ? ++$products_stored : 1;
		$sku = sprintf("%s%04d", $sku, $total);

		if ( $is_gift_wrapped ) {
			$sku = sprintf("%s-GIFT", $sku);
		}

		return $sku;
	}
}
