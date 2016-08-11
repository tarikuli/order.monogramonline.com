<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PurchaseCreateRequest extends Request
{
	/**
	 * Determine if the user is authorized to make this request.
	 * @return bool
	 */
	public function authorize ()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 * @return array
	 */
	public function rules ()
	{
		return [
			'po_number'		=> 'required',
			'po_date'		=> 'required',
			'vendor_id'		=> 'required',
			'purchase_id'	=> 'required',
		];
	}
}
