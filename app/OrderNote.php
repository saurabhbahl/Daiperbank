<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderNote extends Model {
	protected $table = 'order_note';
	public $timestamps = true;
	protected $guarded = [];

	public function Author() {
		return $this->belongsTo(User::class, 'user_id', 'id', 'Author');
	}
}
