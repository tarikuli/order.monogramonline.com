<?php

namespace App\Http\Controllers;

use App\BatchRoute;
use App\Inventory;
use App\Station;
use App\Template;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use League\Csv\Reader;

class ImportController extends Controller
{
	public function importInventory (Request $request)
	{
		$file = $request->file('attached_csv');

		$mime_types = [
			'application/vnd.ms-excel',
			'text/plain',
			'text/csv',
			'text/tsv',
		];
		if ( !$file || !in_array($file->getClientMimeType(), $mime_types) ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([
					'error' => 'Either file is not given or imported file is not valid.',
				]));
		}
		$file_path = sprintf("%s/assets/imports/inventories/", public_path());
		$file_name = $file->getClientOriginalName();
		$fully_specified_file_name = sprintf("%s%s", $file_path, $file_name);

		$file->move($file_path, $file_name);
		$reader = Reader::createFromPath($fully_specified_file_name);
		$table_columns = Inventory::getTableColumns();
		$csv_columns = $reader->fetchOne();

		if ( count(array_intersect($table_columns, $csv_columns)) != count($table_columns) ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([
					'error' => "Expected columns not found.",
				]));
		}

		if ( $request->get('todo') == 'validate' ) {
			session()->flash('success', 'Seems like a valid CSV file.');

			return redirect()->to('inventories');
		} elseif ( $request->get('todo') == 'upload' ) {
			$rows = $reader->setOffset(1)
						   ->fetchAssoc($csv_columns);
			foreach ( $rows as $row ) {
				$table_field_values = array_intersect_key($row, array_flip($table_columns));
				$inventory = new Inventory();

				foreach ( $table_field_values as $key => $value ) {
					$inventory->$key = trim($value);
				}
				$inventory->save();
			}
			session()->flash('success', 'Inventory is successfully updated.');

			return redirect(url('inventories'));
		}
	}

	public function importBatchRoute (Request $request)
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
		$file_path = sprintf("%s/assets/imports/batch_routes/", public_path());
		$file_name = $file->getClientOriginalName();
		$fully_specified_file_name = sprintf("%s%s", $file_path, $file_name);

		$file->move($file_path, $file_name);
		$reader = Reader::createFromPath($fully_specified_file_name);
		$table_columns = BatchRoute::getTableColumns();
		$extra_columns = [
			'stations',
		];
		$needed_columns = array_merge($table_columns, $extra_columns);
		$csv_columns = array_filter($reader->fetchOne());

		if ( count(array_intersect($needed_columns, $csv_columns)) != count($needed_columns) ) {
			return redirect()
				->back()
				->withErrors(new MessageBag([
					'error' => 'CSV file columns don\'t match. Import stopped.',
				]));
		}

		$rows = $reader->setOffset(1)
					   ->fetchAssoc($needed_columns);

		foreach ( $rows as $row ) {

			$batch_code = trim($row['batch_code']);

			if ( empty( $batch_code ) ) {
				continue;
			}
			$batch_route = BatchRoute::where('batch_code', $batch_code)
									 ->first();
			if ( !$batch_route ) {
				$batch_route = new BatchRoute();
				$batch_route->batch_code = $batch_code;
			}
			foreach ( $table_columns as $column ) {
				if ( $column == 'batch_code' ) {
					continue;
				} elseif ( $column == 'batch_route_name' ) {
					$batch_route->batch_route_name = trim($row['batch_route_name']);
				} elseif ( $column == 'batch_max_units' ) {
					$batch_route->batch_max_units = intval(trim($row['batch_max_units']));
				} elseif ( $column == 'export_template' ) {
					$template_name = trim($row['export_template']);
					$template = Template::where('template_name', $template_name)
										->first();
					if ( $template ) {
						$batch_route->export_template = $template->id;
					} else {
						$batch_route->export_template = 1; // assign default template
					}
				} elseif ( $column == 'batch_options' ) {
					$batch_options = trim($row['batch_options']);
					$batch_route->batch_options = $batch_options;
				}
			}
			$batch_route->save();
			foreach ( $extra_columns as $column ) {
				if ( $column == 'stations' ) {
					$stationsFromFile = trim($row['stations']);
					$stationsArray = explode(",", str_replace(" ", "", $stationsFromFile));
					$stationsFromTable = Station::whereIn("station_name", $stationsArray)
												->lists('id')
												->toArray();
					$batch_route->stations()
								->sync($stationsFromTable);
				}
			}
		}
		session()->flash('success', 'Batch routes are successfully updated.');

		return redirect(url('batch_routes'));
	}
}
