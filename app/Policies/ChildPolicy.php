<?php

namespace App\Policies;

use App\User;
use App\Child;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChildPolicy
{
    use HandlesAuthorization;

    public function before(User $user) {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the child.
     *
     * @param  \App\User  $user
     * @param  \App\Child  $child
     * @return mixed
     */
    public function view(User $user, Child $child)
    {
        return $user->Agency->id === $child->agency_id;
    }

    /**
     * Determine whether the user can create children.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->Agency->isActive();
    }

    /**
     * Determine whether the user can update the child.
     *
     * @param  \App\User  $user
     * @param  \App\Child  $child
     * @return mixed
     */
    public function update(User $user, Child $child)
    {
        return $user->Agency->id === $child->agency_id;
    }

    /**
     * Determine whether the user can delete the child.
     *
     * @param  \App\User  $user
     * @param  \App\Child  $child
     * @return mixed
     */
    public function delete(User $user, Child $child)
    {
        return $user->Agency->id === $child->agency_id;
    }
}
