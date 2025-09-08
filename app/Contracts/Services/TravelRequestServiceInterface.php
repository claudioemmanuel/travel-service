<?php

namespace App\Contracts\Services;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface TravelRequestServiceInterface
{
    public function getTravelRequestsForUser(array $filters, User $user): LengthAwarePaginator;
    public function create(array $data, User $user): TravelRequest;
    public function update(array $data, TravelRequest $travelRequest): TravelRequest;
    public function findByIdForUser(int $id, User $user): ?TravelRequest;
}
