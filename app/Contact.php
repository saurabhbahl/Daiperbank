<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model {
	protected $table = 'contact';
	protected $guarded = [];

	protected function setPhoneAttribute($value) {
		$this->attributes['phone'] = preg_replace('#[^\d]+#', '', $value);
	}

	public function Agency() {
		return $this->belongsToMany(Agency::class, 'agency_contact', 'contact_id', 'agency_id', 'Agency');
	}
}
