<?php

use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    private $departments = [
        [
            'House Department',
            'H-DEPT',
            [
                'H-ASB',
                'H-BO',
                'H-CHN',
                'H-EMB',
                'H-ENG',
                'H-ERC',
                'H-FMB',
                'H-IPK',
                'H-LSR',
                'H-MCH',
                'H-PLT',
                'H-POL',
                'H-QCD',
                'H-SHP',
            ]
        ],
        [
            'Jewelery Department',
            'J-DEPT',
            [
                'J-ASB',
                'J-BO',
                'J-CHN',
                'J-ENG',
                'J-EOD',
                'J-ETC',
                'J-GGR',
                'J-GRD',
                'J-GSB',
                'J-JWR',
                'J-LSR',
                'J-MCH',
                'J-MGA',
                'J-PLT',
                'J-POL',
                'J-QCD',
                'J-REC',
                'J-RFB',
                'J-SHP',
            ]
        ],
        [
            'QC Department',
            'QC-DEPT',
            [
                'QC',
            ]
        ],
        [
            'Red Laser Department',
            'R-DEPT',
            [
                'R-BM1',
                'R-BM2',
                'R-BM3',
                'R-BM4',
                'R-BM5',
                'R-BO',
                'R-EMB',
                'R-GGR',
                'R-GLP',
                'R-GRD',
                'R-MGR',
                'R-NIP',
                'R-PEDD',
                'R-PIGN',
                'R-PJOS',
                'R-PNAF',
                'R-QCD',
                'R-REC',
                'R-Red',
                'R-SHP',
                'RedLaser',
            ]
        ],
        [
            'Sublimation Department',
            'S-DEPT',
            [
                'S-BO',
                'S-GGR',
                'S-GRD',
                'S-GRP',
                'S-GRPH',
                'S-QCD',
                'S-REC',
                'S-SHP',
                'S-SUB',
                'Sublimatio',
            ]
        ]
    ];

    public function run()
    {
		foreach ( $this->departments as $value ) {
			$department = new \App\Department();
			$department->department_code = $value[1];
			$department->department_name = $value[0];
			$department->save();

			$original_stations = $value[2];

			$stations_to_add = \App\Station::whereIn('station_name', $original_stations)
										   ->orderByRaw(sprintf("FIELD (station_name, '%s')", implode("', '", $original_stations)))
										   ->lists('id')
										   ->toArray();

			$department->stations()
						->attach($stations_to_add);
		}
    }
}
