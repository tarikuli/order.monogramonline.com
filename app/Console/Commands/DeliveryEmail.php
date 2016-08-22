<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Hamcrest\Arrays\IsArray;
use App\Setting;
use Monogram\Helper;
use App\Http\Controllers\PrintController;

class DeliveryEmail extends Command
{
	// https://www.youtube.com/watch?v=mp-XZm7INl8
	private $save_to_path = '';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deliveryemail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Script will send all delivery email.';

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
		// get the public path where to store the image
    	$this->save_to_path = public_path('assets/exports/station_log/');
    	if (file_exists($this->save_to_path."deliveryemail")){
    		return false;
    	}
		Helper::createLock("deliveryemail");
		PrintController::sendShippingConfirmByScript();
		Helper::deleteLock("sort_csvfiles");
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
				if (!is_dir($dir.'/'.$val)){
					$file_list[] = $val;
				}
			}
		}
		return $file_list;
    }
}
