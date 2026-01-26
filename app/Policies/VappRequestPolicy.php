<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Models\Vapp\VappRequest;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class VappRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */


    public function view(User $user, VappRequest $vapp_request): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy VappRequestPolicy::view user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy VappRequestPolicy::view use_id=' . $user->id . ' request_created_by=' . $vapp_request->created_by);
        return $user->id == $vapp_request->created_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VappRequest $vapp_request): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy VappRequestPolicy::view user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy VappRequestPolicy::update use_id=' . $user->id . ' request_created_by=' . $vapp_request->created_by);
        return $user->id == $vapp_request->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VappRequest $vapp_request): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy VappRequestPolicy::delete user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy VappRequestPolicy::delete use_id=' . $user->id . ' request_created_by=' . $vapp_request->created_by);
        return $user->id == $vapp_request->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Employee $employee): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        //
    }
}
