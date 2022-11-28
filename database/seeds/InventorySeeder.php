<?php

use App\Inventory;
use App\InventoryAdjustment;
use App\Product;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$User = User::all()->first();
		$Txn = factory(InventoryAdjustment::class)->create([
			'adjustment_type' => config('hsdb.inventory.adjustment_map.ADJUSTMENT'),
			'created_by_user_id' => $User->id,
			'adjustment_note' => 'Seeded product inventory',
			'adjustment_datetime' => Carbon::now(),
		]);

		Product::all()->each(function ($Product) use ($Txn, $User) {
			factory(Inventory::class)->create([
				'inventory_adjustment_id' => $Txn->id,
				'txn_type' => 'credit',
				'product_id' => $Product->id,
				'amount' => rand(125, 325),
			]);
		});
	}
}
