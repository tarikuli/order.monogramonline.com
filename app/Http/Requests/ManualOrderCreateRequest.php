<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ManualOrderCreateRequest extends Request
{
	public function authorize ()
	{
		return true;
	}

	public function rules ()
	{
		return [
			'item_id_catalog' => 'required',
			'ship_first_name' => 'required',
			'bill_first_name' => 'required',
			'bill_email' => 'required|email',
			'shipping' => 'required',
		];
	}

	public function messages ()
	{
		return [
			'item_id_catalog.required' => 'Items must be selected',
		];
	}
}
