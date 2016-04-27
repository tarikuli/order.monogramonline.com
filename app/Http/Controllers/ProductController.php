<?php namespace App\Http\Controllers;

use App\BatchRoute;
use App\Category;
use App\Image;
use App\MasterCategory;
use App\Occasion;
use App\Product;
use App\ProductionCategory;
use App\SalesCategory;
use App\Specification;
use App\Store;
use App\SubCategory;
use App\Collection as CollectionModel;

use App\Vendor;
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
use Monogram\ApiClient;
use Monogram\Helper;

class ProductController extends Controller
{
	private $store_id = null;

	public function index (Request $request)
	{
		$products = Product::with('batch_route', 'master_category')
						   ->where('is_deleted', 0)/*->searchIdCatalog($request->get('id_catalog'))
						   ->searchProductModel($request->get('product_model'))
						   ->searchProductName($request->get('product_name'))*/
						   ->searchInOption($request->get('search_in'), $request->get("search_for"))
						   ->searchProductSalesCategory($request->get('product_sales_category'))
						   ->searchRoute($request->get('route'))
						   ->searchProductionCategory($request->get('product_production_category'))
						   ->searchProductOccasion($request->get('product_occasion'))
						   ->searchProductCollection($request->get('product_collection'))
						   ->searchMasterCategory($request->get('product_master_category'))/*->searchCategory($request->get('product_category'))
						   ->searchSubCategory($request->get('product_sub_category'))*/
						   ->latest()
						   ->paginate(50);

		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->orderBy('batch_route_name')
								  ->lists('batch_route_name', 'id');

		$searchInRoutes = Collection::make($batch_routes);
		$searchInRoutes->prepend('All', '0');

		$batch_routes->prepend('Not selected', 'null');

		/*$product_master_category = MasterCategory::where('is_deleted', 0)
												 ->lists('master_category_description', 'id')
												 ->prepend('All', 0);*/
		$master_categories = (new MasterCategoryController())->getChildCategories();

		$product_category = Category::where('is_deleted', 0)
									->lists('category_description', 'id')
									->prepend('All', 0);

		$product_sub_category = SubCategory::where('is_deleted', 0)
										   ->lists('sub_category_description', 'id')
										   ->prepend('All', 0);

		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->lists('production_category_description', 'id')
												   ->prepend('All', 0);

		$product_occasions = Occasion::where('is_deleted', 0)
									 ->lists('occasion_description', 'id')
									 ->prepend('All', '0');
		$product_collections = CollectionModel::where('is_deleted', 0)
											  ->lists('collection_description', 'id')
											  ->prepend('All', '0');
		$sales_categories = SalesCategory::where('is_deleted', 0)
										 ->lists('sales_category_description', 'sales_category_code')
										 ->prepend('Select sales category', 'all');
		$count = 1;

		#return $products;
		return view('products.index', compact('products', 'product_occasions', 'product_collections', 'count', 'batch_routes', 'request', 'searchInRoutes', 'product_master_category', 'product_category', 'product_sub_category', 'production_categories'))
			->with('categories', $master_categories)
			->with('id', 0)
			->with('sales_categories', $sales_categories);
	}

	public function create ()
	{
		$stores = Store::where('is_deleted', 0)
					   ->lists('store_name', 'store_id');

		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->orderBy('batch_route_name')
								  ->lists('batch_route_name', 'id')
								  ->prepend('Select a Route', '');
		$is_taxable = [
			'1' => 'Yes',
			'0' => 'No',
		];
		$vendors = Vendor::where('is_deleted', 0)
						 ->lists('vendor_name', 'id')
						 ->prepend('Select Supplier', '');
		/*$master_categories = MasterCategory::where('is_deleted', 0)
										   ->lists('master_category_description', 'id')
										   ->prepend('Select category', '');*/
		$master_categories = (new MasterCategoryController())->getChildCategories();

		$categories = Category::where('is_deleted', 0)
							  ->lists('category_description', 'id')
							  ->prepend('Select sub category 1', '');

		$sub_categories = SubCategory::where('is_deleted', 0)
									 ->lists('sub_category_description', 'id')
									 ->prepend('Select sub category 2', '');

		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->lists('production_category_description', 'id')
												   ->prepend('Select production category', '');

		$product_occasions = Occasion::where('is_deleted', 0)
									 ->lists('occasion_description', 'id');
		#->prepend('Select occasion', '');
		$product_collections = CollectionModel::where('is_deleted', 0)
											  ->lists('collection_description', 'id');
		#->prepend('Select collection', '');
		$sales_categories = SalesCategory::where('is_deleted', 0)
										 ->lists('sales_category_description', 'id')
										 ->prepend('Select sales category', '');

		#return $product_occasions;
		$financial_categories = [
			'0' => 'Select a financial category',
		];

		return view('products.create', compact('title', 'stores', 'product_occasions', 'product_collections', 'batch_routes', 'is_taxable', 'master_categories', 'categories', 'sub_categories', 'production_categories'))
			->with('categories', $master_categories)
			->with('id', 0)
			->with('sales_categories', $sales_categories)
			->with('vendors', $vendors)
			->with('financial_categories', $financial_categories);
	}

	public function store (ProductAddRequest $request)
	{
		#return $request->all();
		$master_category_id = $request->get('product_master_category');
		$master_category = MasterCategory::where('is_deleted', 0)
										 ->where('id', $master_category_id)
										 ->first();
		if ( !$master_category ) {
			return redirect()
				->back()
				->withInput()
				->withErrors([
					'error' => 'Selected category is invalid',
				]);
		}
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
		if ( $request->exists('product_upc') ) {
			$product->product_upc = trim($request->get('product_upc'));
		}
		if ( $request->exists('product_asin') ) {
			$product->product_asin = trim($request->get('product_asin'));
		}
		if ( $request->exists('product_brand') ) {
			$product->product_brand = trim($request->get('product_brand'));
		}
		if ( $request->exists('product_availability') ) {
			$product->product_availability = trim($request->get('product_availability'));
		}
		if ( $request->exists('vendor_id') ) {
			$product->vendor_id = $request->get('vendor_id');
		}
		if ( $request->exists('financial_category') ) {
			$product->financial_category = $request->get('financial_category');
		}
		if ( $request->exists('product_default_cost') ) {
			$product->product_default_cost = intval($request->get('product_default_cost'));
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
		/*if ( $request->exists('product_category') ) {
			$product->product_category = intval($request->get('product_category'));
		}
		if ( $request->exists('product_sub_category') ) {
			$product->product_sub_category = intval($request->get('product_sub_category'));
		}*/
		if ( $request->exists('product_production_category') ) {
			$product->product_production_category = intval($request->get('product_production_category'));
		}
		if ( $request->exists('product_sales_category') ) {
			$product->product_sales_category = intval($request->get('product_sales_category'));
		}
		/*if ( $request->exists('product_collection') ) {
			$product->product_collection = intval($request->get('product_collection'));
		}
		if ( $request->exists('product_occasion') ) {
			$product->product_occasion = intval($request->get('product_occasion'));
		}*/
		if ( $request->exists('product_price') ) {
			$product->product_price = floatval($request->get('product_price'));
		}
		if ( $request->exists('product_sale_price') ) {
			$product->product_sale_price = floatval($request->get('product_sale_price'));
		}
		if ( $request->exists('product_wholesale_price') ) {
			$product->product_wholesale_price = $request->get('product_wholesale_price');
		}
		if ( $request->exists('product_thumb') ) {
			$product->product_thumb = $request->get('product_thumb');
		}
		if ( $request->exists('product_video') ) {
			$product->product_video = $request->get('product_video');
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
		if ( $request->exists('product_drop_shipper') ) {
			$product->product_drop_shipper = $request->get('product_drop_shipper') ? 1 : 0;
		}
		if ( $request->exists('is_deleted') ) {
			$product->is_deleted = $request->get('is_deleted') ? 1 : 0;
		}
		if ( $request->exists('is_royalties') ) {
			$product->is_royalties = $request->get('is_royalties') ? 1 : 0;
		}
		if ( $request->exists('product_royalty_paid') ) {
			$product->product_royalty_paid = $request->get('product_royalty_paid');
		}
		if ( $request->exists('product_keywords') ) {
			$product->product_keywords = trim($request->get('product_keywords'));
		}
		if ( $request->exists('product_description') ) {
			$product->product_description = trim($request->get('product_description'));
		}
		if ( $request->exists('product_note') ) {
			$product->product_note = trim($request->get('product_note'));
		}
		if ( $request->exists('height') ) {
			$product->height = floatval($request->get('height'));
		}
		if ( $request->exists('width') ) {
			$product->width = floatval($request->get('width'));
		}
		$product->save();
		if ( $request->hasFile('images') ) {
			$this->image_manipulator($request->file('images'), $product);
		}
		if ( $request->exists('product_collection') && is_array($request->get('product_collection')) ) {
			// check if product collection is an array
			// check if the given collection is valid
			$collections = CollectionModel::whereIn('id', $request->get('product_collection'))
										  ->lists('id')
										  ->toArray();
			if ( $collections ) {
				$product->collections()
						->attach($collections);
			}

			#$product->product_collection = intval($request->get('product_collection'));
		}
		if ( $request->exists('product_occasion') && is_array($request->get('product_occasion')) ) {
			$occasions = Occasion::whereIn('id', $request->get('product_occasion'))
								 ->lists('id')
								 ->toArray();
			if ( $occasions ) {
				$product->occasions()
						->attach($occasions);
			}
			#$product->product_occasion = intval($request->get('product_occasion'));
		}

		session()->flash('success', 'Product is successfully added.');

		return redirect(url('products'));
	}

	public function show ($id)
	{
		// if searching for inactive or deleted product
		$product = Product::with('store', 'images', 'batch_route', 'master_category', 'production_category', 'collections', 'vendor', 'sales_category')
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
		$product = Product::with('batch_route', 'specifications', 'images')
						  ->where('is_deleted', 0)
						  ->find($id);
		$category_hierarchy = new Collection();
		Helper::getCategoryHierarchy($product->product_master_category, $category_hierarchy);

		$textual_hierarchy_array = array_map(function ($category) {
			return $category['master_category_description'];
		}, $category_hierarchy->reverse()
							  ->toArray());

		$textual_hierarchy = implode(" > ", $textual_hierarchy_array);

		if ( !$product ) {
			return view('errors.404');
		}
		$stores = Store::where('is_deleted', 0)
					   ->lists('store_name', 'store_id');

		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->orderBy('batch_route_name')
								  ->lists('batch_route_name', 'id')
								  ->prepend('Select a Route', '');
		$is_taxable = [
			'1' => 'Yes',
			'0' => 'No',
		];
		$vendors = Vendor::where('is_deleted', 0)
						 ->lists('vendor_name', 'id')
						 ->prepend('Select Supplier', '');
		/*$master_categories = MasterCategory::where('is_deleted', 0)
										   ->lists('master_category_description', 'id')
										   ->prepend('Select category', '');*/
		$master_categories = (new MasterCategoryController())->getChildCategories();

		$categories = Category::where('is_deleted', 0)
							  ->lists('category_description', 'id')
							  ->prepend('Select sub category 1', '');

		$sub_categories = SubCategory::where('is_deleted', 0)
									 ->lists('sub_category_description', 'id')
									 ->prepend('Select sub category 2', '');

		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->lists('production_category_description', 'id')
												   ->prepend('Select production category', '');

		$product_occasions = Occasion::where('is_deleted', 0)
									 ->lists('occasion_description', 'id');
		#->prepend('Select occasion', '');
		$product_collections = CollectionModel::where('is_deleted', 0)
											  ->lists('collection_description', 'id');
		#->prepend('Select collection', '');
		$sales_categories = SalesCategory::where('is_deleted', 0)
										 ->lists('sales_category_description', 'id')
										 ->prepend('Select sales category', '');

		#return $product_occasions;
		$financial_categories = [
			'0' => 'Select a financial category',
		];

		return view('products.edit', compact('product', 'title', 'stores', 'product_occasions', 'product_collections', 'batch_routes', 'is_taxable', 'master_categories', 'categories', 'sub_categories', 'production_categories'))
			->with('categories', $master_categories)
			->with('textual_hierarchy', $textual_hierarchy)
			->with('id', 0)
			->with('sales_categories', $sales_categories)
			->with('vendors', $vendors)
			->with('financial_categories', $financial_categories);
		/* previous values */
		/*$stores = Store::where('is_deleted', 0)
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
		$master_categories = (new MasterCategoryController())->getChildCategories();

		$categories = Category::where('is_deleted', 0)
							  ->lists('category_description', 'id')
							  ->prepend('Select sub category 1', '');

		$sub_categories = SubCategory::where('is_deleted', 0)
									 ->lists('sub_category_description', 'id')
									 ->prepend('Select sub category 2', '');

		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->lists('production_category_description', 'id')
												   ->prepend('Select production category', '');

		$product_occasions = Occasion::where('is_deleted', 0)
									 ->lists('occasion_description', 'id')
									 ->prepend('Select occasion', '');
		$product_collections = CollectionModel::where('is_deleted', 0)
											  ->lists('collection_description', 'id')
											  ->prepend('Select collection', '');

		$is_taxable = [
			'1' => 'Yes',
			'0' => 'No',
		];

		return view('products.edit', compact('product', 'stores', 'product_occasions', 'product_collections', 'batch_routes', 'is_taxable', 'master_categories', 'categories', 'sub_categories', 'production_categories'))
			->with('categories', $master_categories)
			->with('id', 0);*/
	}

	public function update (ProductUpdateRequest $request, $id)
	{
		$master_category_id = $request->get('product_master_category');
		$master_category = MasterCategory::where('is_deleted', 0)
										 ->where('id', $master_category_id)
										 ->first();
		if ( !$request->ajax() && !$master_category ) {
			return redirect()
				->back()
				->withInput()
				->withErrors([
					'error' => 'Selected category is invalid',
				]);
		}

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
		if ( $request->has('allow_mixing') ) {
			$product->allow_mixing = $request->get('allow_mixing') == 1 ? 1 : 0;
		}
		/*if ( $request->exists('product_category') ) {
			$product->product_category = $request->get('product_category');
		}
		if ( $request->exists('product_sub_category') ) {
			$product->product_sub_category = $request->get('product_sub_category');
		}*/
		if ( $request->exists('product_upc') ) {
			$product->product_upc = trim($request->get('product_upc'));
		}
		if ( $request->exists('product_asin') ) {
			$product->product_asin = trim($request->get('product_asin'));
		}
		if ( $request->exists('product_brand') ) {
			$product->product_brand = trim($request->get('product_brand'));
		}
		if ( $request->exists('product_availability') ) {
			$product->product_availability = trim($request->get('product_availability'));
		}
		if ( $request->exists('financial_category') ) {
			$product->financial_category = $request->get('financial_category');
		}
		if ( $request->exists('product_default_cost') ) {
			$product->product_default_cost = intval($request->get('product_default_cost'));
		}
		if ( $request->exists('product_wholesale_price') ) {
			$product->product_wholesale_price = $request->get('product_wholesale_price');
		}
		if ( $request->exists('is_royalties') ) {
			$product->is_royalties = $request->get('is_royalties') ? 1 : 0;
		}
		if ( $request->exists('product_royalty_paid') ) {
			$product->product_royalty_paid = $request->get('product_royalty_paid');
		}
		if ( $request->exists('product_video') ) {
			$product->product_video = $request->get('product_video');
		}
		if ( $request->exists('product_master_category') ) {
			$product->product_master_category = $request->get('product_master_category');
		}
		if ( $request->exists('product_production_category') ) {
			$product->product_production_category = intval($request->get('product_production_category'));
		}
		if ( $request->exists('product_sales_category') ) {
			$product->product_sales_category = intval($request->get('product_sales_category'));
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
		if ( $request->exists('product_drop_shipper') ) {
			$product->product_drop_shipper = $request->get('product_drop_shipper') ? 1 : 0;
		}
		if ( $request->exists('product_keywords') ) {
			$product->product_keywords = trim($request->get('product_keywords'));
		}
		if ( $request->exists('product_description') ) {
			$product->product_description = trim($request->get('product_description'));
		}
		if ( $request->exists('product_note') ) {
			$product->product_note = trim($request->get('product_note'));
		}
		if ( $request->exists('height') ) {
			$product->height = floatval($request->get('height'));
		}
		if ( $request->exists('width') ) {
			$product->width = floatval($request->get('width'));
		}

		$product->save();

		if ( $request->hasFile('images') ) {
			$this->image_manipulator($request->file('images'), $product);
		}

		// grab yahoo/custom data fields from request
		$customData = [ ];
		// TODO: how to check the second level json?
		foreach ( $request->all() as $key => $value ) {
			if ( strpos($key, "custom-") !== false ) {
				$name = substr($key, strlen("custom-"));
				$customData[$name] = $value;
			}
		}

		#return $customData;

		// if the request is not for updating batch
		if ( !$request->exists('update_batch') ) {
			// if the request has collection
			if ( $request->exists('product_collection') && is_array($request->get('product_collection')) ) {
				// check if product collection is an array
				// check if the given collection is valid
				$collections = CollectionModel::whereIn('id', $request->get('product_collection'))
											  ->lists('id')
											  ->toArray();
				if ( $collections ) {
					$product->collections()
							->sync($collections);
				}

				#$product->product_collection = intval($request->get('product_collection'));
			} else {
				// the update request has no collection request sent over the form
				$product->collections()
						->detach();
			}

			// if the product occasions are array and exist
			if ( $request->exists('product_occasion') && is_array($request->get('product_occasion')) ) {
				$occasions = Occasion::whereIn('id', $request->get('product_occasion'))
									 ->lists('id')
									 ->toArray();
				if ( $occasions ) {
					$product->occasions()
							->sync($occasions);
				}
				#$product->product_occasion = intval($request->get('product_occasion'));
			} else {
				// the update request has no occasion request sent over the form
				$product->occasions()
						->detach();
			}
		}
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
		$products = Product::with('batch_route', 'master_category')
						   ->where(function ($query) {
							   return $query->whereNull('batch_route_id')
											->orWhere('batch_route_id', Helper::getDefaultRouteId());
						   })
						   ->where('is_deleted', 0)
						   ->searchInOption($request->get('search_in'), $request->get("search_for"))
						   ->searchProductSalesCategory($request->get('product_sales_category'))
						   ->searchRoute($request->get('route'))
						   ->searchProductionCategory($request->get('product_production_category'))
						   ->searchProductOccasion($request->get('product_occasion'))
						   ->searchProductCollection($request->get('product_collection'))
						   ->searchMasterCategory($request->get('product_master_category'))/*->searchCategory($request->get('product_category'))
						   ->searchSubCategory($request->get('product_sub_category'))*/
						   ->latest()
						   ->paginate(50);

		/*$products = Product::with('batch_route', 'master_category')
						   ->where('is_deleted', 0)
						   ->whereNull('batch_route_id')
						   ->orWhere('batch_route_id', Helper::getDefaultRouteId())
						   ->searchInOption($request->get('search_in'), $request->get("search_for"))
						   ->searchProductSalesCategory($request->get('product_sales_category'))
						   ->searchProductOccasion($request->get('product_occasion'))
						   ->searchProductCollection($request->get('product_collection'))
						   ->latest()
						   ->paginate(50);*/
		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->orderBy('batch_route_name')
								  ->lists('batch_route_name', 'id');

		$searchInRoutes = Collection::make($batch_routes);
		$searchInRoutes->prepend('All', '0');

		$batch_routes->prepend('Not selected', 'null');

		$master_categories = (new MasterCategoryController())->getChildCategories();

		$product_category = Category::where('is_deleted', 0)
									->lists('category_description', 'id')
									->prepend('All', 0);

		$product_sub_category = SubCategory::where('is_deleted', 0)
										   ->lists('sub_category_description', 'id')
										   ->prepend('All', 0);

		$production_categories = ProductionCategory::where('is_deleted', 0)
												   ->lists('production_category_description', 'id')
												   ->prepend('All', 0);


		$product_occasions = Occasion::where('is_deleted', 0)
									 ->lists('occasion_description', 'id')
									 ->prepend('All', '0');
		$product_collections = CollectionModel::where('is_deleted', 0)
											  ->lists('collection_description', 'id')
											  ->prepend('All', '0');
		$count = 1;

		$sales_categories = SalesCategory::where('is_deleted', 0)
										 ->lists('sales_category_description', 'sales_category_code')
										 ->prepend('Select sales category', 'all');

		return view('products.index', compact('products', 'count', 'product_occasions', 'product_collections', 'batch_routes', 'request', 'searchInRoutes', 'product_master_category', 'product_category', 'product_sub_category', 'production_categories'))
			->with('categories', $master_categories)
			->with('id', 0)
			->with('sales_categories', $sales_categories);
	}

	public function change_mixing_status (Request $request)
	{
		if ( !$request->ajax() ) {
			return redirect()->back();
		}
		$mixing_status = $request->get('mixing_status', 1);
		$mixing_status = in_array($mixing_status, [
			0,
			1,
		]) ? $mixing_status : 1;
		$product_id = $request->get('id', 0);

		$product = Product::find($product_id);

		if ( !$product ) {
			return response()->json([
				'error' => true,
				'data'  => 'Not a valid product id',
			], 422);
		}

		$product->allow_mixing = $mixing_status;
		$product->save();

		return response()->json([
			'error' => false,
			'data'  => 'Allow mixing status changed successfully',
		], 200);
	}

	public function import (Request $request)
	{
		$file = $request->file('csv_file');

		$mimes = [
			'application/vnd.ms-excel',
			'application/octet-stream',
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
		$extra_columns = [
			'product_occasions',
			'product_collections',
		];
		$needed_columns = array_merge($table_columns, $extra_columns);
		$csv_columns = array_filter($reader->fetchOne());
		#dd(array_diff($needed_columns, $csv_columns));
		if ( count(array_intersect($needed_columns, $csv_columns)) != count($needed_columns) ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([
					'error' => 'CSV file columns don\'t match. Import stopped.',
				]));
		}
		#dd($table_columns, $needed_columns, $csv_columns, count(array_intersect($needed_columns, $csv_columns)));
		$rows = $reader->setOffset(1)
					   ->fetchAssoc($needed_columns);

		set_time_limit(0);
		foreach ( $rows as $row ) {
			$id_catalog = trim($row['id_catalog']);
			$model = trim($row['id_catalog']);
			if ( empty( $id_catalog ) || empty( $model ) ) {
				continue;
			}
			$product = Product::where('id_catalog', $row['id_catalog'])
							  ->first();
			if ( !$product ) {
				$product = new Product();
				$product->id_catalog = $row['id_catalog'];
				$product->product_model = $row['product_model'];
			}
			foreach ( $table_columns as $column ) {
				if ( $column == 'id_catalog' ) {
					continue;
				} elseif ( $column == 'product_model' ) {
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
				} elseif ( $column == 'product_drop_shipper' ) {
					$product->product_drop_shipper = $row['product_drop_shipper'] ? 1 : 0;
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
				}/* elseif ( $column == 'product_occasions' ) {
					$product_occasions_from_file = trim($row['product_occasions']);
					$product_occasion_from_table = Occasion::whereIn('occasion_code', $product_occasions_from_file)
														   ->where('is_deleted', 0)
														   ->get();
					if ( $product_occasion_from_table ) {
						$product->product_occasion = $product_occasion_from_table->id;
					} else {
						$product->product_occasion = null;
					}
				} elseif ( $column == 'product_collections' ) {
					$product_collection_from_file = trim($row['product_collection']);
					$product_collection_from_table = CollectionModel::where('collection_code', $product_collection_from_file)
																	->where('is_deleted', 0)
																	->first();
					if ( $product_collection_from_table ) {
						$product->product_collection = $product_collection_from_table->id;
					} else {
						$product->product_collection = null;
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
				} */ elseif ( $column == 'product_production_category' ) {
					$production_category_from_file = trim($row['product_production_category']);
					$production_category_from_table = ProductionCategory::where('production_category_code', $production_category_from_file)
																		->where('is_deleted', 0)
																		->first();
					if ( $production_category_from_table ) {
						$product->product_production_category = $production_category_from_table->id;
					} else {
						$product->product_production_category = null;
					}
				} elseif ( $column == 'product_sales_category' ) {
					$sales_category_from_file = trim($row['product_sales_category']);
					$sales_category_from_table = SalesCategory::where('sales_category_code', $sales_category_from_file)
															  ->where('is_deleted', 0)
															  ->first();
					if ( $sales_category_from_table ) {
						$product->product_sales_category = $sales_category_from_table->id;
					} else {
						$product->product_sales_category = null;
					}
				} else {
					if ( $column == "product_note" ) {
						#dd($row[$column]);
					}
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
			foreach ( $extra_columns as $column ) {
				if ( $column == 'product_occasions' ) {
					$occasionsFromFile = trim($row['product_occasions']);
					$occasionsArray = explode(",", str_replace(" ", "", $occasionsFromFile));
					$occasionsInTable = Occasion::whereIn("occasion_code", $occasionsArray)
												->lists('id')
												->toArray();
					$product->occasions()
							->sync($occasionsInTable);
				} elseif ( $column == 'product_collections' ) {
					$collectionsFromFile = trim($row['product_collections']);
					$collectionsArray = explode(",", str_replace(" ", "", $collectionsFromFile));
					$collectionsInTable = CollectionModel::whereIn("collection_code", $collectionsArray)
														 ->lists('id')
														 ->toArray();
					#dd($collectionsInTable);
					$product->collections()
							->sync($collectionsInTable);
				}
			}
		}
		session()->flash('success', 'Product is successfully added');

		return redirect(url('products'));
	}

	public function export ()
	{
		$table_columns = Product::getTableColumns();
		$extra_columns = [
			'product_occasions',
			'product_collections',
			'product_images',
		];
		$columns = array_merge($table_columns, $extra_columns);
		#$products = Product::with('batch_route', 'master_category', 'category', 'sub_category', 'production_category', 'product_occasion_details', 'product_collection_details')->get($columns);
		$products = Product::with('batch_route', 'master_category', 'production_category', 'sales_category', 'images')
						   ->where('is_deleted', 0)
						   ->get();
		#->get(array_merge([ 'id' ], $table_columns));
		$file_path = sprintf("%s/assets/exports/products/", public_path());
		$file_name = sprintf("products-%s-%s.csv", date("y-m-d", strtotime('now')), str_random(5));
		$fully_specified_path = sprintf("%s%s", $file_path, $file_name);

		$csv = Writer::createFromFileObject(new \SplFileObject($fully_specified_path, 'w+'), 'w');
		$csv->insertOne($columns);

		set_time_limit(0);
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
				} elseif ( $column == 'product_sales_category' ) {
					$row[] = $product->sales_category ? $product->sales_category->sales_category_code : '';
				} elseif ( $column == 'product_category' ) {
					continue;
					$row[] = $product->category ? $product->category->category_code : '';
				} elseif ( $column == 'product_sub_category' ) {
					continue;
					$row[] = $product->sub_category ? $product->sub_category->sub_category_code : '';
				} elseif ( $column == 'product_production_category' ) {
					$row[] = $product->production_category ? $product->production_category->production_category_code : '';
				} elseif ( $column == 'product_collections' ) {
					// get collections and insert
					$row[] = implode(",", $product->collections->lists('collection_code')
															   ->all());
					/*continue;
					$row[] = $product->product_collection ? $product->product_collection_details->collection_code : '';*/
				} elseif ( $column == 'product_occasions' ) {
					// get occasions and insert
					$row[] = implode(",", $product->occasions->lists('occasion_code')
															 ->all());
					/*continue;
					$row[] = $product->product_occasion ? $product->product_occasion_details->occasion_code : '';*/
				} elseif ( $column == 'product_images' ) {
					$row[] = implode(",", $product->images->lists('path')
														  ->toArray());
				} else {
					$row[] = $product->$column;
				}
			}
			$csv->insertOne($row);
		}

		return response()->download($fully_specified_path);
	}

	public function getSync ()
	{
		$stores = Store::where('is_deleted', 0)
					   ->lists('store_name', 'store_id');

		return view('products.add', compact('stores'));
	}

	public function postSync (Request $request)
	{
		#return $request->all();
		$id_catalogs = [ ];
		if ( $request->exists('sync_all') && ( $request->get('sync_all') == 1 ) ) {
			$id_catalogs = Product::where('is_deleted', 0)
								  ->where('store_id', $request->get('store'))
								  ->lists('id_catalog')
								  ->toArray();
		} else {
			$id_catalog_input = trim(preg_replace('/\s+/', '', $request->get('product_id_catalogs')), ",");
			if ( $id_catalog_input == "" ) {
				return redirect()
					->back()
					->withInput()
					->withErrors([
						'error' => 'No id catalog is given',
					]);
			}
			$id_catalogs = explode(",", $id_catalog_input);
		}
		$needed_api = '';
		$store = $request->get('store');
		if ( strpos($store, "yhst") !== false ) {
			$needed_api = 'yahoo';
		}
		try {
			$api_client = new ApiClient($id_catalogs, $store, $needed_api, "sync");
		} catch ( \Exception $exception ) {
			return redirect()
				->back()
				->withInput()
				->withErrors(new MessageBag([ 'api_error' => 'Selected store is not valid' ]));
		}
		$responses = [ ];
		$errors = new Collection();
		list( $responses, $errors ) = $api_client->fetch_data();
		$count = count($id_catalogs);
		foreach ( $responses as $data ) {
			$this->store_id = $request->get('store');
			$id_catalog = $data[0];
			$response = $data[1];
			$success = $this->sync_product($response);
			if ( $success === false ) {
				$errors->add(sprintf("Insertion error: %s", $id_catalog), sprintf("Error occurred while reading data from api for id catalog : %s.", $id_catalog));
			}
		}
		if ( $errors->count() ) {
			return redirect()
				->back()
				->withErrors($errors);
		}

		Session::flash('success', sprintf('%d Product(s) are synced successfully.', ( count($responses) - $errors->count() )));

		// if the request is from products edit page,
		//redirect to there

		if ( $request->get('callee') ) {
			return redirect(url($request->get('callee')));
		}

		return redirect(url('products/sync'));
	}

	public function sync_product ($response)
	{
		$xml = simplexml_load_string($response);
		if ( $xml === false ) {
			return false;
		}
		if ( $xml->InfoMessages || $xml->ErrorMessages ) { // no item found
			return false;
		}
		foreach ( $xml->ResponseResourceList->Catalog->ItemList->children() as $item ) {
			$id_catalog = $item->ID;
			$product_name = $item->Name;
			$model = $item->Code->Value;
			// return false, if id catalog or model is null
			if ( is_null($id_catalog) || is_null($model) ) {
				return false;
			}
			$price = $item->Price;
			$sale_price = $item->SalePrice;
			$ship_weight = $item->ShipWeight;
			$orderable = $item->Orderable;
			$gift_cert = $item->GiftCert;
			$headline = $item->Headline;
			$caption = $item->Caption;
			$abstract = $item->Abstract;
			$label = $item->Label;
			$condition = $item->Condition;
			#dd($xml);
			$formatted_data = $this->fetch_custom_data($item->CustomData);
			$keywords = $formatted_data['keywords'];
			$description = $formatted_data['description'];
			$customData = $formatted_data['customData'];
			// TODO: Option list grabbing
			if ( $item->OptionList ) {
				$option_list = $this->fetch_option_list($item->OptionList->Option);
				$customData = array_merge($customData, $option_list);
			}

			$updates = [
				'product_name'        => $product_name,
				'product_model'       => $model,
				'product_price'       => $price,
				'product_sale_price'  => $sale_price,
				'product_keywords'    => $keywords,
				'product_description' => $description,
				'ship_weight'         => $ship_weight,
				'product_orderable'   => $orderable,
				'product_gift_cert'   => $gift_cert,
				'product_headline'    => $headline,
				'product_caption'     => $caption,
				'product_abstract'    => $abstract,
				'product_label'       => $label,
				'product_condition'   => $condition,
			];

			$product = Product::where('id_catalog', $id_catalog)
							  ->first();
			if ( $product ) {
				Product::where('id_catalog', $id_catalog)
					   ->update($updates);
			} else {
				$product = new Product();
				$product->store_id = $this->store_id;
				$product->id_catalog = $id_catalog;
				foreach ( $updates as $key => $value ) {
					$product->$key = $value;
				}
				$product->save();
			}

			$specification = Specification::where('id_catalog', $id_catalog)
										  ->first();
			if ( !$specification ) {
				$specification = new Specification();
				$specification->id_catalog = $id_catalog;
			}
			$specification->product_model = $model;
			$specification->product_id = $product->id;
			$specification->custom_data = json_encode($customData);
			$specification->save();
		}

		return true;
	}

	public function fetch_custom_data ($node)
	{
		$keywords = '';
		$description = '';
		$data = [ ];
		foreach ( $node as $cursor ) {
			$name = (string) $cursor->Name;
			$value = (string) $cursor->Value;
			if ( $name == 'keywords' ) {
				$keywords = $value;
			} elseif ( $name == 'description' ) {
				$description = $value;
			} else {
				$data[$name] = $value;
			}
		}

		return [
			'keywords'    => $keywords,
			'description' => $description,
			'customData'  => $data,
		];
	}

	public function fetch_option_list ($node) // $item->OptionList
	{
		$options = [ ];
		foreach ( $node as $cursor ) {
			$name = (string) $cursor->Name;
			$values_list = $cursor->ValueList ? (array) $cursor->ValueList->Value : [ ]; // if values list exist, cast it to array
			foreach ( $values_list as $value ) {
				$options[$name][] = (string) $value;
			}
		}

		return $options;
	}

	public function post_to_yahoo (Request $request)
	{
		return $request->all();
	}

	private function image_manipulator ($images, $product)
	{
		foreach ( $images as $image ) {
			if ( $image->isValid() ) {
				$destinationPath = 'assets/images/product_images';
				$extension = $image->getClientOriginalExtension();
				$fileName = sprintf("%s-%s.%s", $product->product_model, rand(11111, 99999), $extension);
				$image->move($destinationPath, $fileName);
				$product_image = new Image();
				$product_image->product_id = $product->id;
				$product_image->path = sprintf("%s/%s", url($destinationPath), $fileName);
				$product_image->save();
			}
		}
	}
}
