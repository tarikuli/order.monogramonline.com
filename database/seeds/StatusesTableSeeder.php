<?php

use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    private $statuses = [
        [
            "0",
            "Credit Hold",
        ],
        [
            "20",
            "In-Process",
        ],
        [
            "25",
            "On-hold",
        ],
        [
            "30",
            "TO BE PROCESSED",
        ],
        [
            "36",
            "Drop Ship",
        ],
        [
            "40",
            "Shipped",
        ],
        [
            "45",
            "Returned",
        ],
        [
            "50",
            "Cancelled",
        ],
        [
            "114",
            "WAITING FOR ANOTHER PC",
        ],
        [
            "111",
            "CUSTOMER SERVICE ISSUE",
        ],
        [
            "116",
            "READY TO SHIP",
        ],
        [
            "128",
            "WAITING FOR INVENTORY -B/O",
        ],
        [
            "100",
            "Gave To Gina",
        ],
        [
            "103",
            "Gave To Patricio",
        ],
        [
            "112",
            "Gave to Rosmaire",
        ],
        [
            "113",
            "Gave to Emma",
        ],
        [
            "105",
            "Gave To Bob",
        ],
        [
            "106",
            "Gave To Givona",
        ],
        [
            "154",
            "Gave TO Symphani",
        ],
        [
            "117",
            "REJECT-GAVE TO GINA",
        ],
        [
            "118",
            "REJECT-GAVE TO LEE",
        ],
        [
            "119",
            "REJECT-GAVE TO RON",
        ],
        [
            "120",
            "REJECT-GAVE TO MATT",
        ],
        [
            "121",
            "REJECT-GAVE TO GIVONA",
        ],
        [
            "126",
            "ORDER REJECTED",
        ],
        [
            "134",
            "TEMP",
        ],
        [
            "135",
            "AT QC",
        ],
        [
            "138",
            "Multi Line",
        ],
        [
            "141",
            "GAVE TO JUAN",
        ],
        [
            "143",
            "Gave to Jewelry Work",
        ],
        [
            "166",
            "Gave Ryan",
        ],
        [
            "142",
            "Reconcile",
        ],
        [
            "158",
            "RECEIVED FROM BOB",
        ],
        [
            "148",
            "BIN- 2 BOB Extra Picking Ticket",
        ],
        [
            "146",
            "BIN- 4 Repairs / Broken Chain",
        ],
        [
            "153",
            "BIN- 5 Broken / Redo",
        ],
        [
            "150",
            "BIN- 6 Repair For Juan",
        ],
        [
            "149",
            "BIN- 7 Redo For Juan",
        ],
        [
            "156",
            "BIN- 10 Missing Partial Pcs",
        ],
        [
            "155",
            "BIN- 12 FACTORY extra paper",
        ],
        [
            "151",
            "BIN- 14 for Laser Booth",
        ],
        [
            "163",
            "FACTORY POLISH",
        ],
        [
            "164",
            "FACTORY - DANNY",
        ],
        [
            "165",
            "JOSE-ENGRAVING",
        ],
        [
            "167",
            "TIME PLATING",
        ],
        [
            "168",
            "ADDRESS UPDATE",
        ],
        [
            "169",
            "FACTORY(CIRCLE OF LIFE)",
        ],
        [
            "170",
            "RYAN(RED LASER)",
        ],
        [
            "171",
            "JOSE LASER(LASER MACHINE)",
        ],
        [
            "172",
            "SUBLIMATION -SUZETTE",
        ],
        [
            "173",
            "RED LASER 1",
        ],
        [
            "174",
            "SUBLIMATION -1",
        ],
        [
            "175",
            "WORK-FLOW SYSTEM",
        ],
        [
            "176",
            "Waiting for QC",
        ],
        [
            "177",
            "EMBROIDERY",
        ],
        [
            "178",
            "Waiting to be shipped",
        ],
        [
            "179",
            "Frances",
        ],
        [
            "180",
            "ENGRAVING",
        ],
        [
            "181",
            "JEWELRY",
        ],
        [
            "182",
            "EMBROIDERY",
        ],
        [
            "183",
            "RED LASER",
        ],
        [
            "184",
            "SUBLIMATION",
        ],
        [
            "185",
            "WAITING FOR ANOTHER 2",
        ],
    ];

    public function run ()
    {
        foreach ( $this->statuses as $status ) {
            $s = new \App\Status();
            $s->status_code = $status[0];
            $s->status_name = $status[1];
            $s->save();
        }
    }
}
