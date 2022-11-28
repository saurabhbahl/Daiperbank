<?php

namespace App\Policies;

use App\Inventory;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy {
	use HandlesAuthorization;

	protected function isUserAdmin(User $User) {
		return $User->isAdmin();
	}

	/**
	 * Determine whether the user can view the inventory.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Inventory  $inventory
	 * @return mixed
	 */
	public function view(User $User, Inventory $Inventory = null) {
		return $this->isUserAdmin($User);
	}

	/**
	 * Determine whether the user can create inventories.
	 *
	 * @param  \App\User  $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $this->isUserAdmin($User);
	}

	/**
	 * Determine whether the user can update the inventory.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Inventory  $inventory
	 * @return mixed
	 */
	public function update(User $user, Inventory $inventory) {
		return $this->isUserAdmin($User);
	}

	/**
	 * Determine whether the user can delete the inventory.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Inventory  $inventory
	 * @return mixed
	 */
	public function delete(User $user, Inventory $inventory) {
		return $this->isUserAdmin($User);
	}
}
