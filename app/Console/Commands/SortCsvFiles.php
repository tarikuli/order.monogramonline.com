<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SortCsvFiles extends Command
{
	// https://www.youtube.com/watch?v=mp-XZm7INl8
	private $save_to_path = '';
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
        // this line is mandatory
		// get the public path where to store the image
		$this->save_to_path = public_path();

		$this->logger("warning", "IMAGE NAME", "MESSAGE", $this->save_to_path);
    }

    private function logger ($type, $segment_one, $segment_two, $segment_three = '')
    {
    	$message = sprintf("%-60s - %-30s - %s", $segment_one, $segment_two, $segment_three);
    	if ( $type == 'info' ) {
    		$this->info($message);
    	} elseif ( $type == 'error' ) {
    		$this->error($message);
    	} elseif ( $type == 'warning' ) {
    		$this->warn($message);
    	}
    	\Log::info($message);
    }
}
