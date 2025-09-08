<?php

namespace App\Contracts\Repositories;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Support\Collection;

interface TravelRequestRepositoryInterface
{
    public function getTravelRequestsForUser(array $filters, User $user): Collection;
    public function create(array $data): TravelRequest;
    public function update(TravelRequest $travelRequest, array $data): TravelRequest;
}
