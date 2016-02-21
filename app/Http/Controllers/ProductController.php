<?php

namespace App\Http\Controllers;

use App\BatchRoute;
use App\Category;
use App\MasterCategory;
use App\Product;
use App\ProductionCategory;
use App\Store;
use App\SubCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAddRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\MessageBag;
use League\Csv\Reader;
use League\Csv\Writer;
use Monogram\Helper;

class ProductController extends Controller
{
	public function index (Request $request)
	{
		$products = Product::with('batch_route')
						   ->where('is_deleted', 0)
						   ->searchIdCatalog($request->get('id_catalog'))
						   ->searchProductModel($request->get('product_model'))
						   ->searchProductName($request->get('product_name'))
						   ->searchRoute($request->get('route'))
						   ->searchProductionCategory($request->get('product_production_category'))
						   ->searchMasterCategory($request->get('product_master_category'))
						   ->searchCategory($request->get('product_category'))
						   ->searchSubCategory($request->get('product_sub_category'))
						   ->latest()
						   ->paginate(50);

		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->lists('batch_route_name', 'id');

		$searchInRoutes = Collection::make($batch_routes);
		$searchInRoutes->prepend('All', '0');

		$batch_routes->prepend('Not selected', 'null');

		$product_master_category = MasterCategory::where('is_deleted', 0)
												 ->lists('master_category_description', 'id')
												 ->prepend('All', 0);

		$product_category = Category::where('is_deleted', 0)
									->lists('category_description', 'id')
									->prepend('All', 0);

		$product_sub_category = SubCategory::where('is_deleted', 0)
										   ->lists('sub_category_description', 'id')
										   ->prepend('All', 0);

		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->lists('production_category_description', 'id')
												   ->prepend('Select production category', '');
		$count = 1;

		return view('products.index', compact('products', 'count', 'batch_routes', 'request', 'searchInRoutes', 'product_master_category', 'product_category', 'product_sub_category', 'production_categories'));
	}

	public function create ()
	{
		$stores = Store::where('is_deleted', 0)
					   ->lists('store_name', 'store_id');

		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->lists('batch_route_name', 'id')
								  ->prepend('Select a Route', '');
		$is_taxable = [
			'1' => 'Yes',
			'0' => 'No',
		];

		$master_categories = MasterCategory::where('is_deleted', 0)
										   ->lists('master_category_description', 'id')
										   ->prepend('Select category', '');

		$categories = Category::where('is_deleted', 0)
							  ->lists('category_description', 'id')
							  ->prepend('Select sub category 1', '');

		$sub_categories = SubCategory::where('is_deleted', 0)
									 ->lists('sub_category_description', 'id')
									 ->prepend('Select sub category 2', '');

		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->lists('production_category_description', 'id')
												   ->prepend('Select production category', '');

		return view('products.create', compact('title', 'stores', 'batch_routes', 'is_taxable', 'master_categories', 'categories', 'sub_categories', 'production_categories'));
	}

	public function store (ProductAddRequest $request)
	{
		$id_catalog = trim($request->get('id_catalog'));
		$product_model = trim($request->get('product_model'));
		$checkExisting = Product::where('id_catalog', $id_catalog)
								->orWhere('product_model', $product_model)
								->first();
		if ( $checkExisting ) {
			return redirect()
				->back()
				->withInput()
				->withErrors([
					'error' => 'Product already exists either with id catalog or model',
				]);
		}

		$product = new Product();
		$product->id_catalog = $id_catalog;
		$product->product_model = $product_model;
		if ( $request->exists('store_id') ) {
			$product->store_id = $request->get('store_id');
		}
		if ( $request->exists('vendor_id') ) {
			$product->vendor_id = $request->get('vendor_id');
		}
		if ( $request->exists('product_url') ) {
			$product->product_url = $request->get('product_url');
		}
		if ( $request->exists('product_name') ) {
			$product->product_name = trim($request->get('product_name'));
		}
		if ( $request->exists('ship_weight') ) {
			$product->ship_weight = floatval($request->get('ship_weight'));
		}
		if ( $request->exists('product_master_category') ) {
			$product->product_master_category = intval($request->get('product_master_category'));
		}
		if ( $request->exists('product_category') ) {
			$product->product_category = intval($request->get('product_category'));
		}
		if ( $request->exists('product_sub_category') ) {
			$product->product_sub_category = intval($request->get('product_sub_category'));
		}
		if ( $request->exists('product_production_category') ) {
			$product->product_production_category = intval($request->get('product_production_category'));
		}
		if ( $request->exists('product_price') ) {
			$product->product_price = floatval($request->get('product_price'));
		}
		if ( $request->exists('product_sale_price') ) {
			$product->product_sale_price = floatval($request->get('product_sale_price'));
		}
		if ( $request->exists('product_thumb') ) {
			$product->product_thumb = $request->get('product_thumb');
		}
		if ( $request->exists('batch_route_id') ) {
			// update request via form for overall change
			if ( is_numeric($request->get('batch_route_id')) ) {
				$requested_batch_route_id = $request->get('batch_route_id');
				$batch_route = BatchRoute::where('is_deleted', 0)
										 ->find($requested_batch_route_id);
			} else {
				// update request from lists
				// only for batch route
				$requested_batch_route_text = $request->get('batch_route_id');
				$batch_route = BatchRoute::where('batch_code', $requested_batch_route_text)
										 ->first();
			}

			if ( $batch_route ) {
				$product->batch_route_id = $batch_route->id;
			} else {
				$product->batch_route_id = Helper::getDefaultRouteId();
				$is_error = true;
				$error_messages[] = [ 'batch_code' => 'Batch code is not correct' ];
			}
		}
		if ( $request->exists('is_taxable') ) {
			$product->is_taxable = $request->get('is_taxable') ? 1 : 0;
		}
		if ( $request->exists('product_keywords') ) {
			$product->product_keywords = trim($request->get('product_keywords'));
		}
		if ( $request->exists('product_description') ) {
			$product->product_description = trim($request->get('product_description'));
		}
		if ( $request->exists('height') ) {
			$product->height = floatval($request->get('height'));
		}
		if ( $request->exists('width') ) {
			$product->width = floatval($request->get('width'));
		}
		$product->save();

		return redirect(url('products'));
	}

	public function show ($id)
	{
		// if searching for inactive or deleted product
		$product = Product::with('batch_route', 'master_category', 'category', 'sub_category', 'production_category')
						  ->where('is_deleted', 0)
						  ->find($id);
		if ( !$product ) {
			return view('errors.404');
		}

		#return $product;
		return view('products.show', compact('product'));
	}

	public function edit ($id)
	{
		$stores = Store::where('is_deleted', 0)
					   ->lists('store_name', 'store_id');
		// if searching for inactive or deleted product
		$product = Product::with('batch_route')
						  ->where('is_deleted', 0)
						  ->find($id);
		if ( !$product ) {
			return view('errors.404');
		}

		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->lists('batch_route_name', 'id');
		$master_categories = MasterCategory::where('is_deleted', 0)
										   ->lists('master_category_description', 'id')
										   ->prepend('Select category', '');

		$categories = Category::where('is_deleted', 0)
							  ->lists('category_description', 'id')
							  ->prepend('Select sub category 1', '');

		$sub_categories = SubCategory::where('is_deleted', 0)
									 ->lists('sub_category_description', 'id')
									 ->prepend('Select sub category 2', '');

		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->lists('production_category_description', 'id')
												   ->prepend('Select production category', '');
		$is_taxable = [
			'1' => 'Yes',
			'0' => 'No',
		];

		return view('products.edit', compact('product', 'stores', 'batch_routes', 'is_taxable', 'master_categories', 'categories', 'sub_categories', 'production_categories'));
	}

	public function update (ProductUpdateRequest $request, $id)
	{
		$product = Product::where('is_deleted', 0)
						  ->find($id);
		if ( !$product ) {
			return view('errors.404');
		}
		$is_error = false;
		$error_messages = [ ];
		if ( $request->exists('store_id') ) {
			$product->store_id = $request->get('store_id');
		}
		if ( $request->exists('vendor_id') ) {
			$product->vendor_id = $request->get('vendor_id');
		}
		if ( $request->exists('product_url') ) {
			$product->product_url = $request->get('product_url');
		}
		if ( $request->exists('product_name') ) {
			$product->product_name = trim($request->get('product_name'));
		}
		if ( $request->exists('ship_weight') ) {
			$product->ship_weight = floatval($request->get('ship_weight'));
		}
		if ( $request->exists('product_master_category') ) {
			$product->product_master_category = $request->get('product_master_category');
		}
		if ( $request->exists('product_category') ) {
			$product->product_category = $request->get('product_category');
		}
		if ( $request->exists('product_sub_category') ) {
			$product->product_sub_category = $request->get('product_sub_category');
		}
		if ( $request->exists('product_production_category') ) {
			$product->product_production_category = intval($request->get('product_production_category'));
		}
		if ( $request->exists('product_price') ) {
			$product->product_price = floatval($request->get('product_price'));
		}
		if ( $request->exists('product_sale_price') ) {
			$product->product_sale_price = floatval($request->get('product_sale_price'));
		}
		if ( $request->exists('product_thumb') ) {
			$product->product_thumb = $request->get('product_thumb');
		}
		if ( $request->exists('batch_route_id') ) {
			// update request via form for overall change
			if ( is_numeric($request->get('batch_route_id')) ) {
				$requested_batch_route_id = $request->get('batch_route_id');
				$batch_route = BatchRoute::where('is_deleted', 0)
										 ->find($requested_batch_route_id);
			} else {
				// update request from lists
				// only for batch route
				$requested_batch_route_text = $request->get('batch_route_id');
				$batch_route = BatchRoute::where('batch_code', $requested_batch_route_text)
										 ->first();
			}

			if ( $batch_route ) {
				$product->batch_route_id = $batch_route->id;
			} else {
				$product->batch_route_id = Helper::getDefaultRouteId();
				$is_error = true;
				$error_messages[] = [ 'batch_code' => 'Batch code is not correct' ];
			}
		}
		if ( $request->exists('is_taxable') ) {
			$product->is_taxable = $request->get('is_taxable') ? 1 : 0;
		}
		if ( $request->exists('product_keywords') ) {
			$product->product_keywords = trim($request->get('product_keywords'));
		}
		if ( $request->exists('product_description') ) {
			$product->product_description = trim($request->get('product_description'));
		}
		if ( $request->exists('height') ) {
			$product->height = floatval($request->get('height'));
		}
		if ( $request->exists('width') ) {
			$product->width = floatval($request->get('width'));
		}
		$product->save();

		if ( !$request->ajax() ) {
			if ( $is_error ) {
				return redirect()
					->back()
					->withErrors(new MessageBag($error_messages));
			} else {
				$product->save();
				Session::flash('success', sprintf('Product: <b>%s</b> is updated successfully', $product->id_catalog));

				return redirect()->back();
			}
		} else {
			if ( $is_error ) {
				return response()->json([
					'error' => true,
					'data'  => new MessageBag($error_messages),
				], 422);
			}

			return response()->json([
				'error' => false,
				'data'  => 'Product batch is successfully updated',
			], 200);
		}

	}

	public function destroy ($id)
	{
		$product = Product::where('is_deleted', 0)
						  ->find($id);
		if ( !$product ) {
			return view('errors.404');
		}

		$product->is_deleted = 1;
		$product->save();

		return redirect(url('products'));
	}

	public function unassigned (Request $request)
	{
		$products = Product::with('batch_route')
						   ->where('is_deleted', 0)
						   ->whereNull('batch_route_id')
						   ->orWhere('batch_route_id', Helper::getDefaultRouteId())
						   ->searchIdCatalog($request->get('id_catalog'))
						   ->searchProductModel($request->get('product_model'))
						   ->searchProductName($request->get('product_name'))
						   ->latest()
						   ->paginate(50);
		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->lists('batch_route_name', 'id');

		$searchInRoutes = Collection::make($batch_routes);
		$searchInRoutes->prepend('All', '0');

		$batch_routes->prepend('Not selected', 'null');

		$product_master_category = MasterCategory::where('is_deleted', 0)
												 ->lists('master_category_description', 'id')
												 ->prepend('All', 0);

		$product_category = Category::where('is_deleted', 0)
									->lists('category_description', 'id')
									->prepend('All', 0);

		$product_sub_category = SubCategory::where('is_deleted', 0)
										   ->lists('sub_category_description', 'id')
										   ->prepend('All', 0);

		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->lists('production_category_description', 'id')
												   ->prepend('Select production category', '');
		$count = 1;

		return view('products.index', compact('products', 'count', 'batch_routes', 'request', 'searchInRoutes', 'product_master_category', 'product_category', 'product_sub_category', 'production_categories'));
	}

	public function import (Request $request)
	{
		$file = $request->file('csv_file');

		$mimes = [
			'application/vnd.ms-excel',
			'text/plain',
			'text/csv',
			'text/tsv',
		];
		if ( !in_array($file->getClientMimeType(), $mimes) ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([ 'error' => 'Not a valid csv file' ]));
		}
		$file_path = sprintf("%s/assets/imports/products/", public_path());
		$file_name = $file->getClientOriginalName();
		$fully_specified_file_name = sprintf("%s%s", $file_path, $file_name);

		$file->move($file_path, $file_name);
		$reader = Reader::createFromPath($fully_specified_file_name);
		$table_columns = Product::getTableColumns();
		$csv_columns = array_filter($reader->fetchOne());

		if ( count(array_intersect($table_columns, $csv_columns)) != count($table_columns) ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([
					'error' => 'CSV file columns don\'t match. Import stopped.',
				]));
		}
		$rows = $reader->setOffset(1)
					   ->fetchAssoc($table_columns);

		foreach ( $rows as $row ) {
			$product = Product::where('id_catalog', $row['id_catalog'])
							  ->first();
			if ( !$product ) {
				$product = new Product();
				$product->id_catalog = $row['id_catalog'];
			}
			foreach ( $table_columns as $column ) {
				if ( $column == 'id_catalog' ) {
					continue;
				} elseif ( $column == 'is_taxable' ) {
					$product->is_taxable = strtolower($row['is_taxable']) == 'yes' ? 1 : 0;
				} elseif ( $column == 'batch_route_id' ) {
					/*$batch_route = BatchRoute::where('batch_code', 'LIKE', sprintf("%%%s%%", trim($row['batch_route_id'])))
											 ->first();*/
					$batch_route = BatchRoute::where('batch_code', 'LIKE', sprintf("%s", trim($row['batch_route_id'])))
											 ->first();
					if ( $batch_route ) {
						$product->batch_route_id = $batch_route->id;
					} else {
						$product->batch_route_id = Helper::getDefaultRouteId();
					}
				} elseif ( $column == 'is_deleted' ) {
					$product->is_deleted = $row['is_deleted'] ? 1 : 0;
				} elseif ( $column == 'product_master_category' ) {
					$master_category_from_file = trim($row['product_master_category']);
					$master_category_from_table = MasterCategory::where('master_category_code', $master_category_from_file)
																->where('is_deleted', 0)
																->first();
					if ( $master_category_from_table ) {
						$product->product_master_category = $master_category_from_table->id;
					} else {
						$product->product_master_category = null;
					}
				} elseif ( $column == 'product_category' ) {
					$category_from_file = trim($row['product_category']);
					$category_from_table = Category::where('category_code', $category_from_file)
												   ->where('is_deleted', 0)
												   ->first();
					if ( $category_from_table ) {
						$product->product_category = $category_from_table->id;
					} else {
						$product->product_category = null;
					}
				} elseif ( $column == 'product_sub_category' ) {
					$sub_category_from_file = trim($row['product_sub_category']);
					$sub_category_from_table = SubCategory::where('sub_category_code', $sub_category_from_file)
														  ->where('is_deleted', 0)
														  ->first();
					if ( $sub_category_from_table ) {
						$product->product_sub_category = $sub_category_from_table->id;
					} else {
						$product->product_sub_category = null;
					}
				} elseif ( $column == 'product_production_category' ) {
					$production_category_from_file = trim($row['product_production_category']);
					$production_category_from_table = ProductionCategory::where('production_category_code', $production_category_from_file)
																		->where('is_deleted', 0)
																		->first();
					if ( $production_category_from_table ) {
						$product->product_production_category = $production_category_from_table->id;
					} else {
						$product->product_production_category = null;
					}
				} else {
					$product->$column = trim($row[$column]);
				}
				/*$product->store_id = $row['store_id'];
				$product->product_name = $row['product_name'];
				$product->product_model = $row['product_model'];
				$product->product_keywords = $row['product_keywords'];
				$product->product_description = $row['product_description'];
				$product->product_category = $row['product_category'];
				$product->product_sub_category = $row['product_sub_category'];
				$product->product_price = $row['product_price'];
				$product->product_url = $row['product_url'];
				$product->product_thumb = $row['product_thumb'];
				$batch_route = BatchRoute::where('batch_code', 'LIKE', sprintf("%%%s%%", trim($row['batch_route_id'])))
										 ->first();
				if ( $batch_route ) {
					$product->batch_route_id = $batch_route->id;
				}*/
			}
			$product->save();
		}
		session()->flash('success', 'Product is successfully added');

		return redirect(url('products'));
	}

	public function export ()
	{
		$columns = Product::getTableColumns();
		$products = Product::with('batch_route', 'master_category', 'category', 'sub_category', 'production_category')
						   ->get($columns);

		$file_path = sprintf("%s/assets/exports/products/", public_path());
		$file_name = sprintf("products-%s-%s.csv", date("y-m-d", strtotime('now')), str_random(5));
		$fully_specified_path = sprintf("%s%s", $file_path, $file_name);

		$csv = Writer::createFromFileObject(new \SplFileObject($fully_specified_path, 'a+'), 'w');

		$csv->insertOne($columns);

		foreach ( $products as $product ) {
			$row = [ ];
			foreach ( $columns as $column ) {
				if ( $column == 'batch_route_id' ) {
					if ( $product->batch_route_id ) {
						#$route_name = $product->batch_route->batch_code;
						$row[] = $product->batch_route->batch_code;
						#$row[] = BatchRoute::find($product->batch_route_id)->batch_code;
					} else {
						$row[] = '';
					}
					continue;
				} elseif ( $column == 'is_taxable' ) {
					$row[] = ( $product->is_taxable == 1 ) ? 'Yes' : 'No';
				} elseif ( $column == 'product_master_category' ) {
					$row[] = $product->master_category ? $product->master_category->master_category_code : '';
				} elseif ( $column == 'product_category' ) {
					$row[] = $product->category ? $product->category->category_code : '';
				} elseif ( $column == 'product_sub_category' ) {
					$row[] = $product->sub_category ? $product->sub_category->sub_category_code : '';
				} elseif ( $column == 'product_production_category' ) {
					$row[] = $product->production_category ? $product->production_category->production_category_code : '';
				} else {
					$row[] = $product->$column;
				}
			}
			$csv->insertOne($row);
		}

		return response()->download($fully_specified_path);
	}
}
