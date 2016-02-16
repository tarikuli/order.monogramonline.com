<?php

use Illuminate\Database\Seeder;

class SubCategoriesTableSeeder extends Seeder
{
	protected $sub_categories = [
		[
			'sc-a',
			'Sub Category A',
			'1',
		],
		[
			'sc-b',
			'Sub Category B',
			'2',
		],
		[
			'sc-v',
			'Sub Category C',
			'3',
		],
		[
			'sc-d',
			'Sub Category D',
			'4',
		],
		[
			'sc-d',
			'Sub Category E',
			'5',
		],
		[
			'sc-f',
			'Sub Category F',
			'6',
		],
		[
			'sc-g',
			'Sub Category G',
			'7',
		],
		[
			'sc-h',
			'Sub Category H',
			'8',
		],

	];

	public function run ()
	{
		foreach ( $this->sub_categories as $value ) {
			$i = 0;
			$sub_category = new \App\SubCategory();
			$sub_category->sub_category_code = $value[$i++];
			$sub_category->sub_category_description = $value[$i++];
			$sub_category->sub_category_display_order = $value[$i++];
			$sub_category->save();
		}
	}
}
