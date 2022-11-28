<?php namespace App\Providers;

use Illuminate\Auth\SessionGuard;

class SessionNoPassGuard extends SessionGuard {
	protected function hasValidCredentials($user, $credentials) {
		return ! is_null($user);
	}
}