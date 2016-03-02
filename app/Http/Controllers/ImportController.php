<?php

namespace App\Http\Controllers;

use App\Inventory;
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
}
