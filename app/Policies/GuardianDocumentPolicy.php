<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Models\Ypi\GuardianDocument;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class GuardianDocumentPolicy
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


    public function view(User $user, GuardianDocument $guardianDocument): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy GuardianDocumentPolicy::view user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy GuardianDocumentPolicy::view use_id=' . $user->id . ' guardian_document_created_by=' . $guardianDocument->created_by);
        return $user->id == $guardianDocument->created_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy GuardianDocumentPolicy::create user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy GuardianDocumentPolicy::create use_id=' . $user->id);
        return true; // Adjust this logic based on your requirements
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GuardianDocument $guardianDocument): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy GuardianDocumentPolicy::update user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy GuardianDocumentPolicy::update use_id=' . $user->id . ' guardian_document_created_by=' . $guardianDocument->created_by);
        return $user->id == $guardianDocument->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GuardianDocument $guardianDocument): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy GuardianDocumentPolicy::delete user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy GuardianDocumentPolicy::delete use_id=' . $user->id . ' guardian_document_created_by=' . $guardianDocument->created_by);
        return $user->id == $guardianDocument->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GuardianDocument $guardianDocument): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GuardianDocument $guardianDocument): bool
    {
        //
    }
}
