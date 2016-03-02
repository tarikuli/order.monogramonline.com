<?php

namespace App\Http\Controllers;

use App\Inventory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use League\Csv\Writer;

class ExportController extends Controller
{
	public function inventory (Request $request)
	{
		$tableColumns = Inventory::getTableColumns();
		$file_path = sprintf("%s/assets/exports/inventories/", public_path());
		$file_name = sprintf("inventory-%s-%s.csv", date("y-m-d-h-i-s", strtotime('now')), str_random(5));
		$fully_specified_path = sprintf("%s%s", $file_path, $file_name);

		$csv = Writer::createFromFileObject(new \SplFileObject($fully_specified_path, 'a+'), 'w');
		$csv->insertOne($tableColumns);

		$inventories = Inventory::where('is_deleted', 0)
								->get($tableColumns)
								->toArray();
		$csv->insertAll($inventories);

		return response()->download($fully_specified_path);
	}
}
