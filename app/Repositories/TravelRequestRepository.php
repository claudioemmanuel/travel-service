<?php

namespace App\Repositories;

use App\Contracts\Repositories\TravelRequestRepositoryInterface;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class TravelRequestRepository implements TravelRequestRepositoryInterface
{
    public function __construct(
        private TravelRequest $travelRequest
    ) {}

    public function getTravelRequestsForUser(array $filters, User $user): LengthAwarePaginator
    {
        $query = $user->travelRequests();

        if (isset($filters['status'])) {
            $query->ByStatus($filters['status']);
        }

        if (isset($filters['destination'])) {
            $query->ByDestination($filters['destination']);
        }

        if (isset($filters['departure_date'])) {
            $query->ByDateRange($filters['departure_date'], $filters['return_date']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 10);
    }

    public function create(array $data): TravelRequest
    {
        return $this->travelRequest->create($data);
    }

    public function update(TravelRequest $travelRequest, array $data): TravelRequest
    {
        $travelRequest->update($data);
        return $travelRequest;
    }

    public function findByIdForUser(int $id, User $user): ?TravelRequest
    {
        return $user->travelRequests()->where('id', $id)->first();
    }
}
