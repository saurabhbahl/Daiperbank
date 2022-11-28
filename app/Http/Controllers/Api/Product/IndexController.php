<?php namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\ProductCategory;

class IndexController extends Controller {
	public function get() {
		$ProductCategories = ProductCategory::with('Product')->orderBy('name', 'ASC')->get();

		return response()->json([
			'success' => true,
			'data' => $ProductCategories,
		]);
	}
}
