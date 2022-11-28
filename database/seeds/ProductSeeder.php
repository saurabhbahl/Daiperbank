<?php

use App\Product;
use App\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->createDiapers();
		$this->createPullups();
	}

	protected function createDiapers() {
		$Category = factory(ProductCategory::class)->create([
			'name' => 'Diaper',
		]);

		$sizes = ['Preemie',
			'Newborn',
			'Size 1',
			'Size 2',
			'Size 3',
			'Size 4',
			'Size 5',
			'Size 6',
			'Size 7',
		];

		$this->createProducts($Category, $sizes);
	}

	protected function createPullups() {
		$Category = factory(ProductCategory::class)->create([
			'name' => 'Pull-up',
		]);

		$sizes = [
			'2T-3T Boy',
			'3T-4T Boy',
			'4T-5T Boy',
			'2T-3T Girl',
			'3T-4T Girl',
			'4T-5T Girl',
		];

		$this->createProducts($Category, $sizes);
	}

	protected function createProducts(ProductCategory $Category, array $sizes) {
		foreach ($sizes as $size) {
			factory(Product::class)->create([
				'product_category_id' => $Category->id,
				'name' => $size,
			]);
		}
	}
}
