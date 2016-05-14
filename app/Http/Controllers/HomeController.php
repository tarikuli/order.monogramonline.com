<?php

namespace App\Http\Controllers;

use App\Item;
use App\Product;
use App\Station;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Monogram\Helper;

class HomeController extends Controller
{
	private $outcome = [ ];

	public function index ()
	{
		$stations = Station::where('is_deleted', 0)
						   ->get();

		return view('home.index', compact('stations'));
	}

	public function bulk_item_update (Request $request)
	{
		set_time_limit(0);
		foreach ( range(0, 1000) as $count ) {
			$items = Item::where('is_deleted', '0')
						 ->take(1000)
						 ->skip($count * 1000)
						 ->get();
			if ( $items->count() == 0 ) {
				return sprintf("<b>Finish at: %s</b>", date('Y-m-d h:m:s'));
				break;
			}
			echo sprintf("<i>Started %05d at: %s<br/></i>", $items->first()->id, date('Y-m-d h:m:s'));
			foreach ( $items as $item ) {
				$child_sku = Helper::getChildSku($item);
				$item->child_sku = $child_sku;
				$item->save();
			}
		}

	}

	public function update_single_item (Request $request)
	{
		$id = $request->get('id', 1360);

		return Helper::getChildSku(Item::find($id));
	}

	public function combination ()
	{
		$a = [
			'a',
			'b',
		];
		$b = [
			'c',
			'd',
		];
		$c = [
			'e',
			'f',
			'g',
		];
		$arrays = [
			$a,
			$b,
			$c,
		];

		$combinations = $this->generate_combinations($arrays);

		return array_map(function ($current) {
			return implode("-", $current);
		}, $combinations);
		#return $this->outcome;
	}

	private function combine ($arrays, $prefix = '')
	{
		$glue = '';
		if ( strlen($prefix) ) {
			$glue = "-";
		}
		if ( count($arrays) == 0 ) {
			return $prefix;
		}
		$final = array_reduce($arrays[0], function ($result, $current) use ($prefix, $glue, $arrays) {
			$result = $this->combine(array_slice($arrays, 1), sprintf("%s%s%s", $prefix, $glue, $current));
			var_dump($current, $result);
			$this->outcome[] = $result;

			return $result;
		}, '');
		var_dump("--FINAL--");

		return $final;
	}

	private function generate_combinations (array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
	{
		$keys = array_keys($data);
		if ( isset( $value ) === true ) {
			array_push($group, $value);
		}

		if ( $i >= count($data) ) {
			array_push($all, $group);
		} else {
			$currentKey = $keys[$i];
			$currentElement = $data[$currentKey];
			foreach ( $currentElement as $val ) {
				$this->generate_combinations($data, $all, $group, $val, $i + 1);
			}
		}

		return $all;
	}
}
