<?php

namespace App\Policies;

use App\Agency;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AgencyPolicy {
	use HandlesAuthorization;

	public function before(User $User) {
		if ($User->isAdmin()) {
			return true;
		}
	}

	public function view(User $User, Agency $Agency = null) {
		return $Agency->id == $User->agency_id;
	}

	public function createNote(User $User, Agency $Agency) {
		return $User->isAdmin();
	}

	public function create(User $User, Agency $Agency = null) {
		return false;
	}

	public function update(User $User, Agency $Agency = null) {
		return $User->Agency->id === $Agency->id;
	}

	public function delete(User $User, Agency $Agency = null) {
		return false;
	}
}
