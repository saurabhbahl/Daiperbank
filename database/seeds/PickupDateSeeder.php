<?php

use App\PickupDate;
use Illuminate\Database\Seeder;

class PickupDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$next_saturday = strtotime('next saturday');
    	$next_sunday = strtotime('next sunday');

    	foreach (range(0, 3) as $week) {
    		$rand_hour = str_pad(rand(9, 16), 2, '0', STR_PAD_LEFT);
    		PickupDate::create([
    			'pickup_date' => date("Y-m-d {$rand_hour}:00:00", $next_saturday),
    		]);

    		$rand_hour = str_pad(rand(9, 16), 2, '0', STR_PAD_LEFT);
    		PickupDate::create([
    			'pickup_date' => date("Y-m-d {$rand_hour}:00:00", $next_sunday),
    		]);

    		$next_saturday += (86400 * 7);
    		$next_sunday += (86400 * 7);
    	}
    }
}
