<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Setting;
use Monogram\Helper;

class SortImageFiles extends Command
{
	// https://www.youtube.com/watch?v=mp-XZm7INl8
	private $save_to_path = '';

	private $imageSearchArray = [];

	private $source_image_dir = "";
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
		// get the public path where to store the image
    	$this->save_to_path = public_path('assets/exports/station_log/');
    	if (file_exists($this->save_to_path."sort_imagefiles")){
    		return false;
    	}
    	if (file_exists($this->save_to_path."sort_csvfiles")){
    		return false;
    	}
		Helper::createLock("sort_imagefiles");
		// Set Source file name
		$settings = Setting::all();
		$settings = $settings->toArray();
		// Put all Search Setting
$this->logger ("info","Called sort:imagefiles");
		foreach ($settings as  $fileNameIndex => $fileName){
// 			$this->logger("info", $fileName['supervisor_station']);

			switch ($fileName['supervisor_station']) {
				case "imageSearch":
					$this->imageSearchArray[$fileName['default_shipping_rule']] = $fileName['default_route_id'];
					break;
				case "source_image_dir":
					$this->source_image_dir = $fileName['default_route_id'];
					break;
			}

		}

		// Get Unique search key from imageSearchArray
		$imageUniqueSearchKeys = array_unique(array_keys($this->imageSearchArray ));

		// Get All  directory from
		if(file_exists ($this->source_image_dir)){
			$source_image_dir_list = $this->getFileName($this->source_image_dir);
			// Loop in Each Dir for Execute Business Logic
			foreach ($source_image_dir_list as $dir_name){
				$file_list_in_dir = $this->getFileName($this->source_image_dir.'/'.$dir_name);
// 				$this->logger("info", $file_list_in_dir);
				if(count($file_list_in_dir) > 0){
					// Search for Match key word in Current directory
					foreach ($this->imageSearchArray as  $imageSrcKey => $imageSearch){
// 						$this->logger("warning", $this->source_image_dir.'/'.$dir_name." -> ".$imageSrcKey." -> ".$file_list_in_dir[0]);
						if (strpos($file_list_in_dir[0], $imageSrcKey) !== false) {
// 							$this->logger("info", $imageSrcKey." ->	".$imageSearch." -> ".$file_list_in_dir[0]);
							$souece_dir = $this->source_image_dir."/".$dir_name;
							$move_dir = $imageSearch."/".$dir_name;
							$move_dir_done = $imageSearch."/../Done";
							$souece_dir_files = $move_dir_done."/".$dir_name."/.";
							$move_dir_files = $imageSearch;
							if($imageSrcKey == "soft"){
// 	$this->logger("error", $souece_dir_files);
// 	$this->logger("info", $move_dir_files);
								shell_exec("mv -r \"$souece_dir\" \"$move_dir_done\"");
								shell_exec("cp -r \"$souece_dir_files\" \"$move_dir_files\"");
							}elseif($imageSrcKey == "hard"){
// 								$this->logger("error", $souece_dir);
// 								$this->logger("info", $move_dir_done);
								shell_exec("mv -r \"$souece_dir\" \"$move_dir_done\"");
								shell_exec("cp -r \"$souece_dir_files\" \"$move_dir_files\"");
							}else{
								shell_exec("mv \"$souece_dir\" \"$move_dir\"");
							}
							break;
						}
					}
				}
			}
		}
		Helper::deleteLock("sort_imagefiles");
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
