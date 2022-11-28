<?php namespace App\ViewGenerators\PickupDate;

use App\PickupDate;
use App\Fulfillment;
use App\ProductCategory;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class ExportedFulfillments {
	public function prepareList(Collection $PickupDates) {
		return $PickupDates->reduce(function($PickupDates, $PickupDate) {
			$PUD = new Fluent(
				$PickupDate->getAttributes()
			);

			$PUD->order_count = $this->countOrders($PickupDate);
			$PUD->orders_pending = $PickupDate->orders_pending;
			$PUD->orders_pending_export = $PickupDate->orders_pending_export;

			$PUD->child_count = $this->countChildren($PickupDate);
			$product_count = $this->countProducts($PickupDate);
			$PUD->diaper_count = $product_count->get(ProductCategory::CATEGORY_ID_DIAPERS, 0);
			$PUD->pullup_count = $product_count->get(ProductCategory::CATEGORY_ID_PULLUPS, 0);
			$PUD->ProductSummary = $this->summarizeProducts($PickupDate);
			$PUD->Fulfillment = collect($PickupDate->Fulfillment->map(function($F) {
				$Fulfillment = new Fluent($F->getAttributes());
				$Fulfillment->order_count = $this->getFulfillmentOrderCount($F);
				return $Fulfillment;
			}));

			$PickupDates->push($PUD);
			return $PickupDates;
		}, collect());
	}

	protected function countOrders(PickupDate $PickupDate) {
		return $PickupDate->Fulfillment->reduce(function($aggregate, $Fulfillment) {
			return $aggregate + $Fulfillment->Order()->count();
		}, 0);
	}

	protected function countChildren(PickupDate $PickupDate) {
		return $PickupDate->Fulfillment->reduce(function($aggregate, $Fulfillment) {
			return $Fulfillment->Order->reduce(function($aggregate, $Order) {
				return $aggregate + $Order->ApprovedChild()->count();
			}, $aggregate);
		}, 0);
	}

	protected function countProducts(PickupDate $PickupDate) {
		return $PickupDate->Fulfillment->reduce(function($Products, $Fulfillment) {
			return $Fulfillment->Order->reduce(function($Products, $Order) {
				return $Order->ApprovedItem->reduce(function($Products, $Item) {
					$Products[ $Item->Product->product_category_id ] = $Item->quantity + $Products->get( $Item->Product->product_category_id, 0 );
					return $Products;
				}, $Products);
			}, $Products);
		}, collect());
	}

	protected function summarizeProducts(PickupDate $PickupDate) {
		return $PickupDate->Fulfillment->reduce(function($Products, $Fulfillment) {
			return $Fulfillment->Order->reduce(function($Products, $Order) {
				return $Order->ApprovedItem->reduce(function($Products, $Item) {
					$Product = $Products->get( $Item->product_id, new Fluent([
						'id' => $Item->product_id,
						'name' => $Item->Product->name,
						'category_id' => $Item->Product->category_id,
						'category_name' => $Item->Product->Category->name,
						'order_count' => 0,
						'quantity' => 0,
					]));
					$Products[ $Item->product_id ] = $Product;

					$Product->order_count++;
					$Product->quantity += $Item->quantity;

					return $Products;
				}, $Products);
			}, $Products);
		}, collect());
	}

	protected function getFulfillmentOrderCount(Fulfillment $Fulfillment) {
		return $Fulfillment->Order->count();
	}
}