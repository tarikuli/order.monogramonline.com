<?php

namespace App;

use App\Http\Controllers\ShippingController;
use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
	protected $table = 'shipping';

	private function tableColumns ()
	{
		$columns = $this->getConnection()
						->getSchemaBuilder()
						->getColumnListing($this->getTable());

		return array_slice($columns, 1, -2);
	}

	public static function getTableColumns ()
	{
		return (new static())->tableColumns();
	}

	public function scopeSearchCriteria ($query, $search_for, $search_in)
	{
		$search_for = trim($search_for);
		if ( in_array($search_in, array_keys(ShippingController::$search_in)) ) {
			/*
			 * camel case method converts the key to camel case
			 * uc first converts the word to upper case first to match the method name
			 */
			$search_function_to_respond = sprintf("scopeSearch%s", ucfirst(camel_case($search_in)));

			return $this->$search_function_to_respond($query, $search_for);
		}

		return;
	}

	public function scopeSearchAddressOne ($query, $address_1)
	{
		if ( empty( $address_1 ) ) {
			return;
		}

		return $query->where('address1', "LIKE", sprintf("%%%s%%", $address_1));
	}

	public function scopeSearchAddressTwo ($query, $address_2)
	{
		if ( empty( $address_2 ) ) {
			return;
		}

		return $query->where('address2', "LIKE", sprintf("%%%s%%", $address_2));
	}

	public function scopeSearchName ($query, $name)
	{
		if ( empty( $name ) ) {
			return;
		}

		return $query->where('name', "LIKE", sprintf("%%%s%%", $name));
	}

	public function scopeSearchOrderNumber ($query, $order_number)
	{
		if ( empty( $order_number ) ) {
			return;
		}

		return $query->where('order_number', "LIKE", sprintf("%%%s%%", $order_number));
	}

	public function scopeSearchPackageShape ($query, $package_shape)
	{
		if ( empty( $package_shape ) ) {
			return;
		}

		return $query->where('package_shape', "LIKE", sprintf("%%%s%%", $package_shape));
	}

	public function scopeSearchCompany ($query, $company)
	{
		if ( empty( $company ) ) {
			return;
		}

		return $query->where('company', "LIKE", sprintf("%%%s%%", $company));
	}

	public function scopeSearchCity ($query, $city)
	{
		if ( empty( $city ) ) {
			return;
		}

		return $query->where('city', "LIKE", sprintf("%%%s%%", $city));
	}

	public function scopeSearchState ($query, $state)
	{
		if ( empty( $state ) ) {
			return;
		}

		return $query->where('state_city', "LIKE", sprintf("%%%s%%", $state));
	}

	public function scopeSearchPostalCode ($query, $postal_code)
	{
		if ( empty( $postal_code ) ) {
			return;
		}

		return $query->where('postal_code', "LIKE", sprintf("%%%s%%", $postal_code));
	}

	public function scopeSearchCountry ($query, $country)
	{
		if ( empty( $country ) ) {
			return;
		}

		return $query->where('country', "LIKE", sprintf("%%%s%%", $country));
	}

	public function scopeSearchEmail ($query, $email)
	{
		if ( empty( $email ) ) {
			return;
		}

		return $query->where('email', "LIKE", sprintf("%%%s%%", $email));
	}

	public function scopeSearchPhone ($query, $phone)
	{
		if ( empty( $phone ) ) {
			return;
		}

		return $query->where('phone', "LIKE", sprintf("%%%s%%", $phone));
	}

	public function scopeSearchTransactionId ($query, $transaction_id)
	{
		if ( empty( $transaction_id ) ) {
			return;
		}

		return $query->where('transaction_id', intval($transaction_id));
	}

	public function scopeSearchTrackingNumber ($query, $tracking_number)
	{
		if ( empty( $tracking_number ) ) {
			return;
		}

		return $query->where('tracking_number', "LIKE", sprintf("%%%s%%", $tracking_number));
	}

	public function scopeSearchMailClass ($query, $mail_class)
	{
		if ( empty( $mail_class ) ) {
			return;
		}

		return $query->where('mail_class', "LIKE", sprintf("%%%s%%", $mail_class));
	}

	public function scopeSearchWithinDate ($query, $start_date, $end_date)
	{
		if ( !$start_date ) {
			return;
		}
		// formatting the date again, if, malformed, won't crash
		$start_date = date('Y-m-d', strtotime($start_date));
		if ( $end_date ) {
			$end_date = date('Y-m-d', strtotime($end_date));
		} else {
			$end_date = $start_date;
		}
		$starting = $start_date;
		$ending = $end_date;

		return $query->where('postmark_date', '>=', $starting)
					 ->where('postmark_date', '<=', $ending);
	}
}
