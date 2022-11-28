<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
	static $password;

	return [
		'name' => $faker->name,
		'username' => $faker->username,
		'email' => $faker->unique()->safeEmail,
		'password' => $password ?: 'secret',
		'remember_token' => str_random(10),
	];
});

$factory->define(App\Agency::class, function ($faker) {
	return [
		'name' => $faker->unique()->company,
		'id_prefix' => '1234',
		'address' => '1 Faking Fake',
		'city' => 'Diaper Town',
		'state' => 'PA',
		'zip' => '17739',
	];
});

$factory->define(App\Contact::class, function ($faker) {
	return [
		'name' => $faker->name,
		'email' => $faker->unique()->safeEmail,
		'phone' => substr(preg_replace('#\D+#', '', $faker->phoneNumber), -10),
	];
});

$factory->define(App\Product::class, function ($faker) {
	return [
		'name' => 'Fake Product',
	];
});

$factory->define(App\ProductCategory::class, function ($faker) {
	return [
		'name' => 'Fake Category',
	];
});

$factory->define(App\InventoryAdjustment::class, function ($faker) {
	return [
	];
});

$factory->define(App\Inventory::class, function ($faker) {
	return [
	];
});

$factory->define(App\PickupDate::class, function ($faker) {
	return [
		'pickup_date' => date('Y-m-d H:i:s', strtotime('tomorrow 3pm')),
	];
});

$factory->define(App\Guardian::class, function ($faker) {
	return [
		'agency_id' => function () { return factory(App\Agency::class)->create()->id; },
		'name' => $faker->unique()->name,
		'gender' => ['m', 'f'][rand(0, 1)],
		'phone' => substr(preg_replace('#\D#', '', $faker->phoneNumber), 0, 20),
		'email' => $faker->unique()->safeEmail,
		'address' => $faker->streetAddress,
		'address_2' => $faker->secondaryAddress,
		'city' => $faker->city,
		'state' => 'PA',
		'zip' => $faker->postcode,
		'military_status' => 'active',
	];
});

$factory->define(App\Child::class, function ($faker) {
	return [
		'agency_id' => function () {
			return factory(App\Agency::class)->create()->id;
		},
		'uniq_id' => $faker->unique()->randomNumber(7),
		'name' => $faker->unique()->name,
		'gender' => ['m', 'f'][rand(0, 1)],
		'dob' => $faker->dateTimeBetween('-5 years', '-1 month')->format('Y-m-d'),
	];
});
