<?php

namespace App;

use Gate;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Session;

class User extends Authenticatable {
	use Notifiable;
	protected $guarded = [];

	public function Agency() {
		return $this->belongsTo(Agency::class, 'agency_id', 'id', 'Agency');
	}

	public function isAdmin() {
		return $this->Agency->isAdmin();
	}

	public function activate() {
		$this->update(['flag_active' => 1]);
	}

	public function deactivate() {
		$this->update(['flag_active' => 0]);
	}

	public function setPasswordAttribute($value) {
		$this->attributes['password'] = app('hash')->make($value);
	}

	public function isBeingImpersonated() {
		return Session::has('imposter') && Session::get('imposter') != $this->id;
	}

	public function stopImpersonating() {
		Auth()->loginUsingId(Session::get('imposter'));
		Session::forget('imposter');
		return $this;
	}

	public function impersonate(User $User) {
		if (Gate::denies('impersonate', $User)) {
			throw new \Exception('You are not authorized to do that.');
		}

		Session::put('imposter', $this->id);
		Auth()->login($User);

		return $this;
	}
}
