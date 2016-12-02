<?php namespace Monogram;

use App\Customer;
use App\User;
use Illuminate\Contracts\Mail\Mailer;
use App\Order;

class AppMailer
{
	/**
	 * The Laravel Mailer instance.
	 * @var Mailer
	 */
	protected $mailer;

	/**
	 * The sender of the emails.
	 * @var string
	 */
	protected $from = '';

	/*
	 * The sender name
	 * @var string
	 */
	protected $sender_name = '';

	/**
	 * The recipient of the emails.
	 * @var string
	 */
	protected $to;

	/**
	 * The recipient cc of the emails.
	 * @var string
	 */
	protected $cc;

	/**
	 * The recipient cc of the emails.
	 * @var string
	 */
	protected $bcc;

	/**
	 * The view for the emails.
	 * @var string
	 */
	protected $view;

	/**
	 * The data associated with the view for the emails.
	 * @var array
	 */
	protected $data = [ ];


	/**
	 * The subject of the emails
	 * @var string
	 */
	protected $subject = '';

	/**
	 * Create a new app mailer instance.
	 *
	 * @param Mailer $mailer
	 */
	public function __construct (Mailer $mailer)
	{
		$this->mailer = $mailer;
		$this->cc = "shlomi@monogramonline.com";//[ ];
	}

	/**
	 * Deliver the emails confirmation.
	 *
	 * @param  User $user
	 *
	 * @return void
	 */
	public function sendEmail (User $user)
	{
		$this->from = env("APPLICATION_DEFAULT_EMAIL");
		$this->sender_name = env("APPLICATION_NAME");
		$this->subject = "Dummy email";
		$this->to = $user->email;
		$this->view = 'emails.confirm';
		$this->data = compact('user');
		$this->deliver();
	}


	/**
	 * Deliver the emails confirmation.
	 *
	 * @param $modules
	 * @param $bill_email
	 * @param $subject
	 *
	 * @return bool
	 */
	public function sendDeliveryConfirmationEmail ($modules, $bill_email, $subject)
	{
		// 		dd($bill_email, $subject); // nortonzanini@gmail.com
		$this->from = env("APPLICATION_DEFAULT_EMAIL");
		$this->sender_name = env("APPLICATION_NAME");
		$this->subject = $subject;
		$this->to = $bill_email;
		// 		$this->cc = 'shlomi@monogramonline.com';
				$this->cc = 'jewel@monogramonline.com';
		$this->view = 'emails.shippingconfirm';
		$this->data = compact('modules');
		$this->deliver();

		return true;
	}

	public function sendMailToCustomer (Customer $customer, $subject, $message)
	{
		$this->from = env("APPLICATION_DEFAULT_EMAIL");
		$this->sender_name = env("APPLICATION_NAME");
		$this->subject = $subject;
		$this->to = $customer->bill_email;/*"sirajul.islam.anik@gmail.com";*/
		$this->view = 'emails.all_email_placeholder';
		$this->data = [ 'email_body' => $message ];
		$this->deliver();
	}

	/**
	 * Deliver the emails.
	 * @return void
	 */
	private function deliver ()
	{
		$this->mailer->send($this->view, $this->data, function ($message) {
			$message->from($this->from, $this->sender_name)
					->to($this->to)// ->cc($this->cc)->bcc($this->bcc)
					->subject($this->subject);
		});
	}
}