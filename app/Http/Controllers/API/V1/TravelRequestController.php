<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTravelRequestRequest;
use App\Http\Requests\UpdateTravelRequestStatusRequest;
use App\Http\Resources\TravelRequestResource;
use App\Contracts\Services\TravelRequestServiceInterface;
use App\Models\TravelRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ListTravelRequestsRequest;

class TravelRequestController extends Controller
{
    public function __construct(
        private TravelRequestServiceInterface $travelRequestService
    ) {}

    public function index(ListTravelRequestsRequest $request): JsonResponse
    {
        $filters = $request->validated();

        $travelRequests = $this->travelRequestService->getTravelRequestsForUser(
            $filters,
            $request->user()
        );

        return response()->json([
            'data' => TravelRequestResource::collection($travelRequests),
        ]);
    }

    public function store(CreateTravelRequestRequest $request): JsonResponse
    {
        try {
            $travelRequest = $this->travelRequestService->create(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'message' => 'Travel request created successfully',
                'data' => new TravelRequestResource($travelRequest),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create travel request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateTravelRequestStatusRequest $request, TravelRequest $travelRequest): JsonResponse
    {
        if ($travelRequest->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to update this travel request',
            ], 403);
        }

        try {
            $updatedTravelRequest = $this->travelRequestService->update(
                $request->validated(),
                $travelRequest
            );
            return response()->json([
                'message' => 'Travel request updated successfully',
                'data' => new TravelRequestResource($updatedTravelRequest),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update travel request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
