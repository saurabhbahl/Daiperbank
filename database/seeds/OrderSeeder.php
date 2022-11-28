<?php

use App\Agency;
use App\Child;
use App\Guardian;
use App\Order;
use App\PickupDate;
use App\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
	public function __construct() {
		$this->faker = \Faker\Factory::create();
	}

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$Agencies = $this->generateAgencies(5);
    	$Agencies->map(function($Agency) {
    		$this->generateChildren($Agency, 10);
    		$this->generateOrders($Agency, rand(2,5));
    	});
    }

    public function generateAgencies($amount) {
    	$agencies = [];
    	foreach (range(0, $amount) as $itr) {
	    	$agency = factory(Agency::class)->create([
	    		'name' => $this->faker->unique()->company,
	    		'id_prefix' => $this->faker->randomNumber(4),
	    	]);

	    	factory(User::class)->create([
	    		'agency_id' => $agency->id,
	    		'name' => $agency->name,
	    		'email' => $this->faker->unique()->safeEmail,
	    	]);

	    	$agencies []= $agency;
	    }

	    return collect($agencies);
    }

    public function generateChildren(Agency $Agency, $amount) {
    	$children = [];
    	foreach (range(0, $amount) as $itr) {
    		$Child = factory(Child::class)->create([
    			'agency_id' => $Agency->id,
    		]);

    		$Guardian = factory(Guardian::class)->create([
    			'agency_id' => $Agency->id,
    		]);

    		$ChildData = $Child->Data()->create([
    			'child_id' => $Child->id,
    			'updated_by_user_id' => $Agency->User->id,
    			'guardian_id' => $Guardian->id,
    			'guardian_type' => $this->faker->randomElement(['parent','grand-parent','aunt-or-uncle','adopted-parent','other']),
    			'weight' => round($Child->age_mo * 4.33 * .35, 0),
    			'zip' => '17404',
    			'status_wic' => rand(0, 1),
    			'status_potty_train' => (bool) $Child->age_mo > 26,
    		]);

    		$children []= $Child;
    	}

    	return collect($children);
    }

    public function generateOrders($Agency, $amount) {
    	$orders = [];
    	foreach (range(0, $amount) as $itr) {
    		$OrderChildren = $Agency->Children()//->orderByRaw('RAND()')
                                ->take(4)->get();

                                // Child::with(['Data', 'NewestData'])
                                // ->where('agency_id', $Agency->id)
                                // ->orderByRaw('RAND()')
                                // ->take(4)->get();

    		$Order = Order::create([
    			'agency_id' => $Agency->id,
    			'created_by_user_id' => $Agency->User->id,
    			'order_status' => Order::STATUS_DRAFT,
    			'pickup_date_id' => PickupDate:://where('pickup_date', '>', DB::raw('now'))
    											orderBy('pickup_date', 'DESC')
    											->take(3)
    											->get()
    											->random()
    											->id,
    		]);

    		foreach ($OrderChildren as $Child) {
    			$OrderChild = $Order->addChild($Child);
    			$OrderChild->updateData([
    				'weight' => $Child->weight,
    				'zip' => $Child->zip,
    				'guardian_id' => $Child->guardian_id,
    				'guardian_type' => $Child->guardian_type,
    				'status_potty_train' => $Child->status_potty_train,
    				'status_wic' => $Child->status_wic,
    				'age' => $Child->age,
    			]);

    			$OrderChild->addProduct([
    				'id' => $Child->getSuggestedProduct()->id,
    				'quantity' => rand(10, 25),
    			]);
    		}

    		$Order->update([
    			'order_status' => Order::STATUS_PENDING_APPROVAL,
    		]);

    		$orders []= $Order;
    	}

    	return collect($orders);
    }
}
