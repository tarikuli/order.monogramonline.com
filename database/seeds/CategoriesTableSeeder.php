<?php

use Illuminate\Database\Seeder;
use App\Category;
class CategoriesTableSeeder extends Seeder
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
            $category = new Category();
            $category->category_code = $value[$i++];
            $category->category_description = $value[$i++];
            $category->category_display_order = $value[$i++];
            $category->save();
        }
    }
}
