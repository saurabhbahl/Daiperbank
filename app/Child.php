<?php

namespace App;

use App\ProductCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Child extends Model {
	use SoftDeletes;

	protected $table = 'child';
	public $timestamps = true;
	protected $guarded = [];

	protected $casts = [
		'dob' => 'date',
	];

	protected $with = ['NewestData'];

	protected $appends = [
		'age',
		'age_mo',
		'age_str',
		'updated_by_user_id',
		'weight',
		'weight_str',
		'status_wic',
		'status_potty_train'
	];

	protected $meta_fields = [
		'age',
		'age_mo',
		'updated_by_user_id',
		'weight',
		'status_wic',
		'status_potty_train'
	];

	public function Data() {
		return $this->hasMany(ChildData::class, 'child_id', 'id', 'Data');
	}

	public function NewestData() {
		return $this->hasOne(ChildData::class, 'child_id', 'id', 'Data')->orderBy('created_at', 'desc');
	}

	public function Location() {
		return $this->belongsTo(Zip::class, 'zip', 'zip', 'Location');
	}

	public function Agency() {
		return $this->belongsTo(Agency::class, 'id', 'agency_id', 'Agency');
	}

	public function Guardian() {
		return $this->belongsTo(Guardian::class, 'guardian_id', 'id', 'Guardian');
	}

	public function Sibling() {
		return $this->hasMany(Child::class, 'guardian_id', 'guardian_id', 'Sibling')
					->where('id', '!=', $this->id)
					->where('agency_id', $this->agency_id);
	}

	public function Order() {
		return $this->belongsToMany(Order::class, 'order_child', 'child_id', 'order_id', 'Order')->withPivot('weight', 'status_wic', 'status_potty_train');
	}

	public function OrderItem() {
		return $this->hasManyThrough(OrderItem::class, OrderChild::class, 'child_id', 'order_child_id', 'id');
	}


	public function getUpdatedByUserIDAttribute() {
		return $this->NewestData->updated_by_user_id ?? null;
	}

	public function getWeightAttribute() {
		return $this->NewestData->weight ?? null;
	}

	public function getWeightStrAttribute() {
		$weight = $this->weight;

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

	public function getStatusWicAttribute() {
		return $this->NewestData->status_wic ?? null;
	}

	public function getStatusPottyTrainAttribute() {
		return $this->NewestData->status_potty_train ?? null;
	}

	public function getDates() {
		return parent::getDates() + [
			'dob'
		];
	}

	/**
	 * Get age of child in years
	 *
	 * @return [type] [description]
	 */
	public function getAgeAttribute() {
		return $this->dob? $this->dob->diffInYears() : null;
	}

	/**
	 * Get age of child in months
	 *
	 * @return [type] [description]
	 */
	public function getAgeMoAttribute() {
		return $this->dob? $this->dob->diffInMonths() : null;
	}

	public function getAgeMonthsAttribute() {
		return $this->dob? $this->getAgeMoAttribute() : null;
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

			if ( ! $this->dob) {
				return null;
			}

			$age_wk = max(1, $this->dob->diffInWeeks(carbon()));

			$str_parts []= "{$age_wk}wk";
		}

		return implode(" ", $str_parts);
	}

	/**
	 * Update the current child's data from the data present in an OrderChild record
	 *
	 * @param  OrderChild $OrderChild	The OrderChild record
	 *
	 * @return Child $this
	 */
	public function updateFromOrder(OrderChild $OrderChild)
	{
		$NewestData = $this->NewestData()->first();

		if (!$NewestData) {
			$NewestData = new ChildData;
		}

		$NewestData->fill(array_only(
			$OrderChild->toArray(),
			[
				'weight',
				'status_potty_train',
				'status_wic',
			]
		));

		if ($NewestData->isDirty()) {
			$NewestData->fill([
				'updated_by_user_id' => $OrderChild->created_by_user_id,
			]);

			$this->NewestData()->create(array_only(
				$NewestData->toArray(),
				[
					'weight',
					'status_potty_train',
					'status_wic',
					'updated_by_user_id',
				]
			));

			$this->load('NewestData');
		}

		return $this;
	}

	public function getAgeInMonths(Carbon $atDate = null) {
		return $this->dob? $this->dob->diffInMonths($atDate ?? carbon()) : null;
	}

	public function getBenefitSummary() {
		return $this->OrderItem->reduce(function($Summary, $Item) {
			if ($Item->Order->isFulfilled()) {
				switch ($Item->Product->product_category_id) {
					case ProductCategory::CATEGORY_ID_DIAPERS:
						$Summary->diapers += $Item->quantity;
						break;

					case ProductCategory::CATEGORY_ID_PULLUPS:
						$Summary->pullups += $Item->quantity;
						break;
				}
			}

			return $Summary;
		},
			new class {
				public $diapers = 0;
				public $pullups = 0;

				public function toArray() {
					return [
						'diapers' => $this->diapers,
						'pullups' => $this->pullups,
					];
				}
			}
		);
	}

	public function getSuggestedProduct() {
		if (3 > $this->age_mo) {
			return Product::find(3);
		}
		else if (6 > $this->age_mo) {
			return Product::find(4);
		}
		else if (12 > $this->age_mo) {
			return Product::find(5);
		}
		else if (18 > $this->age_mo) {
			return Product::find(6);
		}
		else if (26 > $this->age_mo) {
			return Product::find(7);
		}
		else if (3 > $this->age) {
			return Product::find(10);
		}
		else if (4 > $this->age) {
			return Product::find(11);
		}
		else {
			return Product::find(12);
		}
	}
}
