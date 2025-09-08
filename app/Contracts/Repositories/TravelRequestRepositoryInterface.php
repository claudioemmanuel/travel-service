<?php

namespace App\Contracts\Repositories;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface TravelRequestRepositoryInterface
{
    public function getTravelRequestsForUser(array $filters, User $user): LengthAwarePaginator;
    public function findByIdForUser(int $id, User $user): ?TravelRequest;
    public function create(array $data): TravelRequest;
    public function update(TravelRequest $travelRequest, array $data): TravelRequest;
}
