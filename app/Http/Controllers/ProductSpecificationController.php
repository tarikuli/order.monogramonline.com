<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductionCategory;
use App\SpecificationSheet;
use App\Vendor;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Monogram\Helper;

class ProductSpecificationController extends Controller
{
	public function index (Request $request)
	{
		$specSheets = SpecificationSheet::with('production_category')
										->searchCriteria($request->get('search_for_1'), $request->get('search_in_1'))
										->searchCriteria($request->get('search_for_2'), $request->get('search_in_2'))
										->searchStatus($request->get('status'))
										->searchMakeSample($request->get('make_sample'))
										->searchInProductionCategory($request->get('production_category'))
										->searchInWebImageStatus($request->get('web_image_status'))
										->where('is_deleted', 0)
										->paginate(50);
		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->get()
												   ->lists('description_with_code', 'id')
												   ->prepend('Select a production category', '0');
		$statuses = array_merge([ 'all' => "Select a status" ], SpecificationSheet::$statuses);
		$make_sample_data = array_merge([ 'all' => 'Select a make sample' ], Helper::$specSheetSampleDataArray);
		#return $statuses;
		#return $specSheets;
		return view('product_specifications.index')
			->with('specSheets', $specSheets)
			->with('count', 1)
			->with('production_categories', $production_categories)
			->with('make_sample_data', $make_sample_data)
			->with('statuses', $statuses)
			->with('request', $request);
	}

	public function create ()
	{
		return redirect(url("products_specifications/step/1"));
	}

	public function show ($id)
	{
		// redirect to edit page
		return $this->edit($id);
		$spec = SpecificationSheet::find($id);
		if ( !$spec ) {
			return app()->abort(404);
		}

		return $spec;
	}

	public function edit ($id)
	{
		$spec = SpecificationSheet::find($id);
		if ( !$spec ) {
			return app()->abort(404);
		}

		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->get()
												   ->lists('description_with_code', 'id')
												   ->prepend('Select a production category', '0');

		$vendors = Vendor::where('is_deleted', 0)
						 ->lists('vendor_name', 'vendor_name')
						 ->prepend('Select a vendor', 0);

		return view('product_specifications.edit')
			->with('vendors', $vendors)
			->with('spec', $spec)
			->with('production_categories', $production_categories);
	}

	public function update (Request $request, $id)
	{
		$spec = SpecificationSheet::find($id);
		if ( !$spec ) {
			return app()->abort(404);
		}
		$updatedSpecSheet = $this->insertOrUpdateSpec($request, $spec);
		if ( $updatedSpecSheet == false ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'Product name cannot be empty.',
				]);
		}
		session()->flush('proposed_sku');

		return redirect("/products_specifications/$id")->with('success', 'Spec sheet is updated successfully.');
	}

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

				$vendors = Vendor::where('is_deleted', 0)
								 ->lists('vendor_name', 'vendor_name')
								 ->prepend('Select a vendor', 0);

				return view('product_specifications.spec_sheet_step_2')
					->with('vendors', $vendors)
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
				$specSheet = false;
				$previous_sku = trim($request->get('previous_sku', ''));
				if ( !empty( $previous_sku ) ) {
					$specSheet = SpecificationSheet::where('product_sku', $previous_sku)
												   ->first();
					if ( !$specSheet ) {
						return redirect()
							->back()#->withInput()
							->withErrors([
								'error' => 'Not a valid product sku is chosen to copy',
							]);
					} else {
						$newSpecSheet = $specSheet->replicate();
						$newSpecSheet->product_sku = trim($request->get('product_sku'));
						$newSpecSheet->save();
					}
				} else {
					$specSheet = $this->insertOrUpdateSpec($request);
				}
				if ( $specSheet == false ) {
					return redirect()
						->back()
						->withInput()
						->withErrors([
							'error' => 'Product name cannot be empty.',
						]);
				}
				session()->flush('proposed_sku');

				return redirect('/products_specifications')->with('success', 'Spec sheet is created successfully.');
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
		$product_name = trim($request->get('product_name'));

		if ( empty( $product_name ) ) {
			return false;
		}

		if ( is_null($specSheet) ) {
			$specSheet = new SpecificationSheet();
			// product sku is one time insert able. only on insertion time.
			$specSheet->product_sku = trim($request->get('product_sku'));
			$specSheet->status = 0;
		} else {
			$specSheet->status = intval($request->get('status'));
		}
		$specSheet->product_name = $product_name;
		$specSheet->web_image_status = intval(trim($request->get('web_image_status')));
		$specSheet->product_description = trim($request->get('product_description'));
		$specSheet->product_weight = floatval($request->get('product_weight'));
		$specSheet->product_length = floatval($request->get('product_length'));
		$specSheet->product_width = floatval($request->get('product_width'));
		$specSheet->product_height = floatval($request->get('product_height'));
		$specSheet->packaging_type_name = trim($request->get('packaging_type_name'));
		$specSheet->packaging_size = trim($request->get('packaging_size'));
		$specSheet->packaging_weight = floatval($request->get('packaging_weight'));
		$specSheet->total_weight = floatval($request->get('total_weight'));
		$specSheet->production_category_id = intval($request->get('production_category'));
		$specSheet->art_work_location = trim($request->get('art_work_location'));
		$specSheet->production_image_location = trim($request->get('production_image_location'));
		$specSheet->temperature = trim($request->get('temperature'));
		$specSheet->dwell_time = trim($request->get('dwell_time'));
		$specSheet->pressure = trim($request->get('pressure'));
		$specSheet->run_time = trim($request->get('run_time'));
		$specSheet->type = trim($request->get('type'));
		$specSheet->font = trim($request->get('font'));
		$specSheet->variation_name = trim($request->get('variation_name'));
		$specSheet->make_sample = trim($request->get('make_sample'));
		$specSheet->product_general_note = trim($request->get('product_general_note'));

		/* spec table data */
		$table_data = [ ];
		foreach ( array_chunk($request->get('spec_table_data'), 10) as $row ) {
			if ( empty( $row[0] ) ) {
				continue;
			}
			$table_data[] = $row;
		}

		$specSheet->spec_table_data = json_encode($table_data);


		/* Special note segment */
		$i = 0;
		$arr = [ ];
		foreach ( $request->get('special_note') as $note ) {
			// if special note col is left empty, don't insert
			if ( empty( trim($note) ) ) {
				continue;
			}
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
			if ( empty( trim($part) ) ) {
				continue;
			}
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

		if ( $request->hasFile('product_images') ) {
			$paths = $this->image_manipulator($request->file('product_images'), $request->get('product_sku'));
			$specSheet->images = json_encode($paths);
		}

		if ( $request->has('delete_product_details_image') && strtolower($request->get('delete_product_details_image', 'no')) == 'yes' ) {
			$specSheet->product_details_file = json_encode([ ]); // delete the image already in
		} elseif ( $request->hasFile('product_details_file') ) {
			$paths = $this->image_manipulator($request->file('product_details_file'), $request->get('product_sku'));
			$specSheet->product_details_file = json_encode($paths);
		}
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

		/*$products_stored = Product::where('product_model', 'LIKE', sprintf("%s%%", $production_category_code))
								  ->count();
		$total = $products_stored ? ++$products_stored : 1;*/
		$products_stored = SpecificationSheet::count();
		$total = 4140 + $products_stored;
		$sku = sprintf("%s%04d", $sku, $total);

		if ( $is_gift_wrapped ) {
			$sku = sprintf("%s-GIFT", $sku);
		}

		return $sku;
	}

	public function destroy (Request $request, $id)
	{
		$spec = SpecificationSheet::find($id);
		if ( $spec ) {
			$spec->is_deleted = 1;
			$spec->save();
		}

		return redirect()
			->to('/products_specifications')
			->with('success', 'Spec is deleted successfully');
	}
}
