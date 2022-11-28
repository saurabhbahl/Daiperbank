<?php

use Illuminate\Database\Seeder;

class MockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$this->call(PickupDateSeeder::class);
		$this->call(OrderSeeder::class);
    }
}
