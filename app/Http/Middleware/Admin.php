<?php

namespace App\Http\Middleware;

use Closure;

class Admin
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (auth()->check() && auth()->user()->isAdmin()) {
			return $next($request);
		}
		dd(auth()->user());

		$message = "You are not authorized to do that";
		if ( ! auth()->check()) {
			$message = "Your session has timed out. Please log in again.";
		}

		if ($request->wantsJson()) {
			return response()->json([
				'success' => false,
				'message' => $message
			], 403);
		}
		else {
			flash($message)->error();
			return redirect()->route(auth()->check()? 'home' : 'login');
		}
	}
}
