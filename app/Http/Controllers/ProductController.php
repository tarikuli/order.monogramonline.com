<?php

namespace App\Http\Controllers;

use App\BatchRoute;
use App\Category;
use App\MasterCategory;
use App\Product;
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

		$product_master_category = Product::groupBy('product_master_category')
										  ->lists('product_master_category', 'product_master_category')
										  ->prepend('Select a master category', 'all');

		$product_category = Product::groupBy('product_category')
								   ->lists('product_category', 'product_category')
								   ->prepend('Select a category', 'all');

		$product_sub_category = Product::groupBy('product_sub_category')
									   ->lists('product_sub_category', 'product_sub_category')
									   ->prepend('Select a sub category', 'all');

		$sub_categories = SubCategory::where('is_deleted', 0)
									 ->lists('sub_category_description', 'id')
									 ->prepend('All', 0);
		$count = 1;

		return view('products.index', compact('products', 'count', 'batch_routes', 'request', 'searchInRoutes', 'product_master_category', 'product_category', 'product_sub_category'));
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
										   ->lists('master_category_description', 'master_category_code')
										   ->prepend('Select category', '');

		$categories = Category::where('is_deleted', 0)
							  ->lists('category_description', 'category_code')
							  ->prepend('Select sub category 1', '');

		$sub_categories = SubCategory::where('is_deleted', 0)
									 ->lists('sub_category_description', 'sub_category_code')
									 ->prepend('Select sub category 2', '');

		return view('products.create', compact('title', 'stores', 'batch_routes', 'is_taxable', 'master_categories', 'categories', 'sub_categories'));
	}

	public function store (ProductAddRequest $request)
	{
		$product = new Product();
		$product->store_id = $request->get('store_id');
		$product->id_catalog = $request->get('id_catalog');
		$product->product_name = $request->get('product_name');
		$product->product_model = $request->get('product_model');
		$product->product_keywords = $request->get('product_keywords');
		$product->product_description = $request->get('product_description');
		$product->product_category = $request->get('product_category');
		$product->product_price = $request->get('product_price');
		$product->product_url = $request->get('product_url');
		$product->product_thumb = $request->get('product_thumb');
		$product->batch_route_id = $request->get('batch_route_id');
		$product->is_taxable = $request->get('is_taxable');
		$product->product_master_category = $request->get('master_category');
		$product->product_category = $request->get('category');
		$product->product_sub_category = $request->get('sub_category');
		/*$product->sale_price = $request->get('sale_price');
		$product->wPrice = $request->get('wPrice');
		$product->taxable = $request->get('taxable');
		$product->upc = $request->get('upc');
		$product->brand = $request->get('brand');
		$product->ASIN = $request->get('ASIN');
		$product->su_code = $request->get('su_code');
		$product->acct_code = $request->get('acct_code');
		$product->product_condition = $request->get('product_condition');
		$product->image_url_4P = $request->get('image_url_4P');
		$product->inset_url = $request->get('inset_url');*/
		$product->save();

		return redirect(url('products'));
	}

	public function show ($id)
	{
		// if searching for inactive or deleted product
		$product = Product::with('batch_route')
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
		// if searching for inactive or deleted product
		$product = Product::with('batch_route')
						  ->where('is_deleted', 0)
						  ->find($id);
		if ( !$product ) {
			return view('errors.404');
		}

		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->lists('batch_code', 'id');
		$master_categories = MasterCategory::where('is_deleted', 0)
										   ->lists('master_category_description', 'master_category_code')
										   ->prepend('Select category', '');

		$categories = Category::where('is_deleted', 0)
							  ->lists('category_description', 'category_code')
							  ->prepend('Select sub category 1', '');

		$sub_categories = SubCategory::where('is_deleted', 0)
									 ->lists('sub_category_description', 'sub_category_code')
									 ->prepend('Select sub category 2', '');
		$is_taxable = [
			'1' => 'Yes',
			'0' => 'No',
		];

		return view('products.edit', compact('product', 'batch_routes', 'is_taxable', 'master_categories', 'categories', 'sub_categories'));
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
		if ( $request->exists('product_name') ) {
			$product->product_name = $request->get('product_name');
		}
		if ( $request->exists('product_model') ) {
			$product->product_model = $request->get('product_model');
		}
		if ( $request->exists('product_keywords') ) {
			$product->product_keywords = $request->get('product_keywords');
		}
		if ( $request->exists('product_description') ) {
			$product->product_description = $request->get('product_description');
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
		if ( $request->exists('product_price') ) {
			$product->product_price = $request->get('product_price');
		}
		if ( $request->exists('product_url') ) {
			$product->product_url = $request->get('product_url');
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
				$is_error = true;
				$error_messages[] = [ 'batch_code' => 'Batch code is not correct' ];
			}
		}
		if ( $request->exists('is_taxable') ) {
			$product->is_taxable = $request->get('is_taxable');
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

		$product_master_category = Product::groupBy('product_master_category')
										  ->lists('product_master_category', 'product_master_category')
										  ->prepend('Select a master category', 'all');

		$product_category = Product::groupBy('product_category')
								   ->lists('product_category', 'product_category')
								   ->prepend('Select a category', 'all');

		$product_sub_category = Product::groupBy('product_sub_category')
									   ->lists('product_sub_category', 'product_sub_category')
									   ->prepend('Select a sub category', 'all');

		$sub_categories = SubCategory::where('is_deleted', 0)
									 ->lists('sub_category_description', 'id')
									 ->prepend('All', 0);
		$count = 1;

		return view('products.index', compact('products', 'count', 'batch_routes', 'request', 'searchInRoutes', 'product_master_category', 'product_category', 'product_sub_category'));

		/*$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->lists('batch_route_name', 'id');
		$searchInRoutes = Collection::make($batch_routes);
		$searchInRoutes->prepend('All', '0');

		$batch_routes->prepend('Not selected', 'null');

		$categories = Category::where('is_deleted', 0)
							  ->lists('category_description', 'id')
							  ->prepend('All', 0);
		$sub_categories = SubCategory::where('is_deleted', 0)
									 ->lists('sub_category_description', 'id')
									 ->prepend('All', 0);

		$count = 1;


		return view('products.index', compact('products', 'count', 'batch_routes', 'request', 'searchInRoutes', 'categories', 'sub_categories'));*/

		/*$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->lists('batch_route_name', 'id');
		#->lists('batch_code', 'id');

	    $searchInRoutes = Collection::make($batch_routes);
		$searchInRoutes->prepend('All', '0');

		$batch_routes->prepend('Not selected', 'null');
		$count = 1;

		return view('products.index', compact('products', 'count', 'batch_routes', 'request'));*/
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
		/*$columns = [
			'store_id',
			'id_catalog',
			'product_name',
			'product_model',
			'product_keywords',
			'product_description',
			'product_category',
			'product_sub_category',
			'product_price',
			'product_url',
			'product_thumb',
			'batch_route_id',
			'is_taxable',
		];*/
		$table_columns = Product::getTableColumns();
		$csv_columns = $reader->fetchOne();

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
		$products = Product::with('batch_route')
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
				} else {
					$row[] = $product->$column;
				}
			}
			$csv->insertOne($row);
		}

		return response()->download($fully_specified_path);
	}
}
