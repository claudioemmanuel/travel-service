<?php

namespace App\Policies;

use App\Models\TravelRequest;
use App\Models\User;

class TravelRequestPolicy
{
    public function view(User $user, TravelRequest $travelRequest): bool
    {
        return $user->is_approver || $travelRequest->user_id === $user->id;
    }

    public function updateStatus(User $user, TravelRequest $travelRequest): bool
    {
        return $user->is_approver;
    }

    public function updateOwner(User $user, TravelRequest $travelRequest): bool
    {
        return $travelRequest->user_id === $user->id;
    }
}
