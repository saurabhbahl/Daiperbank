<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model {
	protected $table = 'product_category';
	public $timestamps = true;

	const CATEGORY_ID_DIAPERS = 1;
	const CATEGORY_ID_PULLUPS = 2;
	const CATEGORY_ID_PERIOD  = 3;

	public function Product() {
		return $this->hasMany(Product::class, 'product_category_id', 'id', 'Product');
	}
}
