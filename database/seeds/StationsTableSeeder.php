<?php

use Illuminate\Database\Seeder;
use App\Station;

class StationsTableSeeder extends Seeder
{
	protected $stations = [
		[
			'﻿H-ASB',
			'House-Assembly',
		],
		[
			'H-BO',
			'House-Back Order',
		],
		[
			'H-CHN',
			'House-Chain',
		],
		[
			'H-EMB',
			'House-AT EMBRODERY',
		],
		[
			'H-ENG',
			'House-Engraving',
		],
		[
			'H-ERC',
			'House-Received EMBRODERY',
		],
		[
			'H-FMB',
			'House-For EMBRODERY',
		],
		[
			'H-IPK',
			'House-Inventory Picking ** EMMA',
		],
		[
			'H-LSR',
			'House-Laser Engraving',
		],
		[
			'H-MCH',
			'House-Maching',
		],
		[
			'H-PLT',
			'House-Platting',
		],
		[
			'H-POL',
			'House-Polish',
		],
		[
			'H-QCD',
			'House-QC Department',
		],
		[
			'H-SHP',
			'House-Shipping',
		],
		[
			'J-ASB',
			'J-Assembly',
		],
		[
			'J-BO',
			'J-Back Order',
		],
		[
			'J-CHN',
			'J-Chain',
		],
		[
			'J-ENG',
			'J-Engraving Department',
		],
		[
			'J-EOD',
			'J-End of Day',
		],
		[
			'J-ETC',
			'J-Etching',
		],
		[
			'J-GGR',
			'J-Give to Graphics',
		],
		[
			'J-GRD',
			'J-Graphics Done ** SYMPHANI',
		],
		[
			'J-GSB',
			'J-Graphics at BOB',
		],
		[
			'J-JWR',
			'J-Jeweler',
		],
		[
			'J-LSR',
			'J-Fiber Laser Dept',
		],
		[
			'J-MCH',
			'J-Matching',
		],
		[
			'J-MGA',
			'J-Manual Graphics',
		],
		[
			'J-PLT',
			'J-Platting',
		],
		[
			'J-POL',
			'J-Polish',
		],
		[
			'J-QCD',
			'J-QC Department',
		],
		[
			'J-REC',
			'J-Reconciliation',
		],
		[
			'J-RFB',
			'J-Received From BOB',
		],
		[
			'J-SHP',
			'J-Shipping',
		],
		[
			'QC',
			'QC Dept',
		],
		[
			'R-BM1',
			'Red-Machine 1 (Red)',
		],
		[
			'R-BM2',
			'Red-Machine 2 (Red)',
		],
		[
			'R-BM3',
			'Red-Machine 3 (Blue)',
		],
		[
			'R-BM4',
			'Red-Machine 4 (Blue)',
		],
		[
			'R-BM5',
			'Red-Machine 5 (Blue)',
		],
		[
			'R-BO',
			'Red-Back Order',
		],
		[
			'R-EMB',
			'Red-EMBRODERY',
		],
		[
			'R-GGR',
			'Red-Give to Graphics',
		],
		[
			'R-GLP',
			'Red-Graphics Laser Processing',
		],
		[
			'R-GRD',
			'Red-Graphics DONE ** ROSE',
		],
		[
			'R-MGR',
			'Red-Manual Graphics DONE',
		],
		[
			'R-NIP',
			'Red-No Import',
		],
		[
			'R-PEDD',
			'Red- Process Eddie',
		],
		[
			'R-PIGN',
			'Red-Process Ignacio',
		],
		[
			'R-PJOS',
			'Red-Process Jose',
		],
		[
			'R-PNAF',
			'Red-Process Nafiz',
		],
		[
			'R-QCD',
			'Red-QC Department',
		],
		[
			'R-REC',
			'Red-Reconciliation',
		],
		[
			'R-Red',
			'Red-Red Laser Department',
		],
		[
			'R-SHP',
			'Red-Shiping',
		],
		[
			'RedLaser',
			'Red Laser Dept',
		],
		[
			'S-BO',
			'Sub-Back Order',
		],
		[
			'S-GGR',
			'Sub-Give to Graphics',
		],
		[
			'S-GRD',
			'Sub-Graphics DONE ** SYMPHANI',
		],
		[
			'S-GRP',
			'Sub-Graphic to Print',
		],
		[
			'S-GRPH',
			'Sub-Graphic to Print HOUSE',
		],
		[
			'S-QCD',
			'Sub-QC Department',
		],
		[
			'S-REC',
			'Sub-Reconciliation',
		],
		[
			'S-SHP',
			'Sub-Shipping',
		],
		[
			'S-SUB',
			'Sub-Sublimation Dept',
		],
		[
			'Sublimatio',
			'Sublimation Dept',
		],
		[
			'Sublimatio',
			'Sublimation Dept',
		],
		[
			'S-SUP',
			'Supervisor',
		],

	];

	public function run ()
	{
		foreach ( $this->stations as $value ) {
			$i = 0;
			$station = new Station();
			$station->station_name = $value[$i++];
			$station->station_description = $value[$i++];
			$station->save();
		}
	}
}
