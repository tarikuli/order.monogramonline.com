<?php

namespace App\Http\Controllers;

use App\Vendor;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class VendorController extends Controller
{
	public function index ()
	{
		$count = 1;
		$vendors = Vendor::where('is_deleted', 0)
						 ->latest()
						 ->paginate(50);

		return view('vendors.index', compact('vendors', 'count'));
	}

	public function create ()
	{
		return view('vendors.create');
	}

	public function store (Requests\VendorCreateRequest $request)
	{
		$vendor = new Vendor();
		$vendor->vendor_name = trim($request->get('vendor_name'));
		$vendor->email = trim($request->get('email'));
		$vendor->phone_number = trim($request->get('phone_number'));

		if ( $request->has('zip_code') ) {
			$vendor->zip_code = trim($request->get('zip_code'));
		}
		if ( $request->has('state') ) {
			$vendor->state = trim($request->get('state'));
		}
		if ( $request->has('country') ) {
			$vendor->country = trim($request->get('country'));
		}

		$vendor->save();

		session()->flash('success', 'Vendor created successfully.');

		return redirect()->route('vendors.index');
	}

	public function show ($id)
	{
		$vendor = Vendor::find($id);
		if ( !$vendor ) {
			return view('errors.404');
		}

		return view('vendors.show', compact('vendor'));
	}

	public function edit ($id)
	{
		$vendor = Vendor::find($id);
		if ( !$vendor ) {
			return view('errors.404');
		}

		return view('vendors.edit', compact('vendor'));
	}

	public function update (Requests\VendorUpdateRequest $request, $id)
	{
		$vendor = Vendor::find($id);
		if ( !$vendor ) {
			return view('errors.404');
		}

		$vendor->vendor_name = trim($request->get('vendor_name'));

		$vendor->phone_number = trim($request->get('phone_number'));

		if ( $request->has('email') ) {
			$vendor->email = trim($request->get('email'));
		}
		if ( $request->has('zip_code') ) {
			$vendor->zip_code = trim($request->get('zip_code'));
		}
		if ( $request->has('state') ) {
			$vendor->state = trim($request->get('state'));
		}
		if ( $request->has('country') ) {
			$vendor->country = trim($request->get('country'));
		}

		$vendor->save();

		session()->flash('success', 'Vendor udpated successfully updated.');

		return redirect()->route('vendors.index');
	}

	public function destroy ($id)
	{
		$vendor = Vendor::find($id);
		if ( !$vendor ) {
			return view('errors.404');
		}

		$vendor->is_deleted = 1;
		$vendor->save();

		session()->flash('success', 'Vendor successfully deleted.');

		return redirect()->route('vendors.index');

	}
}
