<?php

namespace Tests\Browser;

use App\Agency;
use App\Contact;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AgencyTest extends DuskTestCase {
	use DatabaseMigrations;

	/** @test */
	function adminCanCreateAnAgency() {
		$Agency = factory(Agency::class)->create([
			'flag_is_admin' => true,
		]);
		$User = factory(User::class)->create([
			'agency_id' => $Agency->id,
		]);
		$Contact = factory(Contact::class)->create();
		$Agency->Contact()->sync([$Contact->id]);

		$this->browse(function (Browser $browser) use ($User) {
			$browser->loginAs($User)
				->visit(route('agency.create'))
				->type('#agency_name', 'fake agency')
				->type('#agency_id_prefix', '9999')
				->type('#user_username', 'fakeagency')
				->type('#user_password', 'fakepw')
				->type('#user_confirm_password', 'fakepw')
				->type('#agency_address', '1 fake street')
				->type('#agency_city', 'faketown')
				->select('#agency_state', 'PA')
				->type('#agency_zip', '17404')
				->type('#contact_name', 'mr faker')
				->type('#contact_email', 'mr.faker@example.com')
				->type('#contact_phone', '8037677969')
				->press('form[method=post] button[type=submit]');

			$NewAgency = Agency::where('id_prefix', 9999)->first();
			$this->assertNotNull($NewAgency);
			$this->assertEquals("fake agency", $NewAgency->name);

			$NewUser = User::where('agency_id', $NewAgency->id)->first();
			$this->assertNotNull($NewUser);
			$this->assertEquals("fakeagency", $NewUser->username);

			$Contact = $NewAgency->Contact()->first();
			$this->assertNotNull($Contact);
			$this->assertEquals("mr faker", $Contact->name);
		});
	}

	/** @test */
	function adminCanEditAgency() {
		$Agency = factory(Agency::class)->create([
			'flag_is_admin' => true,
		]);
		$User = factory(User::class)->create([
			'agency_id' => $Agency->id,
		]);
		$Contact = factory(Contact::class)->create();
		$Agency->Contact()->sync([$Contact->id]);

		$this->browse(function (Browser $browser) use ($Agency, $User) {
			$browser->loginAs($User)
				->visit(route('agency.edit', [$Agency->id]))
				->type('#agency_name', 'edited agency')
				->type('#agency_id_prefix', '1111')
				->type('#user_username', 'edited-username')
				->type('#user_password', 'edited-password')
				->type('#user_confirm_password', 'edited-password')
				->type('#agency_address', '1 edited-street')
				->type('#agency_city', 'edited-town')
				->select('#agency_state', 'SC')
				->type('#agency_zip', '29229')
				->type('#contact_name', 'mr editor')
				->type('#contact_email', 'mr.editor@example.com')
				->type('#contact_phone', '8039670069')
				->press('form[method=post] button[type=submit]');

			$browser->assertPathIs('/agency');
			$browser->assertSee('edited agency');

			$EditedAgency = $Agency->fresh();
			$this->assertEquals("edited agency", $EditedAgency->name);

			$AgencyUser = $Agency->User;
			$this->assertEquals("edited-username", $AgencyUser->username);
			$this->assertTrue(app('hash')->check('edited-password', $AgencyUser->password));

			$this->assertTrue(1 == $Agency->Contact->count());
			$Contact = $Agency->Contact()->first();
			$this->assertEquals("mr editor", $Contact->name);
			$this->assertEquals('mr.editor@example.com', $Contact->email);
			$this->assertEquals('8039670069', $Contact->phone);
		});
	}
}
