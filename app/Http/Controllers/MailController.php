<?php namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Monogram\AppMailer;
use Monogram\Helper;

class MailController extends Controller
{
	public function mailer (Request $request)
	{
		$message_type = intval($request->get('message_type'));
		$order_id = $request->get('order');

		if ( !array_key_exists($message_type, Helper::$MESSAGE_TYPES) ) {
			return response()->json([

			], 422);
		}

		$order = Order::with('items', 'customer')
					  ->where('order_id', $order_id)
					  ->where('is_deleted', 0)
					  ->first();

		if ( !$order ) {
			return response()->json([ ], 404);
		}
		$message_body = null;
		if ( $message_type == 0 ) {
			$message_body = '';
		} elseif ( $message_type == 1 ) {
			/*$message_body = view('emails.abcd')
				->with('order', $order)
				->render();*/
			$message_body = 'invoice';
		} elseif ( $message_type == 2 ) {
			/*$message_body = view('emails.abcd')
				->with('order', $order)
				->render();*/
			$message_body = "Packing slip";
		} elseif ( $message_type == 3 ) {
			$message_body = view('emails.return_from')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 4 ) {
			$message_body = view('emails.tracking')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 5 ) {
			$message_body = view('emails.back_ordered')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 6 ) {
			$message_body = view('emails.request_to_change')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 7 ) {
			$message_body = view('emails.response_requested')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 8 ) {
			$message_body = view('emails.need_address_verification')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 9 ) {
			$message_body = view('emails.order_delay')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 10 ) {
			$message_body = view('emails.purchase_in_back_order')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 11 ) {
			$message_body = view('emails.refund_issued')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 12 ) {
			$message_body = view('emails.store_credit')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 13 ) {
			$message_body = view('emails.order_status')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 14 ) {
			$message_body = view('emails.bottle_opener_error')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 15 ) {
			$message_body = view('emails.repair_fee')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 16 ) {
			$message_body = view('emails.cancelled_order')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 17 ) {
			$message_body = view('emails.drop_shipper_order')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 18 ) {
			$message_body = view('emails.return_items')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 19 ) {
			$message_body = view('emails.return_item_old_return')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 20 ) {
			$message_body = view('emails.item_sold_out')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 21 ) {
			$message_body = view('emails.incomplete_gift_basket')
				->with('order', $order)
				->render();
		} elseif ( $message_type == 22 ) {
			$message_body = view('emails.holiday_back_order')
				->with('order', $order)
				->render();
		}

		return response()->json([
			'error'   => false,
			'subject' => $this->subjectBuilder($message_type, $order),
			'message' => $message_body,
		]);
	}

	private function subjectBuilder ($type, $order)
	{
		return sprintf("%s - ORDER - %s", Helper::$MESSAGE_TYPES[$type], $order->short_order);
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
}
