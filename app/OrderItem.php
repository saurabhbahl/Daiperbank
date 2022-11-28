<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model {
	use SoftDeletes;

	protected $table = 'order_item';
	public $timestamps = false;
	protected $guarded = [];

	protected $casts = [
		'flag_approved' => 'bool',
	];

	public function Product() {
		return $this->belongsTo(Product::class, 'product_id', 'id', 'Product');
	}

	public function Order() {
		return $this->belongsTo(Order::class, 'order_id', 'id', 'Order');
	}

	public function Child() {
		return $this->belongsTo(OrderChild::class, 'order_child_id', 'id');
	}
}
