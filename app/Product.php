<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
	protected $table = 'product';
	public $timestamps = true;

	protected $appends = [
		'full_name',
	];

	public function Category() {
		return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id', 'Category');
	}

	public function getFullNameAttribute() {
		return "{$this->name} {$this->Category->name}";
	}
}
