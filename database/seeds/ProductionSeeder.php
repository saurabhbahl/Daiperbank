<?php

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->call(ProductionBaseUserSeeder::class);
		$this->call(ProductSeeder::class);
	}
}
