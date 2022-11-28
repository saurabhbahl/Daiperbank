<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChildData extends Model {
	protected $table = 'child_data';
	public $timestamps = true;
	protected $guarded = [];

	protected $casts = [
		'status_wic' => 'boolean',
		'status_potty_train' => 'boolean',
	];

	public function Child() {
		return $this->belongsTo(Child::class, 'child_id', 'id', 'Child');
	}

	public function Location() {
		return $this->belongsTo(Zip::class, 'zip', 'zip', 'Location');
	}

	public function getAgeAttribute() {
		return $this->Child->dob->diffInYears();
	}

	public function getAgeMoAttribute() {
		return $this->Child->dob->diffInMonths();
	}

	public function setWeightAttribute($attribute) {
		$this->attributes['weight'] = $attribute * 16;
	}

	public function getWeightAttribute() {
		return round($this->attributes['weight'] / 16, 2);
	}

	public function save(array $options = []) {
		if ($tbr = parent::save($options)) {
			$this->updateChildDraftOrders();
		}

		return $tbr;
	}

	protected function updateChildDraftOrders() {
		$Order = $this->Child->Order()->where('order_status', Order::STATUS_DRAFT)->get();
		$Order->map(function ($Order) {
			$Order->pivot->status_wic = $this->status_wic;
			$Order->pivot->status_potty_train = $this->status_potty_train;
			$Order->pivot->save();
		});
	}

	// public function getUpdatedByUserIdAttribute() {
	// 	return auth()->user()->id ?? null;
	// }

	// public function setUpdatedByUserIdAttribute($value) {
	// 	$this->attributes['updated_by_user_id'] = $value;

	// 	return $this;
	// }
}
