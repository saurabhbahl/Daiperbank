<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->call(BaseInstallUserSeeder::class);
		$this->call(ProductSeeder::class);
		$this->call(InventorySeeder::class);
		$this->call(UserSeeder::class);
	}
}
