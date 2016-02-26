<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class CustomValidationProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 * @return void
	 */
	public function boot ()
	{
		Validator::extend('no_space_allowed', function ($attribute, $value, $parameters, $validator) {
			return count(explode(' ', $value)) == 1;
		});
		Validator::replacer('no_space_allowed', function ($message, $attribute, $rule, $parameters) {
			return $message;
		});
	}

	/**
	 * Register the application services.
	 * @return void
	 */
	public function register ()
	{
		//
	}
}
