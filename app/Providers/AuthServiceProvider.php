<?php

namespace App\Providers;

use App\Agency;
use App\Child;
use App\Inventory;
use App\Note;
use App\Order;
use App\Policies\AgencyPolicy;
use App\Policies\ChildPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\OrderNotePolicy;
use App\Policies\OrderPolicy;
use App\Policies\UserPolicy;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		Agency::class => AgencyPolicy::class,
		Inventory::class => InventoryPolicy::class,
		Order::class => OrderPolicy::class,
		Child::class => ChildPolicy::class,
		Note::class => OrderNotePolicy::class,
		User::class => UserPolicy::class,
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->registerPolicies();
	}
}
