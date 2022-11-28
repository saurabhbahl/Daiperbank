<?php

namespace App\Policies;

use App\Note;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderNotePolicy {
	use HandlesAuthorization;

	public function before(User $User) {
		if ($User->isAdmin()) {
			return true;
		}
	}

	/**
	 * Determine whether the user can view the note.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Note  $note
	 * @return mixed
	 */
	public function view(User $user, Note $note) {
		//
	}

	/**
	 * Determine whether the user can create notes.
	 *
	 * @param  \App\User  $user
	 * @return mixed
	 */
	public function create(User $user) {
		//
	}

	/**
	 * Determine whether the user can update the note.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Note  $note
	 * @return mixed
	 */
	public function update(User $user, Note $note) {
		//
	}

	/**
	 * Determine whether the user can delete the note.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Note  $note
	 * @return mixed
	 */
	public function delete(User $user, Note $note) {
		return $user->id === $note->user_id || $user->isAdmin();
	}
}
