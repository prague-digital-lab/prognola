<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    public function viewAsInvited(User $user, Workspace $workspace): bool
    {
        $workspace = $user->workspaces()->find($workspace->id);

        return $workspace !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Workspace $workspace): bool
    {
        $workspace = $user->workspaces()->withPivot(['role'])->find($workspace->id);

        return $workspace !== null && $workspace->pivot->role != 'invited';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Workspace $workspace): bool
    {
        return $user->workspaces()->find($workspace->id) !== null;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Workspace $workspace): bool
    {
        return $user->workspaces()->find($workspace->id) !== null;
    }
}
