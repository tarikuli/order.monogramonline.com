<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PurchasedInvProductsCreateRequest extends Request
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
			"code" => 'required',
			"name" => 'required',
			"unit" => 'required',
			"price" => 'required|numeric',
		];
	}
}
