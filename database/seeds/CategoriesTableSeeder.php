<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategoriesTableSeeder extends Seeder
{
	protected $categories = [
		"RED",
		"jewlery",
		"SUB",
		"Assessorie",
		"DropShip",
		"emb",
		"eng",
		"inventory",
		"0",
		"Acrylic",
		"mis",
		"hat",
		"GIFT BASKE",
		"Solid Gold",
		"Gift Cert",
		"Tatto Banz",
		"watches",
		"Monogram",

	];

	public function run ()
	{
		$c = 1;
		foreach ( $this->categories as $value ) {
			$category = new Category();
			$category->category_code = $value;
			$category->category_description = $value;
			$category->category_display_order = $c++;
			$category->save();
		}
	}
}
