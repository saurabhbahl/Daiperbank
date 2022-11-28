<?php namespace Tests\Feature;

use App\Agency;
use App\Contact;
use App\Decorators\Order\ClonedOrder;
use App\Decorators\Order\PendingOrder;
use App\Guardian;
use App\Order;
use App\PickupDate;
use App\Product;
use App\ProductCategory;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

require_once __DIR__ . '/../../database/seeds/OrderSeeder.php';
require_once __DIR__ . '/../../database/seeds/ProductSeeder.php';

class OrderTest extends TestCase {
	use DatabaseMigrations;

	/** @test */
	public function creates_orders() {
		$Agency = factory(Agency::class)->create();

		$User = factory(User::class)->create([
			'agency_id' => $Agency->id,
		]);

		$PickupDate = factory(PickupDate::class)->create();

		$Product = factory(Product::class)->create([
			'product_category_id' => factory(ProductCategory::class)->create()->id,
		]);

		$Guardian = factory(Guardian::class)->create([
			'agency_id' => $Agency->id,
			'name' => 'A spectacular parent',
		]);

		$Order = [
			'Children' => [
				[
					'id' => 'new--1',
					'name' => 'Test Kid',
					'uniq_id' => rand(12345, 9999),
					'gender' => 'm',
					'dob' => date('Y-m-d', strtotime('-1 month')),
					'weight' => '10.5',
					'zip' => '29229',
					'status_wic' => 1,
					'status_potty_train' => 0,
					'guardian_id' => $Guardian->id,
					'guardian_type' => 'parent',

					'Product' => [
						'id' => $Product->id,
						'qty' => 50,
					],

					'isNew' => true,
				],
			],
			'pickup_date_id' => $PickupDate->id,
		];

		$this->actingAs($User);
		$this->disableExceptionHandling();
		$response = $this->json('post', '/api/orders/create', $Order);
		// dd($Order, $response->json());
		$response->assertSuccessful();
		$response->assertJsonStructure([
			'success',
			'data' => [
				'id',
			],
		]);

		// dd($response);
	}

	/** @test */
	public function createsOrderWithGuardian() {
		$Agency = factory(Agency::class)->create();

		$User = factory(User::class)->create([
			'agency_id' => $Agency->id,
		]);

		$PickupDate = factory(PickupDate::class)->create();

		$Product = factory(Product::class)->create([
			'product_category_id' => factory(ProductCategory::class)->create()->id,
		]);

		$Order = [
			'Children' => [
				[
					'id' => 'new--1',
					'name' => 'Test Kid',
					'uniq_id' => rand(12345, 9999),
					'gender' => 'm',
					'dob' => date('Y-m-d', strtotime('-1 month')),
					'weight' => '10.5',
					'zip' => '29229',
					'status_wic' => 1,
					'status_potty_train' => 0,
					'guardian_id' => 'new--1',
					'guardian_type' => 'father',

					'Product' => [
						'id' => $Product->id,
						'qty' => 50,
					],

					'isNew' => true,

					'Guardian' => [
						'name' => 'Jim',
						'gender' => 'm',
						'email' => 'jim@jimsc.com',
						'phone' => '8037677969',
						'address' => '111 algrave way',
						'address_2' => null,
						'city' => 'columbia',
						'state' => 'pa',
						'zip' => '29229',
						'military_status' => 'not-military',
					],
				],
			],
			'pickup_date_id' => $PickupDate->id,
		];

		$this->actingAs($User);
		$this->disableExceptionHandling();
		$response = $this->json('post', '/api/orders/create', $Order);
		// dd($Order, $response->json());
		$response->assertSuccessful();
		$response->assertJsonStructure([
			'success',
			'data' => [
				'id',
			],
		]);
	}

	/** @test */
	public function clonesOrders() {
		$Agency = factory(Agency::class)->create();

		$User = factory(User::class)->create([
			'agency_id' => $Agency->id,
		]);

		$PickupDate = factory(PickupDate::class)->create();

		$Product = factory(Product::class)->create([
			'product_category_id' => factory(ProductCategory::class)->create()->id,
		]);

		$Order = [
			'Children' => [
				[
					'id' => 'new--1',
					'name' => 'Test Kid',
					'uniq_id' => rand(12345, 9999),
					'gender' => 'm',
					'dob' => date('Y-m-d', strtotime('-1 month')),
					'weight' => '10.5',
					'zip' => '29229',
					'status_wic' => 1,
					'status_potty_train' => 0,
					'guardian_id' => 'new--1',
					'guardian_type' => 'father',

					'Product' => [
						'id' => $Product->id,
						'qty' => 50,
					],

					'isNew' => true,

					'Guardian' => [
						'name' => 'Jim',
						'gender' => 'm',
						'email' => 'jim@jimsc.com',
						'phone' => '8037677969',
						'address' => '111 algrave way',
						'address_2' => null,
						'city' => 'columbia',
						'state' => 'pa',
						'zip' => '29229',
						'military_status' => 'not-military',
					],
				],
			],
			'pickup_date_id' => $PickupDate->id,
		];

		$this->actingAs($User);
		$this->disableExceptionHandling();
		$response = $this->json('post', '/api/orders/create', $Order)->json();
		$order_id = $response['data']['id'];

		$Order = Order::with(['Child', 'Item', 'Child.Child', 'PickupDate', ])->find($order_id);
		$ClonedOrder = ClonedOrder::create($Order);

		$this->assertNotEquals($ClonedOrder->id, $Order->id);
		$this->assertEquals($ClonedOrder->Child->count(), $Order->Child->count());
		$this->assertEquals($ClonedOrder->Item->count(), $Order->Item->count());
		$this->assertEquals($ClonedOrder->pickup_date_id, $Order->pickup_date_id);
		$this->assertEquals($ClonedOrder->agency_id, $Order->agency_id);
		$this->assertNotNull($ClonedOrder->cloned_from_order_id);
		$this->assertEquals($ClonedOrder->cloned_from_order_id, $Order->id);

		$NewProduct = factory(Product::class)->create([
			'product_category_id' => $Product->product_category_id,
		]);

		$ClonedOrder->updateChildren([
			$Order->Child->first()->child_id => [
				'product_id' => $NewProduct->id,
				'qty' => 1234,
			],
		]);

		$this->assertEquals($NewProduct->id, $ClonedOrder->Child->first()->Item->product_id);
		$this->assertEquals(1234, $ClonedOrder->Child->first()->Item->quantity);

		$ClonedOrder->removeChildren([ $ClonedOrder->Child->first()->child_id ]);

		$this->assertCount(0, $ClonedOrder->Child->all());

		$NewPickupDate = factory(PickupDate::class)->create([
			'pickup_date' => strtotime('+10 days'),
		]);

		$ClonedOrder->updatePickupDate($NewPickupDate->id);
		$ClonedOrder->refresh();

		$this->assertEquals($NewPickupDate->id, $ClonedOrder->pickup_date_id);

		$PendingOrder = (new PendingOrder($ClonedOrder))->save();
		$ClonedOrder->refresh();

		$this->assertEquals($PendingOrder->id, $ClonedOrder->id);
		$this->assertEquals(Order::STATUS_PENDING_APPROVAL, $ClonedOrder->order_status);
	}

	/** @test */
	function rejectsOrders()
	{
		$this->disableExceptionHandling();

		$Agency = factory(Agency::class)->create();
		$Contact = factory(Contact::class)->make([
			'email' => 'healthysteps@jimsc.com',
		]);

		$Agency->Contact()->create($Contact->toArray());
		factory(User::class)->create([
			'agency_id' => $Agency->id,
		]);

		factory(PickupDate::class)->create();

		$AdminUser = factory(User::class)->create();

		$this->seedProducts();
		$Order = $this->generateOrderForAgency($Agency);

		$this->actingAs($AdminUser);

		$response = $this->json('post', '/api/orders/' . $Order->id . '/reject', [
			'reason' => "Testing
Multi

Line


Rejection",
			'flag_share_reason' => true,
		]);

		$response->assertSuccessful();
		$response->assertJsonStructure(['success', 'next']);
		$response->assertJson([
			'success' => true,
		]);
	}

	public function seedProducts() {
		(new \ProductSeeder)->run();
	}

	public function generateOrderForAgency($Agency) {
		$OrderSeeder = new \OrderSeeder;

		$Children = $OrderSeeder->generateChildren($Agency, 2);
		$Order = $OrderSeeder->generateOrders($Agency, 1)->first();

		return $Order;
	}
}
