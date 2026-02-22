<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;

class AssessmentPolicy
{
    /**
     * Any authenticated user can create an assessment.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * A user may only view their own assessments.
     */
    public function view(User $user, Assessment $assessment): bool
    {
        return $user->id === $assessment->user_id;
    }

    /**
     * Assessments are immutable once submitted – no update allowed.
     */
    public function update(User $user, Assessment $assessment): bool
    {
        return false;
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        return false;
    }
}
