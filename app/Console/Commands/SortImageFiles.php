<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Setting;

class SortImageFiles extends Command
{
	// https://www.youtube.com/watch?v=mp-XZm7INl8
	private $save_to_path = '';

	private $imageSearchArray = [];

	private $destination_csv_dir = "";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sort:imagefiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Script will sort image files according image file name to destination hot Directory.';

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
//     	$imageSearchArray['pc1'] = 'C:\xampp\htdocs\destination_csv_dir\pc1';
//     	$imageSearchArray['pc2'] = 'C:\xampp\htdocs\destination_csv_dir\pc2';
//     	$imageSearchArray['pc1'] = 'C:\xampp\htdocs\destination_csv_dir\pc3';

		// get the public path where to store the image
		$this->save_to_path = public_path();
		// Set Source file name
// 		$destination_csv_dir = 'C:\xampp\htdocs\destination_csv_dir';
// 		$settings = Setting::where('supervisor_station', 'csvSearch')->get();
		$settings = Setting::all();
		$settings = $settings->toArray();

		foreach ($settings as  $fileNameIndex => $fileName){
// 			$this->logger("info", $fileName['supervisor_station']);

			switch ($fileName['supervisor_station']) {
				case "imageSearch":
					$this->imageSearchArray[$fileName['default_shipping_rule']] = $fileName['default_route_id'];
					break;
				case "destination_image_dir":
					$this->destination_csv_dir = $fileName['default_route_id'];
					break;
			}

		}

		// Get Unique search key from imageSearchArray
		$imageUniqueSearchKeys = array_unique(array_keys($this->imageSearchArray ));

		// Get file name from directory

		foreach ($this->imageSearchArray as  $imageDirPath){

// 			$this->logger("info", $imageSrcKey);
// 			$this->logger("warning", $imageDirPath);

			// Get files name from directory
			$imageFileNames = $this->getFileName($imageDirPath);
// 			$this->logger("error", $fileNames);
			// Loop in image files
			foreach ($imageFileNames as $imageFileName){
				// V.V.V I note Destination move image file name will be same as  $imageSrcKey
				foreach ($imageUniqueSearchKeys as $imageSrcKey)
				{
					// Check search key exist in image file name.
					if (strpos($imageFileName, $imageSrcKey) !== false) {
						// If Exist Move to Soft or Hard Folder
						$this->logger("info", $imageDirPath.'/'.$imageFileName);
	// 					$this->logger("warning", $this->destination_csv_dir.'\\'.$imageFileName);
						if(file_exists ($imageDirPath.'/'.$imageFileName)){
							copy($imageDirPath.'/'.$imageFileName, $this->destination_csv_dir.'/'.$imageSrcKey.'/'.$imageFileName);
						}
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
			$file_list = array_values(array_diff(scandir($dir), array('..', '.')));
		}
		return $file_list;
    }
}
