<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ProductAddRequest extends Request
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
			"store_id"           => "required",
			"id_catalog"         => "required",
			"product_model"      => "required",
			"product_name"       => "required",
			"ship_weight"        => "required",
			"product_price"      => "required",
			"product_sale_price" => "required",
			"is_taxable"         => "required",
		];
	}
}
