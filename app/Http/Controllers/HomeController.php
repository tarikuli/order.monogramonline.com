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
}
