<?php

namespace App\Http\Controllers;

use App\Product;
use App\Station;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class HomeController extends Controller
{
	private $save_to_path = '';

	public function index ()
	{
		$stations = Station::where('is_deleted', 0)
						   ->get();

		return view('home.index', compact('stations'));
	}

	public function imageCrawler ()
	{
		// this line is mandatory
		// get the public path where to store the image
		$this->save_to_path = public_path('media');
		$products = Product::where('is_deleted', 0)
						   ->get();
		foreach ( $products as $product ) {
			$url = $product->product_url;
			if ( !filter_var($url, FILTER_VALIDATE_URL) ) {
				continue;
			}
			$images = $this->getImages($url);
			$i = 0;
			foreach ( $images as $image ) {
				$this->download_image($image, $i);
				$i++;
			}
		}

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
