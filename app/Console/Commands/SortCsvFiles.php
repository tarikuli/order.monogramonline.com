<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Hamcrest\Arrays\IsArray;
use App\Setting;

class SortCsvFiles extends Command
{
	// https://www.youtube.com/watch?v=mp-XZm7INl8
	private $save_to_path = '';

	private $csvSearchArray = [];

	private $source_csv_dir = "";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sort:csvfiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Script will sort csv files according csv file name to destination Directory.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // this line is mandatory settings supervisor_station

    	// CSV search string
//     	$csvSearchArray['pc1'] = 'C:\xampp\htdocs\source_csv_dir\pc1';
//     	$csvSearchArray['pc2'] = 'C:\xampp\htdocs\source_csv_dir\pc2';
//     	$csvSearchArray['pc1'] = 'C:\xampp\htdocs\source_csv_dir\pc3';

		// get the public path where to store the image
		$this->save_to_path = public_path();
		// Set Source file name
// 		$source_csv_dir = 'C:\xampp\htdocs\source_csv_dir';
// 		$settings = Setting::where('supervisor_station', 'csvSearch')->get();
		$settings = Setting::all();
		$settings = $settings->toArray();

		foreach ($settings as  $fileNameIndex => $fileName){
// 			$this->logger("info", $fileName['supervisor_station']);

			switch ($fileName['supervisor_station']) {
				case "csvSearch":
					$this->csvSearchArray[$fileName['default_shipping_rule']] = $fileName['default_route_id'];
					break;
				case "source_csv_dir":
					$this->source_csv_dir = $fileName['default_route_id'];
					break;
			}

		}

		// Get file name from directory
		$fileNames = $this->getFileName($this->source_csv_dir);

		foreach ($fileNames as  $fileNameIndex => $fileName){
			foreach ($this->csvSearchArray as $csvSrckey => $csvSrcVal){
				if (strpos($fileName, $csvSrckey) !== false) {
					$this->logger("info", $fileName);
					if(file_exists ($this->source_csv_dir.'\\'.$fileName)){
						#copy($source_csv_dir.'\\'.$fileName, $csvSrcVal.'\\'.$fileName);
						rename($this->source_csv_dir.'\\'.$fileName, $csvSrcVal.'\\'.$fileName);
					}
				}
			}
		}

    }

    private function logger ($type,$message)
    {
    	if(is_array($message)){
    		$messages = "";
    		foreach ($message as $key => $val){
    			$messages .=$key ." - ". $val."\n";
    		}
    		$message = $messages;
    	}
    	if ( $type == 'info' ) {
    		$this->info($message);
    	} elseif ( $type == 'error' ) {
    		$this->error($message);
    	} elseif ( $type == 'warning' ) {
    		$this->warn($message);
    	}
    	\Log::info($message);
    }

    private function getFileName($dir){
    	$file_list = [];
		if (is_dir($dir)){
			$file_lists = array_values(array_diff(scandir($dir), array('..', '.')));
			foreach ($file_lists as $val){
				if (!is_dir($dir."\\".$val)){
					$file_list[] = $val;
				}
			}
		}
		return $file_list;
    }
}
