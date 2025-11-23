<?php

namespace App\Policies;

use App\Models\Disease;
use App\Models\User;

class DiseasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Super admin and healthcare admins can view disease surveillance
        return $user->role_id === 1 || $user->role_id === 2;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Disease $disease): bool
    {
        // Super admin, healthcare admins, and doctors can view disease details
        return in_array($user->role_id, [1, 2, 3]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Doctors can create disease records
        return $user->role_id === 3;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Disease $disease): bool
    {
        // Super admin and the doctor who reported can update
        return $user->role_id === 1 || $user->id === $disease->reported_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Disease $disease): bool
    {
        // Only super admin can delete
        return $user->role_id === 1;
    }
}
