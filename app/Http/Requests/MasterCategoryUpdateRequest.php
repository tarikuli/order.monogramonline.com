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
	 *
	 * @param \App\Http\Requests\Request|\Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	public function rules (\Illuminate\Http\Request $request)
	{
		if ( $request->has('master_category_code') ) {
			return [
				'master_category_code'          => 'required|no_space_allowed',
				'master_category_description'   => 'required',
				'master_category_display_order' => 'required',
			];
		}

		return [
			'modified_code'          => 'required|no_space_allowed',
			'modified_description'   => 'required',
			'modified_display_order' => 'required',
		];
	}
}
