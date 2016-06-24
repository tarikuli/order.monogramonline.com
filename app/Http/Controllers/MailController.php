<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
	public function mailer (Request $request)
	{
		return $request->all();
	}

	public function send_mail (Request $request)
	{
		return $request->all();
	}
}
