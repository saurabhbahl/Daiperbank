<?php namespace App\ViewGenerators\PickupDate;

use App\Order;
use App\PickupDate;
use App\ProductCategory;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class OrderList {
	public function prepareList(Collection $Orders) {
		return $Orders->map(function($Order) {
			$product_count = $this->countProducts($Order);
			$Order->diaper_count = $product_count->get(ProductCategory::CATEGORY_ID_DIAPERS, 0);
			$Order->pullup_count = $product_count->get(ProductCategory::CATEGORY_ID_PULLUPS, 0);

			return $Order;
		});
	}

	protected function countProducts(Order $Order) {
		return $Order->ApprovedItem->reduce(function($Products, $Item) {
			$Products[ $Item->Product->product_category_id ] = $Item->quantity + $Products->get( $Item->Product->product_category_id, 0 );
			return $Products;
		}, collect());
	}
}