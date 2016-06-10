<?php namespace App\Http\Controllers;

use App\BatchRoute;
use App\Option;
use App\Parameter;
use App\Product;
use App\Store;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use League\Csv\Reader;
use League\Csv\Writer;
use Monogram\DOMReader;
use Monogram\Helper;
use Monogram\Phantom;

class LogisticsController extends Controller
{
	public function sku_converter (Request $request)
	{
		$store_id = $request->get('store_id');
		$stores = Store::where('is_deleted', 0)
					   ->latest()
					   ->lists('store_name', 'store_id')
					   ->prepend('Select a store', 'all');

		$parameters = new Collection();
		if ( $store_id ) {
			$parameters = Parameter::where('store_id', $store_id)
								   ->where('is_deleted', 0)
								   ->get();
		}
		$index = 1;

		return view('logistics.sku_converter', compact('stores', 'store_id', 'parameters', 'index'));
	}

	public function post_sku_converter (Request $request)
	{
		$store_id = $request->get('store_id');
		$store_name = $request->get('store_name');
		$store = Store::where('store_id', $store_id)
					  ->where('is_deleted', 0)
					  ->first();

		if ( !$store ) {
			$store = new Store();
			$store->store_id = $store_id;
			$store->store_name = $store_name;
			$store->save();
		}

		if ( $request->has('parameters') ) {
			$this->insert_parameters_into_table($request->get('parameters'), $store_id);
		}

		return redirect(url(sprintf('logistics/sku_converter?store_id=%s', $store_id)));
	}

	public function sku_converter_update (Request $request, $store_id)
	{
		#Parameter::where('store_id', $store_id)->delete();
		if ( $request->has('parameters') ) {
			/*foreach ( $request->get('parameters') as $parameter_value ) {
				$parameter = new Parameter();
				$parameter->store_id = $store_id;
				$parameter->parameter_value = trim($parameter_value);
				$parameter->save();
			}*/
			$this->insert_parameters_into_table($request->get('parameters'), $store_id);
		}

		return redirect(url(sprintf('logistics/sku_converter?store_id=%s', $store_id)));
	}

	private function insert_parameters_into_table ($parameters, $store_id)
	{
		Parameter::where('store_id', $store_id)
				 ->delete();
		// filter the empty values array_filter($parameters)
		// create new rows with parameter_value and store_id
		$rows = array_map(function ($row) use ($store_id) {
			return [
				'parameter_value' => trim($row),
				'store_id'        => $store_id,
			];
		}, array_filter($parameters));
		Parameter::insert($rows);
		/*$before_operation = [ ];
		$after_operation = [ ];

		// get the row ids before the update/insert
		$before_operation = Parameter::where('store_id', $store_id)
									 ->lists('id')
									 ->toArray();

		foreach ( $parameters as $parameter_value ) {
			$trimmed_parameter_value = trim($parameter_value);
			if ( empty( $trimmed_parameter_value ) ) {
				continue;
			}

			$parameter = Parameter::where(DB::raw('BINARY `parameter_value`'), $trimmed_parameter_value)// binary for case sensitivity
								  ->where('store_id', $store_id)
								  ->first();
			if ( !$parameter ) {
				$parameter = new Parameter();
				$parameter->store_id = $store_id;
				$parameter->parameter_value = trim($parameter_value);
				$parameter->save();
			}
			// gather the parameter ids while insert/update
			$after_operation[] = $parameter->id;
		}

		#delete the other ids
		$difference = array_diff($before_operation, $after_operation);
		if ( count($difference) ) {
			// if the difference is greater than 0
			// delete those ids
			Parameter::destroy(array_diff($before_operation, $after_operation));
		}*/

	}

	public function get_sku_import ()
	{
		/*$stores = Store::where('is_deleted', 0)
					   ->latest()
					   ->lists('store_name', 'store_id')
					   ->prepend('Select a store', 'all');*/
		$stores = Store::with('parameters')
					   ->where('is_deleted', 0)
					   ->latest()
					   ->get();

		$store_parameters = [ ];

		foreach ( $stores as $store ) {
			$list = "<ol class=\"list-group\">";
			foreach ( $store->parameters as $parameter ) {
				$list .= sprintf("<li class=\"list-group-item\">%s</li>", $parameter->parameter_value);
			}
			$list .= "</ol>";
			$store_parameters[$store->store_id] = $list;
		}

		#return $store_parameters;
		return view('logistics.sku_import', compact('stores', 'store_parameters'));
	}

	public function post_sku_import (Request $request)
	{
		$store_id = $request->get('store_id');
		if ( $request->get('action') == 'export' ) {
			$parameters = Parameter::where('store_id', $store_id)
								   ->orderBy('id')
								   ->get();
			if ( $parameters->count() == 0 ) {
				return redirect()
					->back()
					->withInput()
					->withErrors([
						'message' => 'No parameter is set for this store.',
					]);
			}
			$columns = $parameters->lists('parameter_value')
								  ->toArray();
			$options = Option::with('route')
							 ->where('store_id', $store_id)
							 ->get();
			$file_path = sprintf("%s/assets/exports/skus/", public_path());
			$file_name = sprintf("skus_exports-%s-%s.csv", date("y-m-d", strtotime('now')), str_random(5));
			$fully_specified_path = sprintf("%s%s", $file_path, $file_name);
			$extra_columns = Helper::$SKU_CONVERSION_EXTRA_COLUMNS;
			$columns = array_merge($extra_columns, $columns);

			$csv = Writer::createFromFileObject(new \SplFileObject($fully_specified_path, 'a+'), 'w');
			$csv->insertOne($columns);

			set_time_limit(0);
			foreach ( $options as $option ) {
				//$row = [ ];
				// option has parameter options in json format
				// extract it and find the values
				/*foreach ( json_decode($option->parameter_option, true) as $column_value ) {
					$row[] = $column_value->parameter_option;
				}*/
				$row = [ ];
				$row[] = $option->id_catalog;
				$row[] = $option->parent_sku;
				$row[] = $option->child_sku;
				$row[] = $option->graphic_sku;
				$row[] = $option->allow_mixing ? "Yes" : "No";
				$row[] = $option->route ? $option->route->batch_code : "";

				$json_array = json_decode($option->parameter_option, true);
				$json_array_except = array_filter($json_array, function ($key) {
					if ( $key == "SKU" ) {
						return false;
					}

					return true;
				}, ARRAY_FILTER_USE_KEY);

				$row = array_merge($row, $json_array_except);
				$csv->insertOne($row);
			}

			return response()->download($fully_specified_path);
		}

		// if the request is not export
		// proceed further.
		$file = $request->file('file');

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
				->withErrors(new MessageBag([ 'error' => 'Uploaded file is not a valid CSV file.' ]));
		}

		$file_path = sprintf("%s/assets/imports/products/", public_path());
		$file_name = $file->getClientOriginalName();
		$fully_specified_file_name = sprintf("%s%s", $file_path, $file_name);
		$file->move($file_path, $file_name);

		try {
			$reader = Reader::createFromPath($fully_specified_file_name);
		} catch ( \Exception $ex ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([ 'error' => 'Uploaded file is not a valid CSV file.' ]));
		}

		$row = $reader->fetchOne();

		$is_valid = Helper::validateSkuImportFile($store_id, $row);

		if ( !$is_valid ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([
					/*'error' => sprintf('Column names / values do not match with the parameters with < b>%s </b > store . ', Store::where('store_id', $store_id)
																															->first()->store_name),*/
					'error' => Helper::$error,
				]));
		}

		if ( $request->get('action') == 'validate' ) {
			Session::flash('success', sprintf("Uploaded file is a valid CSV file for <b>%s</b>", Store::where('store_id', $store_id)
																									  ->first()->store_name));

			return redirect()->back();
		}

		$message = '';
		if ( $request->get('action') == 'upload-bulk' ) {
			// if upload bulk,
			// delete all the saved data
			// and insert new
			Option::where('store_id', $store_id)
				  ->delete();
			// save csv values to database
			$this->save_parameters($reader, $store_id);
			// set message
			$message = 'Bulk upload is complete.';
		} elseif ( $request->get('action') == 'upload-new' ) {
			$this->save_parameters($reader, $store_id);
			$message = 'Upload new is complete.';
		} else {
			return redirect()
				->back()
				->withErrors(new MessageBag([
					'error' => sprintf('Sorry, The operation cannot be processed.'),
				]));
		}

		return redirect()
			->back()
			->with('success', $message);

	}

	private function save_parameters ($reader, $store_id)
	{
		try {

			$rows = $reader->setOffset(1)
						   ->fetchAssoc(Helper::$column_names);
			$batch_routes = BatchRoute::where('is_deleted', 0)
									  ->lists('id', 'batch_code');
			set_time_limit(0);
			foreach ( $rows as $row ) {
				$unique_row_value = Helper::generateUniqueRowId();
				$option = new Option();
				$option->store_id = $store_id;
				$option->unique_row_value = $unique_row_value;
				foreach ( Helper::$SKU_CONVERSION_EXTRA_COLUMNS as $column ) {
					if ( strtolower($column) == "batch route" ) {
						$option->batch_route_id = $batch_routes->get($row[$column]) ?: Helper::getDefaultRouteId();
					} elseif ( strtolower($column) == "allow mixing" ) {
						$option->allow_mixing = strtolower($row[$column]) == "yes" ? 1 : 0;
					} else {
						$to_table_field = str_replace(" ", "_", strtolower($column));
						$option->$to_table_field = $row[$column];
					}
				}
				$parameter_options = [ ];
				$jsonable_columns = array_diff(Helper::$column_names, Helper::$SKU_CONVERSION_EXTRA_COLUMNS);
				foreach ( $jsonable_columns as $column_name ) {
					$parameter_options[$column_name] = $row[$column_name];
				}
				$option->parameter_option = json_encode($parameter_options);
				$option->save();
			}
		} catch ( \Exception $e ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([
					'error' => sprintf($e),
				]));
		}

	}

	private function get_unique_id ()
	{
		return sprintf("%s_%s", strtotime("now"), str_random(5));
	}

	public function get_add_new_option (Request $request)
	{
		return $request->all();
	}

	public function get_sku_show (Request $request)
	{
		$store_id = $request->get('store_id');

		if($request->get('unassigned')){
			$unassigned = $request->get('unassigned');
		}else{
			$unassigned = 0;
		}

		$store = Store::where('store_id', $store_id)
					  ->where('is_deleted', 0)
					  ->first();

		$stores = Store::where('is_deleted', 0)
				->latest()
				->get();

		if ( !$store ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'Not a valid store selected',
				]);
		}

		// get the parameters - ie, columns of csv
		$parameters = Parameter::where('store_id', $store_id)
							   ->get();

		$searchable = new Collection($parameters->lists('parameter_value', 'parameter_value'));
		$searchable = (new Collection(array_combine([
			'id_catalog',
			'parent_sku',
			'child_sku',
			'graphic_sku',
		], [
			'ID Catalog',
			'Parent SKU',
			'Child SKU',
			'Graphic SKU',
		])))->merge($searchable);
		$searchable->prepend('Select a field', "");
		// get the values of the above columns
		// by parameter id relation
		// and paginate as of the length of the parameter
		// here in paginate, the multiple is the number of parameter column
		/*$relation_array = $parameters->lists('id')
									 ->toArray();*/
		/*$options = Option::whereIn('parameter_id', $relation_array)#->orderBy(DB::raw(sprintf('FIELD(parameter_id, %s)', implode(", ", $relation_array))))
						 ->paginate(50 * count($parameters));*/
		$options = Option::with('product', 'route.template')
						 ->where('store_id', $store_id) // Comment for view all store
						 ->searchUnassigned($request->get('unassigned'))
						 ->searchInParameterOption($store_id, $request->get('search_for'), $request->get('search_in'))
						 ->paginate(100);

		// split the url into parts
		$submit_url = sprintf("%s?store_id=%s", $request->url(), $store_id);

		#return $options;
		$returnTo = urlencode($request->fullUrl());

		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->orderBy('batch_route_name')
								  ->lists('batch_route_name', 'id')
								  ->prepend('Select a route', 0);
		#return $parameters;
		return view('logistics.sku_converter_store_details', compact('batch_routes', 'searchable', 'parameters', 'options', 'request', 'submit_url', 'store_id', 'returnTo','stores', 'unassigned'));

	}

	public function edit_sku_converter (Request $request)
	{
		$rules = [
			'store_id'         => 'required',
			'unique_row_value' => 'required',
		];

		$inputs = [
			'store_id'         => $request->get('store_id'),
			'unique_row_value' => $request->get('row'),
		];

		$validator = Validator::make($inputs, $rules);

		if ( $validator->fails() ) {
			return redirect()
				->back()
				->withErrors($validator);
		}

		$options = Option::where($inputs)
						 ->first();

		if ( !$options ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'Your input is wrong.',
				]);
		}
		#$decoded_options = json_decode($options->parameter_option, true);
		#$parameter_values = array_keys($decoded_options);
		$parameters = Parameter::where('store_id', $request->get('store_id'))
							   ->get();

		#$options = $options->toArray();
		#return $parameters;
		$returnTo = $request->get('return_to');
		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->orderBy('batch_route_name')
								  ->lists('batch_route_name', 'id');

		return view('logistics.edit_sku_converter', compact('options', 'parameters', 'returnTo', 'batch_routes'));
	}

	public function get_add_child_sku (Request $request)
	{
		$store_id = $request->get('store_id');
		$parameters = Parameter::where('store_id', $store_id)
							   ->get();
		$returnTo = $request->get('return_to');
		$batch_routes = BatchRoute::where('is_deleted', 0)
								  ->orderBy('batch_route_name')
								  ->lists('batch_route_name', 'id');

		return view('logistics.add_child_sku', compact('parameters', 'returnTo', 'store_id', 'batch_routes'));
	}

	public function post_add_child_sku (Request $request)
	{
		$rules = [
			'store_id'  => 'required',
			'child_sku' => 'required',
		];

		$inputs = [
			'store_id'  => $request->get('store_id'),
			'child_sku' => $request->get('child_sku'),
		];

		$validator = Validator::make($inputs, $rules);

		if ( $validator->fails() ) {
			return redirect()
				->back()
				->withErrors($validator);
		}
		$store_id = $request->get('store_id');
		$unique_row_value = Helper::generateUniqueRowId();

		$parameters = Parameter::where('store_id', $store_id)
							   ->get();
		if ( $parameters->count() == 0 ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'Not a valid store selected.',
				]);
		}

		// check if the code is found on request
		// match, if the code found
		// update the value
		$is_code_field_found = false;
		$code = '';
		$dataToStore = [ ];
		foreach ( $parameters as $parameter ) {
			$parameter_value = $parameter->parameter_value;
			$form_field = Helper::textToHTMLFormName($parameter_value);
			if ( $form_field == 'code' ) {
				$is_code_field_found = true;
				$code = $request->get($form_field, '');
			}
			$dataToStore[$parameter_value] = $request->get($form_field, '');
		}
		// check if the code is already existing on database or not
		$option = null;

		$parent_sku = trim($request->get('parent_sku'), '');
		$graphic_sku = trim($request->get('graphic_sku'), '');
		$child_sku = trim($request->get('child_sku'), '');
		$id_catalog = trim($request->get('id_catalog'), '');

		if ( $is_code_field_found ) {
			$option = Option::where('store_id', $store_id)
							->where('child_sku', $child_sku)
							->first();
		}

		if ( !$option ) {
			$option = new Option();
			$option->store_id = $store_id;
			$option->unique_row_value = $unique_row_value;
			$option->child_sku = $child_sku;
		}

		$option->parent_sku = $parent_sku;
		$option->graphic_sku = $graphic_sku;
		$option->id_catalog = $id_catalog;
		$option->allow_mixing = intval($request->get('allow_mixing', 1));
		$option->batch_route_id = intval($request->get('batch_route_id', Helper::getDefaultRouteId()));
		$option->parameter_option = json_encode($dataToStore);

		$option->save();

		$return_to = $request->get('return_to', '');
		$return_to = empty( $return_to ) ? url(sprintf("logistics/sku_show?store_id=%s", $store_id)) : urldecode($return_to);

		return redirect()
			->to($return_to)
			->with('success', "Child sku inserted.");
	}

	public function update_sku_converter (Request $request)
	{
		$rules = [
			'store_id'         => 'required',
			'unique_row_value' => 'required',
		];

		$inputs = [
			'store_id'         => $request->get('store_id'),
			'unique_row_value' => $request->get('unique_row_value'),
		];

		$validator = Validator::make($inputs, $rules);

		if ( $validator->fails() ) {
			return redirect()
				->back()
				->withErrors($validator);
		}

		$store_id = $request->get('store_id');
		$unique_row_value = $request->get('unique_row_value');

		$parameters = Parameter::where('store_id', $store_id)
							   ->get();

		if ( $parameters->count() == 0 ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'Not a valid store selected',
				]);
		}

		$dataToStore = [ ];
		foreach ( $parameters as $parameter ) {
			$parameter_value = $parameter->parameter_value;
			$form_field = Helper::textToHTMLFormName($parameter_value);
			$dataToStore[$parameter_value] = $request->get($form_field, '');
		}
		#return $request->all();
		#return $dataToStore;
		$parent_sku = trim($request->get('parent_sku'), '');
		$graphic_sku = trim($request->get('graphic_sku'), '');
		$child_sku = trim($request->get('child_sku'), '');
		$id_catalog = trim($request->get('id_catalog'), '');
		if ( empty( $child_sku ) ) {
			return redirect()
				->back()
				->withInput()
				->withErrors([
					'error' => 'Child SKU is required',
				]);
		}

		// todo: if child sku is changed, change on items.child_sku too

		Option::where('store_id', $store_id)
			  ->where('unique_row_value', $unique_row_value)
			  ->update([
				  'id_catalog'       => $id_catalog,
				  'parent_sku'       => $parent_sku,
				  'child_sku'        => $child_sku,
				  'graphic_sku'      => $graphic_sku,
				  'allow_mixing'     => intval($request->get('allow_mixing', 1)),
				  'batch_route_id'   => intval($request->get('batch_route_id', Helper::getDefaultRouteId())),
				  'parameter_option' => json_encode($dataToStore),
			  ]);

		$return_to = $request->get('return_to', '');
		$return_to = empty( $return_to ) ? url(sprintf("logistics/sku_show?store_id=%s", $store_id)) : urldecode($return_to);

		return redirect()
			->to($return_to)
			->with('success', "Data updated.");
	}

	public function delete_sku (Request $request, $unique_row_value)
	{
		$option = Option::where('unique_row_value', $unique_row_value)
						->first();
		if ( $option ) {
			Option::where('unique_row_value', $unique_row_value)
				  ->delete();
		}

		return redirect()
			->back()
			->with('success', "Row is deleted.");
	}

	public function update_parameter_option (Request $request, $unique_row)
	{
		$parameter_option = Option::where('unique_row_value', $unique_row)
								  ->first();
		if ( !$parameter_option ) {
			return redirect()
				->back()
				->withErrors([
					'error' => "Not a valid row selected",
				]);
		}
		$message = '';
		if ( $request->has('allow_mixing') ) {
			$parameter_option->allow_mixing = intval($request->get('allow_mixing'));
			$parameter_option->save();
			$message = "Allow mixing is successfully changed";
		} elseif ( $request->has('batch_route_id') ) {
			$batch_route_id = intval($request->get('batch_route_id'));
			$batch_route_id = $batch_route_id > 0 ? $batch_route_id : Helper::getDefaultRouteId();
			$parameter_option->batch_route_id = $batch_route_id;
			$parameter_option->save();
			$message = "Batch route is successfully changed";
		}
		$otherwise = url('/logistics/sku_show?store_id=%s', $parameter_option->store_id);
		$return_to = $request->get('return_to', '');
		$return_to = empty( $return_to ) ? $otherwise : urldecode($return_to);

		return redirect()
			->to($return_to)
			->with('success', $message);
	}

	public function crawl (Request $request)
	{
		$id_catalog = $request->get('id_catalog');
		$store_name = $request->get('store_name');

		return view('logistics.crawl')->with('id_catalog', $id_catalog)
									  ->with('store_name', $store_name);
	}

	public function get_file_contents (Request $request)
	{
		return file_get_contents($request->get('url'));
	}

	public function create_child_sku (Request $request)
	{
		$id_catalog = trim($request->get('id_catalog', null));
		$store_id = trim($request->get('store', null));

		$stores = Store::where('is_deleted', 0)
					   ->lists('store_name', 'id');

		$store_name = $stores->toArray();
		$store_name = strtolower($store_name[$store_id]);

		$crawled_data = null;
		if ( $id_catalog ) {
			// generate the url
			$url = url(sprintf("/crawl?id_catalog=%s&store_name=%s", $id_catalog, $store_name));
			//$url = url(sprintf("/crawl?id_catalog=%s", $id_catalog));

			// pass to the phantom class to get the data
			$phantom = new Phantom($url);
			// generate response
			$response = $phantom->request()
								->getResponse();
			// instantiate the dom reader
			$reader = new DOMReader($response);
			//
			$crawled_data = json_decode($reader->readCrawledData(), true);
		}

		return view('logistics.create_child_sku')
			->with('id_catalog', $id_catalog)
			->with('crawled_data', $crawled_data)
			->with('stores', $stores);
	}

	public function post_create_child_sku (Request $request)
	{

		$id_catalog = $request->get('id_catalog');
		$product = Product::where('id_catalog', $id_catalog)
						  ->first();
		if ( !$product ) {
			return redirect()
				->back()
				->with([
					'error' => 'No product found in database with this id catalog.',
				]);
		}
		$available_groups = $request->get('groups', [ ]);
		$checked_group_values = [ ];
		$selected_groups = [ ];
		foreach ( $available_groups as $group ) {
			$selected = $request->get($group, [ ]);
			if ( $selected ) {
				$checked_group_values[] = $selected;
				$selected_groups[] = $group;
			}
		}
		if ( count($checked_group_values) == 0 ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'No group is selected to create a preview.',
				]);
		}
		$suggestions = Helper::generateChildSKUCombination($checked_group_values);

		return view('logistics.preview_child_sku')
			->with('suggestions', $suggestions)
			->with('id_catalog', $id_catalog)
			->with('product', $product)
			->with('selected_groups', $selected_groups)
			->with('store', $request->get('store'))
			->with('checked_group_values', $checked_group_values);
	}

	public function post_preview (Request $request)
	{
		// selected-options[] = if the checkbox is selected, then the child sku will be created
		// and the value will be in this field

		// selected-group[] = the selected groups to create the child sku

		// selected-child-sku[] = the selected child skus from the suggestions, with or without edit

		$selected_groups = $request->get('selected-group');
		$selected_options = $request->get('selected-options');
		$selected_child_sku_suggestions = $request->get('selected-child-sku');
		if ( count($selected_child_sku_suggestions) == 0 ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'No child SKU is selected to be added',
				]);
		}

		$parent_sku = $request->get('parent_sku');
		$id_catalog = $request->get('id_catalog');
		$store_id = $request->get('store');
		$store = Store::where('is_deleted', 0)
					  ->find($store_id);
		if ( !$store ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'Not a valid store is chosen.',
				]);
		}

		if ( empty( $parent_sku ) ) {
			return redirect()
				->back()
				->withErrors([
					'error' => 'No product is available with that id catalog.',
				]);
		}
		// insert the column values that are not in the parameters table
		// insert into that table.

		$available_groups = Parameter::where('store_id', $store->store_id)
									 ->lists('parameter_value')
									 ->toArray();
		$not_available = array_diff($selected_groups, $available_groups);

		foreach ( $not_available as $inserable ) {
			$parameter = new Parameter();
			$parameter->store_id = $store->store_id;
			$parameter->parameter_value = $inserable;
			$parameter->save();
		}

		$batch_route_id = Helper::getDefaultRouteId();
		$rows = [ ];
		$index = 0;
		foreach ( $selected_options as $option ) {
			$options_array = json_decode($option);
			$combined_array = array_combine($selected_groups, $options_array);
			$child_sku = $selected_child_sku_suggestions[$index];

			$rows[] = [
				'store_id'         => $store->store_id,
				'unique_row_value' => Helper::generateUniqueRowId(),
				'id_catalog'       => $id_catalog,
				'graphic_sku'      => 'NeedGraphicFile',
				'parent_sku'       => $parent_sku,
				'allow_mixing'     => 1,
				'batch_route_id'   => $batch_route_id,
				'child_sku'        => $child_sku,
				'parameter_option' => json_encode($combined_array),
			];
			++$index;
		}

		foreach ( $rows as $columns ) {
			$option = Option::where('child_sku', $columns['child_sku'])
							->first();
			if ( !$option ) {
				$option = new Option();
			}
			foreach ( $columns as $column_key => $column_value ) {
				$option->$column_key = $column_value;
			}
			$option->save();
		}

		return redirect()->to(url(sprintf("/logistics/sku_show?store_id=%s&search_for=%s&search_in=parent_sku", $store->store_id, $parent_sku)));
	}
}
