<?php

use Illuminate\Database\Seeder;
use App\Category;
class MasterCategoriesTableSeeder extends Seeder
{
    protected $categories = [
        [
            'Monogram',
            'Monogram Necklaces',
            '1',
        ],
        [
            'MonogramB',
            'Monogram Bracelets',
            '2',
        ],
        [
            'MonogramE',
            'Monogram Earrings',
            '3',
        ],
        [
            'Bracelets',
            'Bracelets',
            '20',
        ],
        [
            'Earrings',
            'Earrings',
            '30',
        ],
        [
            'Rings',
            'Rings',
            '40',
        ],
        [
            'Necklaces',
            'Necklaces',
            '50',
        ],
        [
            'RED',
            'RED LASER',
            '51',
        ],
        [
            'SUB',
            'SUBLIMATION',
            '52',
        ],
        [
            'GIFT BASKE',
            'GIFT BASKETS',
            '53',
        ],

    ];
    public function run()
    {
        foreach($this->categories as $value){
            $i = 0;
            $category = new \App\MasterCategory();
            $category->master_category_code = $value[$i++];
            $category->master_category_description = $value[$i++];
            $category->master_category_display_order = $value[$i++];
            $category->save();
        }
    }
}
