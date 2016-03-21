<?php

namespace App\Http\Controllers;

use App\Product;
use App\Station;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class HomeController extends Controller
{

	public function index ()
	{
		$stations = Station::where('is_deleted', 0)
						   ->get();

		return view('home.index', compact('stations'));
	}
}
