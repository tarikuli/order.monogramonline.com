<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class MasterCategoryUpdateRequest extends Request
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
			'master_category_code'          => 'required',
			'master_category_description'   => 'required',
			'master_category_display_order' => 'required',
		];
	}
}
