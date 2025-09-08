<?php

namespace App\Services;

use App\Contracts\Services\TravelRequestServiceInterface;
use App\Models\TravelRequest;
use App\Contracts\Repositories\TravelRequestRepositoryInterface;
use App\Models\User;
use App\Exceptions\TravelRequestException;
use Carbon\Carbon;
use App\Notifications\TravelRequestStatusChanged;
use Illuminate\Pagination\LengthAwarePaginator;

class TravelRequestService implements TravelRequestServiceInterface
{
    public function __construct(
        private TravelRequestRepositoryInterface $travelRequestRepository
    ) {}

    public function getTravelRequestsForUser(array $filters, User $user): LengthAwarePaginator
    {
        return $this->travelRequestRepository->getTravelRequestsForUser($filters, $user);
    }

    public function create(array $data, User $user): TravelRequest
    {
        $data['user_id'] = $user->id;
        $data['status'] = TravelRequest::STATUS_REQUESTED;

        return $this->travelRequestRepository->create($data);
    }

    public function update(array $data, TravelRequest $travelRequest): TravelRequest
    {
        $oldStatus = $travelRequest->status;

        if (
            $data['status'] === TravelRequest::STATUS_CANCELLED &&
            !$travelRequest->canBeCancelled()
        ) {
            throw new TravelRequestException('Travel request cannot be cancelled: 
                - not approved yet
                - already approved without 24 hours before the departure date
                - already cancelled');
        }

        $updatedData = ['status' => $data['status']];
        if ($data['status'] === TravelRequest::STATUS_APPROVED) {
            $updatedData['approved_at'] = Carbon::now();
        } else if ($data['status'] === TravelRequest::STATUS_CANCELLED) {
            $updatedData['cancelled_at'] = Carbon::now();
            $updatedData['cancellation_reason'] = $data['cancellation_reason'];
        }

        $this->travelRequestRepository->update($travelRequest, $updatedData);

        if ($oldStatus !== $data['status']) {
            $travelRequest->user->notify(new TravelRequestStatusChanged($travelRequest));
        }

        return $travelRequest->fresh();
    }

    public function findByIdForUser(int $id, User $user): ?TravelRequest
    {
        return $this->travelRequestRepository->findByIdForUser($id, $user);
    }
}
