<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 * @var array
	 */
	protected $commands = [
		\App\Console\Commands\Inspire::class,
		\App\Console\Commands\ImageCrawler::class,
		\App\Console\Commands\SortCsvFiles::class,
		\App\Console\Commands\SortImageFiles::class,
		\App\Console\Commands\DeliveryEmail::deliveryemail,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule $schedule
	 *
	 * @return void
	 */
	protected function schedule (Schedule $schedule)
	{
		$schedule->command('inspire')
				 ->hourly();

		$schedule->command('sort:csvfiles')
				 ->everyFiveMinutes();

		$schedule->command('sort:imagefiles')
				->everyTenMinutes();
	}
}
