<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderChild extends Model {

	const STATE_PENDING = 1;
	const STATE_APPROVED = 2;
	const STATE_REJECTED = 0;

	protected $table = 'order_child';
	public $timestamps = false;
	protected $guarded = [];

	protected $appends = [
		'uniq_id',
		'name',
		'dob',
		'gender',
		'age',
		'age_mo',
		'age_str',
		'zip',
		'created_by_user_id',
		'weight_str',
		'weight_lbs',
		'is_menstruator',
	];

	protected $casts = [
		'status_wic' => 'boolean',
		'status_potty_train' => 'boolean',
	];

	public function Order() {
		return $this->belongsTo(Order::class, 'order_id', 'id', 'Order');
	}

	public function Child() {
		return $this->belongsTo(Child::class, 'child_id', 'id', 'Child')->withTrashed();
	}

	public function Item() {
		return $this->hasOne(OrderItem::class, 'order_child_id', 'id')
					->withTrashed();
	}

	public function Location() {
		return $this->hasOne(Location::class, 'zip', 'zip', 'Location');
	}

	public function getUniqIdAttribute() {
		return $this->Child->uniq_id;
	}

	public function getNameAttribute() {
		return $this->Child->name;
	}

	public function getDobAttribute() {
		return $this->Child->dob;
	}

	public function getGenderAttribute() {
		return $this->Child->gender;
	}
	public function getIsMenstruatorAttribute() {
		return $this->Child->is_menstruator;
	}
	
	public function getWeightStrAttribute() {
		$weight = $this->weight_oz;

		$lbs = floor($weight / 16);
		$oz = null;
		$weight = $weight % 16;

		if ($lbs <= 20 && $weight > 0) {
			$oz = $weight;
		}

		if ( ! $lbs && ! $oz) {
			return '';
		}

		return "{$lbs} lbs" . ($oz? " {$oz} oz" : "");
	}

	public function getWeightOzAttribute() {
		return $this->weight * 16;
	}

	public function getWeightLbsAttribute() {
		return floor($this->weight / 16);
	}

	public function getWeightAttribute() {
		return (float) round($this->attributes['weight'] / 16, 2);
	}

	public function setWeightAttribute($value) {
		$this->attributes['weight'] = $value * 16;
	}

	public function getAgeAttribute() {
		return $this->dob->diffInYears($this->Order->created_at);
	}

	public function getAgeMoAttribute() {
		return $this->dob->diffInMonths($this->Order->created_at);
	}

	public function getAgeStrAttribute() {
		$age_yr = $this->age;
		$age_mo = $this->age_mo;

		$str_parts = [];
		if ($age_yr) {
			$str_parts []= "{$age_yr}yr";
		}

		if ($age_mo) {
			if ($age_yr) {
				$rem_mo = $age_mo % ($age_yr * 12);

				if ($rem_mo) {
					$str_parts []= "{$rem_mo}mo";
				}
			} else {
				$str_parts []= "{$age_mo}mo";
			}
		}

		if (!$age_yr && !$age_mo) {
			$age_wk = max(1, $this->dob->diffInWeeks($this->Order->created_at));

			$str_parts []= "{$age_wk}wk";
		}

		return implode(" ", $str_parts);
	}

	public function getZipAttribute() {
		return $this->Child->zip;
	}

	public function getCreatedByUserIdAttribute() {
		return $this->Order->created_by_user_id;
	}

	public function getFlagApprovedAttribute() {
		if ($this->Item) {
			return $this->Item->flag_approved;
		}

		return false;
	}

	public function getDeletedAt() {
		if ($this->Item) {
			return $this->Item->deleted_at;
		}

		return false;
	}

	public function getState() {
		if ( ! $this->flag_approved && ! $this->deleted_at) {
			return static::STATE_PENDING;
		}
		else if ( ! $this->deleted_at) {
			return static::STATE_APPROVED;
		}

		return static::STATE_REJECTED;
	}

	public function approve() {
		if ($this->Item && !$this->Item->deleted_at) {
			return $this->Item->update([
				'flag_approved' => true,
			]);
		}

		return false;
	}

	public function restoreToPending() {
		if ($this->Item) {
			$this->Item->restore();
			return $this->Item->update([
				'flag_approved' => false,
			]);
		}

		return false;
	}

	public function removeFromOrder($force = false) {
		if ($force) {
			$this->forceDelete();
		} elseif ($this->Item) {
			$this->Item->update([
				'flag_approved' => false,
			]);
			return $this->Item->delete();
		}

		return false;
	}

	public function restoreToOrder() {
		if ($this->Item) {
			$this->Item->update([
				'flag_approved' => false,
			]);
			return $this->Item->restore();
		}

		return false;
	}

	public function updateData($orderChild) {
		if ($orderChild instanceOf Model) {
			$orderChild = $orderChild->getOriginal();
		}
		else {
			$orderChild = (array) $orderChild;
		}

		$this->update([
			'weight' => array_get($orderChild, 'weight'),
			'status_potty_train' => array_get($orderChild, 'status_potty_train'),
			'status_wic' => array_get($orderChild, 'status_wic'),
		]);

		$this->updateChildData();
		return $this;
	}

	public function updateChildData() {
		$this->Child->updateFromOrder($this);
	}

	public function addItem($product_id, $quantity) {
		if ($this->Item) {
			$this->Item->update([
				'product_id' => $product_id,
				'quantity' => $quantity,
			]);

			return $this->Item;
		}
		else {
			$this->Item()->save($Item = new OrderItem([
				'order_id' => $this->order_id,
				'product_id' => $product_id,
				'quantity' => $quantity,
			]));

			return $Item->exists? $Item : false;
		}
	}
}
