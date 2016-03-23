<?php

namespace App\Console\Commands;

use App\Magento;
use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class ImageCrawler extends Command
{
	private $save_to_path = '';
	private $executed_product_image_name = '';

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
		// if id catalog is passed as argument
		if ( $id_catalog ) {
			// check if the id catalog is present in magento table
			$magento_product = Magento::where('id_catalog', $id_catalog)
									  ->first();
			// if the product doesn't exist
			if ( !$magento_product ) {
				$this->error("0 Products found to crawl");

				return;
			}
			// if forward flag is given
			if ( $allow_forward ) {
				$magento_products = Magento::where('id', '>=', $magento_product->id)
										   ->orderBy('id')
										   ->get();
				$products = Product::with('magento')
								   ->whereIn('id_catalog', $magento_products->lists('id_catalog'))
								   ->orderByRaw(DB::raw(sprintf("FIELD(id_catalog,'%s')", implode("','", $magento_products->lists('id_catalog')
																														  ->toArray()))))
								   ->get();
			} else { // if forward flag is not present
				$products = Product::where('id_catalog', $magento_product->id_catalog)
								   ->get();
			}
		} else { // id catalog is not passed as argument
			$magento_products = Magento::orderBy('id')
									   ->get();
			$products = Product::with('magento')
							   ->whereIn('id_catalog', $magento_products->lists('id_catalog'))
							   ->orderByRaw(DB::raw(sprintf("FIELD(id_catalog,'%s')", implode("','", $magento_products->lists('id_catalog')
																													  ->toArray()))))
							   ->get();
		}
		if ( count($products) == 0 ) { // no product found to crawl
			$this->error("0 Products found to crawl");
		} else {
			// products are available to be crawled
			$this->logger("warning", "IMAGE NAME", "MESSAGE", "RESULT");
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
			$id_catalog = $product->id_catalog;
			$save_image_base_name = $product->magento ? $product->magento->id_catalog_new : $product->id_catalog;
			$url = $product->product_url;
			if ( !filter_var($url, FILTER_VALIDATE_URL) ) { // url is invalid
				$this->logger("error", $product->id_catalog, "Error in URL", $url);
				++$errorsTotal;
			} else {
				$images = $this->getImageUrlsFromProductUrl($url);
				if ( count($images) == 0 ) {
					$this->logger("error", $product->id_catalog, "Product does not exist", $url);
				} else {
					$i = 0;
					$imagesTotal += count($images);
					$error_occurred = 0;
					$saved_images = [ ];
					foreach ( $images as $image ) {
						$image_name = $this->download_image($image, $i, $save_image_base_name);
						if ( $image_name !== false ) {
							#$this->logger("info", $product->id_catalog, "Downloaded image", $i + 1);
							$saved_images[] = $image_name;
							$this->logger("info", $image_name, "Downloaded image", $i + 1);
						} else {
							$this->logger("error", $product->id_catalog, "Invalid image URL", $image);
							++$error_occurred;
						}
						$i++;
					}
					$this->logger("info", sprintf("Finished: %s", $product->id_catalog), "Error/Total", sprintf("%d/%d", $error_occurred, count($images)));
					// save the image new names as json to table
					$product->product_remote_images = json_encode($saved_images);
					$product->save();

					// update magento table on successful grab
					Magento::where('id_catalog', $id_catalog)
						   ->update([
							   'is_updated' => 1,
						   ]);
				}
			}
			$progressBar->advance();
			$this->info(PHP_EOL);
			$this->warn(sprintf("%'*120s", ""));
		}

		#$progressBar->finish();
		// next line shows the final countdown
		$this->info(sprintf("Total of %d images download. %d errors found out of %d products.", $imagesTotal, $errorsTotal, count($products)));
	}

	private function download_image ($image_url, $index, $name_base)
	{
		$path_parts = pathinfo($image_url);
		$extension = isset( $path_parts['extension'] ) ? $path_parts['extension'] : '';
		#$filename = isset( $path_parts['filename'] ) ? $path_parts['filename'] : '';
		$filename = $name_base;
		if ( empty( $extension ) || empty( $filename ) ) {
			return false;
		}

		$new_image_name = sprintf("%s-%d.%s", $filename, $index, $extension);
		$save_image_path = sprintf("%s/%s", $this->save_to_path, $new_image_name);
		$this->save_image_to_disk($image_url, $save_image_path);

		return $new_image_name;
	}

	private function save_image_to_disk ($source_url, $destination_url)
	{
		$image = Image::make($source_url);
		$image->save($destination_url);
	}

	private function getImageUrlsFromProductUrl ($url)
	{
		$images = [ ];
		try {
			$html = new \Htmldom($url);
		} catch ( \Exception $e ) {
			return [ ];
		}
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
		$message = sprintf("%-60s - %-30s - %s", $segment_one, $segment_two, $segment_three);
		if ( $type == 'info' ) {
			$this->info($message);
		} elseif ( $type == 'error' ) {
			$this->error($message);
		} elseif ( $type == 'warning' ) {
			$this->warn($message);
		}
	}

}
