<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class VendorCreateRequest extends Request
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
			"vendor_name"  => 'required',
			"email"        => 'required|unique:vendors,email',
			"phone_number" => 'required',
		];
	}
}
