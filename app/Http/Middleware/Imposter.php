<?php namespace App\Http\Middleware;

use Auth;
use Closure;

class Imposter
{
	public function handle($request, Closure $next)
	{
		if ($request->session()->has('impersonate')) {
			Auth::onceUsingId($request->session()->get('impersonate'));
		}

		return $next($request);
	}
}
