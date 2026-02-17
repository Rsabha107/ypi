<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Models\Ypi\ParticipantDocument;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class ParticipantDocumentPolicy
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


    public function view(User $user, ParticipantDocument $participantDocument): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy ParticipantDocumentPolicy::view user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy ParticipantDocumentPolicy::view use_id=' . $user->id . ' participant_document_created_by=' . $participantDocument->created_by);
        return $user->id == $participantDocument->created_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy ParticipantDocumentPolicy::create user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy ParticipantDocumentPolicy::create use_id=' . $user->id);
        return true; // Adjust this logic based on your requirements
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ParticipantDocument $participantDocument): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy ParticipantDocumentPolicy::update user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy ParticipantDocumentPolicy::update use_id=' . $user->id . ' participant_document_created_by=' . $participantDocument->created_by);
        return $user->id == $participantDocument->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ParticipantDocument $participantDocument): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy ParticipantDocumentPolicy::delete user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy ParticipantDocumentPolicy::delete use_id=' . $user->id . ' participant_document_created_by=' . $participantDocument->created_by);
        return $user->id == $participantDocument->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ParticipantDocument $participantDocument): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ParticipantDocument $participantDocument): bool
    {
        //
    }
}
