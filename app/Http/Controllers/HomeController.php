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

	public function test (Request $request)
	{
		$items = Item::all();
		return $items;
		foreach ( $items as $item ) {
			$child_sku = Helper::getChildSku($item);
			return $child_sku;
		}
	}
}
