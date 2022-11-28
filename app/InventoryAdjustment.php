<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class InventoryAdjustment extends Model {
	protected $table = 'inventory_adjustment';
	public $timestamps = true;
	protected $guarded = [];

	protected $casts = [
		'adjustment_datetime' => 'datetime',
	];

	protected $appends = [
		'detail_string',
	];

	const TYPE_DONATION = 1;
	const TYPE_DIAPER_DRIVE = 2;
	const TYPE_PURCHASE = 3;
	const TYPE_ADJUSTMENT = 4;
	const TYPE_ORDER = 5;
	const TYPE_OTHER = 6;
	const TYPE_GC_PURCHASE = 7;

	public function getDates() {
		return parent::getDates() + ['adjustment_datetime'];
	}

	public function Inventory() {
		return $this->hasMany(Inventory::class, 'inventory_adjustment_id', 'id', 'Inventory');
	}

	public function getDetailStringAttribute() {
		$string = static::getTypeMap()[$this->adjustment_type];

		if ($this->adjustment_type == config('hsdb.inventory.adjustment_map.ORDER')) {
			$string .= " {$this->order_id}";
		}

		return $string;
	}

	public function isFromOrder() {
		return $this->adjustment_type == config('hsdb.inventory.adjustment_map.ORDER')
		&& null !== $this->order_id;
	}

	public static function createFromOrder(Order $Order) {
		if (static::adjustmentForOrderExists($Order)) {
			return false;
		}

		$Adjustment = DB::transaction(function() use ($Order) {
			$Adjustment = static::create([
				'created_by_user_id' => $Order->created_by_user_id,
				'adjustment_type' => config('hsdb.inventory.adjustment_map.ORDER'),
				'order_id' => $Order->id,
				'adjustment_datetime' => $Order->PickupDate->pickup_date,
			]);

			foreach ($Order->ApprovedItem as $Item) {
				$Adjustment->Inventory()->create([
					'txn_type' => Inventory::TYPE_DEBIT,
					'product_id' => $Item->product_id,
					'amount' => $Item->quantity,
				]);
			}

			return $Adjustment;
		});

		return $Adjustment;
	}

	public static function adjustmentForOrderExists(Order $Order) {
		return static::where('order_id', $Order->id)->first();
	}

	public static function getTypeMap() {
		return config('hsdb.inventory.adjustment_types');
	}
}
