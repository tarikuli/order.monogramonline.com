<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class OrderCreateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
    	return [
    			"order_status"      => "required",
    			"customer_id"       => "required",
    			"ship_first_name"   => "required",
    			"ship_address_1"    => "required",
    			"ship_city"         => "required",
    			"ship_state"        => "required",
    			"ship_zip"          => "required",
    			"ship_phone"        => "required",
    			"bill_email"        => "required|email",
    			"shipping"          => "required",
    	];
    }
}
