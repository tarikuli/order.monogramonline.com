<?php

use Illuminate\Database\Seeder;
use App\Category;

class MasterCategoriesTableSeeder extends Seeder
{
	protected $categories = [
		"red",
		"jewlery",
		"Sub",
		"Assessories",
		"DropShip",
		"eng",
		"Acrylic",
		"inventory",
		"EMB",
		"mis",
		"Hat",
		"jewelry",
		"Solid Gold",
		"Gift Cert",
		"GIFT BASKE",
		"Inventroy",
		"Monogram",
		"Tatto Banz",
		"6",
		"watches",
		"Soild Gold",
		"Necklaces",
	];

	public function run ()
	{
		$c = 1;
		foreach ( $this->categories as $value ) {
			$category = new \App\MasterCategory();
			$category->master_category_code = $value;
			$category->master_category_description = $value;
			$category->master_category_display_order = $c;
			$category->save();
			$c++;
		}
	}
}
