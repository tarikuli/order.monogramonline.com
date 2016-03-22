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
	protected $signature = 'image:crawl {catalog?} {--forward}';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Download image from stores';

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
		// this line is mandatory
		// get the public path where to store the image
		$this->save_to_path = public_path('media');

		// handle the next operations from next method.
		#$this->imageCrawler();
		$allow_forward = false;
		$id_catalog = $this->argument('catalog');
		if ( !empty( $this->option('forward') ) ) {
			$allow_forward = true;
		}

		if ( !empty( $this->argument('catalog') ) ) {
			$id_catalog = $this->argument('catalog');
		}
		$products = null;
		if ( $id_catalog ) {
			$products = Product::where('id_catalog', $id_catalog)
							   ->get();
			if ( count($products) == 0 ) {
				$this->error("0 Products found to crawl");

				return;
			}
			if ( $allow_forward ) {
				$products = Product::where('id', '>=', $products->first()->id)
								   ->get();
			}
		} else {
			$products = Product::where('is_deleted', 0)
							   ->get();
		}
		
		if ( count($products) == 0 ) {
			$this->error("0 Products found to crawl");
		} else {
			$this->logger("warning", "SKU", "MESSAGE", "RESULT");
			$this->imageCrawler($products);
		}

	}

	public function imageCrawler ($products)
	{
		// start progress bar
		$progressBar = $this->output->createProgressBar(count($products));
		$imagesTotal = 0;
		$errorsTotal = 0;
		foreach ( $products as $product ) {
			$url = $product->product_url;
			$is_error = false;
			$segment_one = '';
			$segment_two = '';
			$segment_three = '';
			if ( !filter_var($url, FILTER_VALIDATE_URL) ) { // url is invalid
				$segment_one = $product->id_catalog;
				$segment_two = "Error in URL";
				$segment_three = $url;
				$this->logger("error", $segment_one, $segment_two, $segment_three);
				++$errorsTotal;
			} else {
				$images = $this->getImages($url);
				$i = 0;
				$imagesTotal += count($images);
				$error_occurred = 0;
				foreach ( $images as $image ) {
					if ( $this->download_image($image, $i) ) {
						$this->logger("info", $product->id_catalog, "Downloaded image", $i + 1);
						++$error_occurred;
					} else {
						$this->logger("error", $product->id_catalog, "Invalid image URL", $image);
					}
					$i++;
				}
				$this->logger("info", $product->id_catalog, "Error/Total", sprintf("%d/%d", $error_occurred, $imagesTotal));
			}

			$progressBar->advance();
			$this->info(PHP_EOL);
		}

		#$progressBar->finish();
		// next line shows the final countdown
		$this->info(sprintf("Total of %d images download. %d errors found out of %d products.", $imagesTotal, $errorsTotal, count($products)));
	}

	private function download_image ($image_url, $index)
	{
		$path_parts = pathinfo($image_url);
		$extension = isset( $path_parts['extension'] ) ? $path_parts['extension'] : '';
		$filename = isset( $path_parts['filename'] ) ? $path_parts['filename'] : '';
		if ( empty( $extension ) || empty( $filename ) ) {
			return false;
		}

		$new_image_name = sprintf("%s-%d.%s", $filename, $index, $extension);
		$save_image_path = sprintf("%s/%s", $this->save_to_path, $new_image_name);
		$this->save_image_to_disk($image_url, $save_image_path);

		return true;
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

	private function logger ($type, $segment_one, $segment_two, $segment_three = '')
	{
		$message = sprintf("%-30s - %-30s - %s", $segment_one, $segment_two, $segment_three);
		if ( $type == 'info' ) {
			$this->info($message);
		} elseif ( $type == 'error' ) {
			$this->error($message);
		} elseif ( $type == 'warning' ) {
			$this->warn($message);
		}
	}

}
