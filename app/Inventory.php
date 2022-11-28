<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model {
	protected $table = 'inventory';
	public $timestamps = true;
	protected $guarded = [];

	const TYPE_CREDIT = 'credit';
	const TYPE_DEBIT = 'debit';

	public function Adjustment() {
		return $this->belongsTo(InventoryAdjustment::class, 'inventory_adjustment_id', 'id', 'Adjustment');
	}

	public function Product() {
		return $this->belongsTo(Product::class, 'product_id', 'id', 'Product');
	}

	public function isCredit() {
		return $this->txn_type == static::TYPE_CREDIT;
	}

	public function isDebit() {
		return $this->txn_type == static::TYPE_DEBIT;
	}
}
