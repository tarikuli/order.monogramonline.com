<?php

use Illuminate\Database\Seeder;

class SubCategoriesTableSeeder extends Seeder
{
	protected $sub_categories = [
		"0",
		"DropShip",
		"sc-a",
	];

	public function run ()
	{
		$i = 0;
		foreach ( $this->sub_categories as $value ) {
			$sub_category = new \App\SubCategory();
			$sub_category->sub_category_code = $value;
			$sub_category->sub_category_description = $value;
			$sub_category->sub_category_display_order = $i++;
			$sub_category->save();
		}
	}
}
