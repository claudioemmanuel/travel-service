<?php

namespace App\Contracts\Services;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Support\Collection;

interface TravelRequestServiceInterface
{
    public function getTravelRequestsForUser(array $filters, User $user): Collection;
    public function create(array $data, User $user): TravelRequest;
    public function update(array $data, TravelRequest $travelRequest): TravelRequest;
}
