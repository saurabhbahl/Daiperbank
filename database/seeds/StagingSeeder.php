<?php

use Illuminate\Database\Seeder;

class StagingSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->call(BaseInstallUserSeeder::class);
		$this->call(ProductSeeder::class);
	}
}
