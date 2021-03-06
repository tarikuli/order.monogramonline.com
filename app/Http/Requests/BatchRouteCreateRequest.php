<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class BatchRouteCreateRequest extends Request
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
			"batch_max_units"  => 'required',
			"batch_route_name" => 'required',
			"batch_code"       => 'required|unique:batch_routes,batch_code',
		];
	}
}
