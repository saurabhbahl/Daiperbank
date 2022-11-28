<?php

namespace Tests\Unit;

use App\Agency;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase {

	use DatabaseMigrations;

	/** @test */
	function userIsNotAdmin() {
		$Agency = factory(Agency::class)->create();
		$User = factory(User::class)->create([
			'agency_id' => $Agency->id,
		]);

		$this->assertFalse($User->isAdmin());
	}

	/** @test */
	function userIsAnAdmin() {
		$Agency = factory(Agency::class)->create([
			'flag_is_admin' => true,
		]);
		$User = factory(User::class)->create([
			'agency_id' => $Agency->id,
		]);

		$this->assertTrue($User->isAdmin());
	}
}
