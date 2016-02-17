<?php

namespace App\Http\Controllers;

use App\Ship;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ShippingController extends Controller
{
	public function index (Request $request)
	{
		$ships = Ship::where('is_deleted', 0)
					 ->paginate(50);

		return view('shipping.index', compact('ships', 'request'));
	}
}
