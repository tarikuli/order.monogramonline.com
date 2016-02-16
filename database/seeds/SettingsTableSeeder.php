<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 * @return void
	 */
	public function run ()
	{
		$setting = new \App\Setting();
		$setting->supervisor_station = 'S-SUP';
		$setting->default_shipping_rule = 38;
		$setting->default_route_id = 115;
		$setting->save();
	}
}
