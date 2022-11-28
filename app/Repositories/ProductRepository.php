<?php namespace App\Repositories;

use App\Product;

class ProductRepository {
	public function getAllProductsWithInventory() {
		$Products = Product::orderBy('product_category_id', 'ASC')
							->orderBy('name', 'ASC')
							->get()
							->keyBy('id');

		$Inventory = new InventoryRepository;
		$product_inventories = $Inventory->getProductInventory();

		foreach ($product_inventories as $product_inventory) {
			$product_inventory->ordered = 0;
			$Products->get($product_inventory->product_id)->Inventory = $product_inventory;
		}

		return $Products;
	}
}