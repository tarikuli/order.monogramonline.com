<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
#use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Auth;
use Illuminate\Support\MessageBag;

class AuthenticationController extends Controller
{
    public function getLogin ()
    {
        return view('auth.login');
    }

    public function postLogin (LoginRequest $request)
    {
//     	$allow_ips = ["96.57.0.130"];
//     	if (!in_array(getenv('REMOTE_ADDR'), $allow_ips)) {
//     		abort(403);
//     	}
    	
        $email = $request->get('email');
        $password = $request->get('password');
        $remember = $request->has('remember') ? true : false;

        if ( !Auth::attempt([
            'email'        => $email,
            'password'     => $password,
            'is_deleted'   => 0,
            #'is_suspended' => 0,
        ], $remember)
        ) {
            $message = new MessageBag([
                'Username and password do not match',
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors($message);
        }

        return redirect()->intended(url('/'));
    }

    public function getLogout ()
    {
        Auth::logout();
		session()->flush();
        return redirect(url('/login'));
    }
}
