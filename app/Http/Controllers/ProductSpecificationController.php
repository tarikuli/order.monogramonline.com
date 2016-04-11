<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductionCategory;
use App\SpecificationSheet;
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
						->to('/products_specifications/step')
						->withErrors([
							'error' => 'SKU cannot be generated or modified by user',
						]);
				}
				$production_categories = ProductionCategory::where('is_deleted', 0)
														   ->get()
														   ->lists('description_with_code', 'id')
														   ->prepend('Select a production category', '0');

				return view('product_specifications.spec_sheet_step_2')
					->with('sku', $request->get('sku'))
					->with('production_categories', $production_categories)
					->with('selected_production_category', $request->get('production_category'));
				break;
			default:
				break;
		}
	}

	public function postSteps (Request $request, $id = 1)
	{
		switch ( $id ) {
			case 1:
				$production_category = ProductionCategory::find($request->get('production_category'));
				if ( !$production_category ) {
					return redirect()
						->to('/products_specifications/step')
						->withErrors([
							'error' => 'Not a valid production category',
						]);
				}
				$proposed_sku = $this->generateSKU($production_category->production_category_code, $request->get('gift-wrap'));
				session()->put('proposed_sku', $proposed_sku);

				return redirect()->to(sprintf("products_specifications/step/2?sku=%s&production_category=%d", $proposed_sku, $request->get('production_category')));
			case 2:
				$specSheet = $this->insertOrUpdateSpec($request);
				session()->flush('proposed_sku');
				return redirect('/products_specifications/step')->with('success', 'Spec sheet is created successfully.');
			default:
				return redirect()
					->to('/products_specifications/step')
					->withErrors([
						'error' => 'Invalid request',
					]);
		}
	}

	private function insertOrUpdateSpec (Request $request, $specSheet = null)
	{
		if ( is_null($specSheet) ) {
			$specSheet = new SpecificationSheet();
		}

		$specSheet->product_name = trim($request->get('product_name'));
		$specSheet->product_sku = trim($request->get('product_sku'));
		$specSheet->product_description = trim($request->get('product_description'));
		$specSheet->product_weight = floatval($request->get('product_weight'));
		$specSheet->product_length = floatval($request->get('product_length'));
		$specSheet->product_width = floatval($request->get('product_width'));
		$specSheet->product_height = floatval($request->get('product_height'));
		$specSheet->packaging_type_name = trim($request->get('packaging_type_name'));
		$specSheet->packaging_size = trim($request->get('packaging_size'));
		$specSheet->packaging_weight = floatval($request->get('packaging_weight'));
		$specSheet->total_weight = floatval($request->get('total_weight'));
		$specSheet->production_category = intval($request->get('production_category'));
		$specSheet->art_work_location = trim($request->get('art_work_location'));
		$specSheet->temperature = trim($request->get('temperature'));
		$specSheet->dwell_time = trim($request->get('dwell_time'));
		$specSheet->pressure = trim($request->get('pressure'));
		$specSheet->run_time = trim($request->get('run_time'));
		$specSheet->type = trim($request->get('type'));
		$specSheet->font = trim($request->get('font'));
		$specSheet->variation_name = trim($request->get('variation_name'));

		/* Special note segment */
		$i = 0;
		$arr = [ ];
		foreach ( $request->get('special_note') as $note ) {
			$arr[] = [
				trim($note),
				trim($request->get('option_name')[$i]),
				trim($request->get('details')[$i]),
			];
			++$i;
		}

		$specSheet->special_note = json_encode($arr);
		/* special note segment ends */

		$specSheet->product_note = trim($request->get('product_note'));

		$specSheet->cost_of_1 = floatval($request->get('cost_of_1'));
		$specSheet->cost_of_10 = floatval($request->get('cost_of_10'));
		$specSheet->cost_of_100 = floatval($request->get('cost_of_100'));
		$specSheet->cost_of_1000 = floatval($request->get('cost_of_1000'));
		$specSheet->cost_of_10000 = floatval($request->get('cost_of_10000'));

		$i = 0;
		$arr = [ ];
		$j = 0;

		foreach ( $request->get('parts_name') as $part ) {
			$arr[] = [
				trim($part),
				$request->get('cost_variation')[$j++],
				$request->get('cost_variation')[$j++],
				$request->get('cost_variation')[$j++],
				$request->get('cost_variation')[$j++],
				trim($request->get('supplier_name')[$i]),
			];
			++$i;
		}

		$specSheet->content_cost_info = json_encode($arr);

		$specSheet->delivery_cost_variation = json_encode($request->get('delivery_cost_variation'));

		$specSheet->labor_expense_cost_variation = json_encode($request->get('labor_expense_cost_variation'));

		$paths = $this->image_manipulator($request->file('product_images'), $request->get('product_sku'));
		$specSheet->images = json_encode($paths);
		$specSheet->save();

		return $specSheet;
	}

	private function image_manipulator ($images, $sku)
	{
		$paths = [ ];
		foreach ( $images as $image ) {
			if ( $image->isValid() ) {
				$destinationPath = 'assets/images/spec_sheet';
				$extension = $image->getClientOriginalExtension();
				$fileName = sprintf("%s-%s.%s", $sku, rand(11111, 99999), $extension);
				$image->move($destinationPath, $fileName);
				$paths[] = sprintf("%s/%s", url($destinationPath), $fileName);
			}
		}

		return $paths;
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
