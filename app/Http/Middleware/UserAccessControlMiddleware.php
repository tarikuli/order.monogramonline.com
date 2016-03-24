<?php

namespace App\Http\Middleware;

use App\Access;
use Closure;

class UserAccessControlMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 *
	 * @return mixed
	 */
	public function handle ($request, Closure $next)
	{
		// auth check is not necessary,
		// it's previously taken
		$uri = $request->route()
					   ->uri();
		if ( $this->url_checker($uri) ) {
			return $next($request);
		}

		return app()->abort(404);
	}

	public function url_checker ($request_uri)
	{
		$granted = auth()
			->user()
			->accesses()
			->lists('page');

		foreach ( $granted as $accessible ) {
			if ( strpos($request_uri, $accessible) !== false ) { // Yoshi version
				return true;
			}
		}

		return false;
	}
}
