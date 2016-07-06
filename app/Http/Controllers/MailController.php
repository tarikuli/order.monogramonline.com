<?php namespace App\Http\Controllers;

use App\EmailTemplate;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Monogram\AppMailer;
use Monogram\Helper;

class MailController extends Controller
{
	private $order = null;

	public function mailer (Request $request)
	{
		$message_type = intval($request->get('message_type'));
		$order_id = $request->get('order');
		$in_static_data = array_key_exists($message_type, Helper::$MESSAGE_TYPES);
		$template = EmailTemplate::where('is_deleted', 0)
								 ->find($message_type);
		$this->order = Order::with('items', 'customer', 'store')
							->where('order_id', $order_id)
							->where('is_deleted', 0)
							->first();

		// not in static message types
		// and not in template table
		// or order is not available
		if ( ( !$in_static_data && !$template ) || !$this->order ) {
			// nothing found
			return response()->json([
			], 422);
		}
		// check if the message type in static data
		$subject = '';
		$message_body = '';
		if ( $in_static_data ) {
			if ( $message_type == 0 ) {
				$subject = "Email";
				$message_body = '';
			} elseif ( $message_type == 1 ) {
				$subject = sprintf("Invoice - Order - %s", $this->order->short_order);
				// this is the view, was created before for the order page,
				// that's why you need to render
				// as the page is not served by the browser
				$message_body = (new PrintController())->invoice($order_id)
													   ->render();
			} elseif ( $message_type == 2 ) {
				$subject = sprintf("Packing slip - Order - %s", $this->order->short_order);
				// this is the view, was created before for the order page,
				// that's why you need to render
				// as the page is not served by the browser
				$message_body = (new PrintController())->packing($order_id)
													   ->render();
			}
		} elseif ( $template ) {
			// or in template
			$subject = $template->message_title;
			$message_body = $this->messageBuilder($template->message);
		}
		$subject = $this->subjectBuilder($subject);

		return response()->json([
			'error'   => false,
			'subject' => $subject,
			'message' => $message_body,
		]);
	}

	private function messageBuilder ($message)
	{
		return $this->stringParser($message);
	}

	private function subjectBuilder ($subject)
	{
		return $this->stringParser(sprintf("%s", $subject));
	}

	public function send_mail (Request $request, AppMailer $mailer)
	{
		$subject = $request->get('subject');
		$message = $request->get('message');
		$order_id = $request->get('order_id');
		$order = Order::with('customer')
					  ->where('order_id', $order_id)
					  ->where('is_deleted', 0)
					  ->first();
		if ( !$order ) {
			return response()->json([ ], 404);
		}
		$mailer->sendMailToCustomer($order->customer, $subject, $message);
	}

	private function stringParser ($string)
	{
		$pattern = implode("|", array_keys(Helper::$EMAIL_TEMPLATE_KEYWORDS));
		// escape the string
		$pattern = str_replace(array_keys(Helper::$REGEX_ESCAPES), array_values(Helper::$REGEX_ESCAPES), $pattern);
		$pattern = sprintf("~%s~", $pattern);
		$parsed = preg_replace_callback($pattern, [
			$this,
			"processor",
		], $string);

		return $parsed;
	}

	private function processor ($match)
	{
		$found = $match[0];
		if ( array_key_exists($found, Helper::$EMAIL_TEMPLATE_KEYWORDS) ) {
			// get the value at index 1 on email template
			$relations = Helper::$EMAIL_TEMPLATE_KEYWORDS[$found][1];
			// if relation as string
			if ( is_string($relations) ) {
				return $this->extractRelationInformation($relations);
				// given as array,
				// multiple relation
			} elseif ( is_array($relations) ) {
				$extracted_relation = [ ];
				foreach ( $relations as $relation ) {
					$extracted_relation[] = $this->extractRelationInformation($relation);
				}
				// before joining the array,
				// filter down the empty results
				#return implode(", ", array_filter($extracted_relation));
				return implode(", ", $extracted_relation);
			}

			return $relations;
		}

		return $found;
	}

	private function extractRelationInformation ($relation)
	{
		// explode the string based on dot
		$parts = explode(".", $relation);
		$data = $this->order;
		foreach ( array_slice($parts, 1) as $part ) {
			// keep extracting the data until the final data is found
			$data = $data->$part;
		}

		// return data
		return $data;
	}
}
