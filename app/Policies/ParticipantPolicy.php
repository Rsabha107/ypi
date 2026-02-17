<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Models\Ypi\Participant;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class ParticipantPolicy
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


    public function view(User $user, Participant $participant): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy ParticipantPolicy::view user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy ParticipantPolicy::view use_id=' . $user->id . ' participant_created_by=' . $participant->created_by);
        return $user->id == $participant->created_by;
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
    public function update(User $user, Participant $participant): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy ParticipantPolicy::update user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy ParticipantPolicy::update use_id=' . $user->id . ' participant_created_by=' . $participant->created_by);
        return $user->id == $participant->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Participant $participant): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy ParticipantPolicy::delete user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy ParticipantPolicy::delete use_id=' . $user->id . ' participant_created_by=' . $participant->created_by);
        return $user->id == $participant->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Participant $participant): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Participant $participant): bool
    {
        //
    }
}
