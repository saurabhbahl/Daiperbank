<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model {
	protected $table = 'guardian';
	public $timestamps = true;
	protected $guarded = [];

	public function Child() {
		return $this->hasMany(Child::class, 'guardian_id', 'id', 'Child');
	}
}
