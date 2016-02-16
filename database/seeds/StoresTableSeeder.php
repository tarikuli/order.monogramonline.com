<?php

use Illuminate\Database\Seeder;

class StoresTableSeeder extends Seeder
{
    private $stores = [
        [
            'amz-1080747',
            'Amazon/Monogram manufacture Online',
        ],[
            'ebay-personalizedjewelrycenter',
            'eBay/personalizedjewelrycenter',
        ],
        [
            "yhst-128796189915726",
            "MonogramOnline.com"
        ],
        [
            "yhst-132060549835833",
            "ShopOnlineDeals.com"
        ],
        [
            "wh-265",
            "WH/Monogramonline"
        ],
        [
            "FB-265",
            "Facebook"
        ],
        [
            "monogrammfg",
            "Etsy/Monog"
        ],
        [
            "micalidesign",
            "Etsy/MICALIDesign"
        ],
        [
            "originalpd",
            "Etsy/OriginalPd"
        ],
        [
            "WYNnecklace",
            "Etsy/WYNnecklace"
        ],
    ];

    public function run()
    {
        foreach($this->stores as $store){
            $s = new \App\Store();
            $s->store_id = $store[0];
            $s->store_name = $store[1];
            $s->save();
        }
    }
}
