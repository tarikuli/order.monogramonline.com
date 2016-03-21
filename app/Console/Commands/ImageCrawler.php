<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;

class ImageCrawler extends Command
{
	private $save_to_path = '';

	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'image:crawl';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Download image from server';

	/**
	 * Create a new command instance.
	 */
	public function __construct ()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle ()
	{
		// handle the next operations from next method.
		$this->imageCrawler();
	}

	public function imageCrawler ()
	{
		// this line is mandatory
		// get the public path where to store the image
		$this->save_to_path = public_path('media');
		$products = Product::where('is_deleted', 0)
						   ->get();
		// start progress bar
		$progressBar = $this->output->createProgressBar(count($products));
		foreach ( $products as $product ) {
			$url = $product->product_url;
			if ( !filter_var($url, FILTER_VALIDATE_URL) ) { // url is invalid
				$this->error(sprintf("<%'*30s> - error - <%s>", $product->id_catalog, $url));
			} else {
				$images = $this->getImages($url);
				$i = 0;
				foreach ( $images as $image ) {
					$this->info(sprintf("<%'*30s> - Downloaded image - %d", $product->id_catalog, ( $i + 1 )));
					$this->download_image($image, $i);
					$i++;
				}
			}
			$progressBar->advance();
			$this->info(PHP_EOL);
		}

		#$progressBar->finish();


		return sprintf('Downloaded %d images.', count($product));
	}

	private function download_image ($image_url, $index)
	{
		$path_parts = pathinfo($image_url);
		$extension = $path_parts['extension'];
		$filename = $path_parts['filename'];

		$new_image_name = sprintf("%s-%d.%s", $filename, $index, $extension);
		$save_image_path = sprintf("%s/%s", $this->save_to_path, $new_image_name);
		$this->save_image_to_disk($image_url, $save_image_path);
	}

	private function save_image_to_disk ($source_url, $destination_url)
	{
		$image = Image::make($source_url);
		$image->save($destination_url);
	}

	private function getImages ($url)
	{
		$images = [ ];
		$html = new \Htmldom($url);
		// get from main image div
		foreach ( $html->find('.mainImage a') as $element ) {
			$images[] = $element->href;
		}
		// get from extra images, if has any
		foreach ( $html->find('.extraImagesWrap a') as $element ) {
			$images[] = $element->href;
		}

		return $images;
	}
}
