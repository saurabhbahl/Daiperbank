<?php

namespace App\Policies;

use App\Agency;
use App\Order;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy {
	use HandlesAuthorization;

	public function before(User $user) {
		if ($user->isAdmin()) {
			return true;
		}
	}

	/**
	 * Determine whether the user can view the order.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Order  $order
	 * @return mixed
	 */
	public function view(User $user, Order $order) {
		return $user->Agency->id === $order->Agency->id;
	}

	/**
	 * Determine whether the user can create orders.
	 *
	 * @param  \App\User  $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->Agency->agency_status == Agency::STATUS_ACTIVE;
	}

	/**
	 * Determine whether the user can update the order.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Order  $order
	 * @return mixed
	 */
	public function update(User $user, Order $order) {
		return $user->Agency->id === $order->Agency->id;
	}

	/**
	 * Determine whether the user can delete the order.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Order  $order
	 * @return mixed
	 */
	public function delete(User $user, Order $order) {
		return $user->Agency->id === $order->Agency->id;
	}

	public function note(User $User, Order $Order) {
		return $User->Agency->id === $Order->agency_id || $User->isAdmin();
	}
}
