<?php

use App\Agency;
use App\Contact;
use App\User;
use Illuminate\Database\Seeder;

class BaseInstallUserSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$HSAgency = factory(Agency::class)->create([
			'name' => 'Healthy Steps',
			'id_prefix' => '1337',
			'address' => '111 Algrave Way',
			'city' => 'Columbia',
			'state' => 'SC',
			'zip' => '29229',
			'flag_is_admin' => 1,
		]);

		$Contact = factory(Contact::class)->create([
			'name' => 'Jim Rubenstein',
			'email' => 'healthy-steps@jimsc.com',
			'phone' => '8037677969',
		]);

		$HSAgency->Contact()->sync([$Contact->id]);

		$JimUser = factory(User::class)->create([
			'agency_id' => $HSAgency->id,
			'name' => 'Jim Rubenstein',
			'email' => 'jrubenstein@gmail.com',
			'username' => 'jrubenstein',
			'password' => 'hs1234',
		]);
	}
}
