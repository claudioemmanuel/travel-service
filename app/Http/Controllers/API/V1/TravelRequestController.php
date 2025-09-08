<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTravelRequestRequest;
use App\Http\Requests\UpdateTravelRequestStatusRequest;
use App\Http\Requests\UpdateOwnerTravelRequestRequest;
use App\Http\Resources\TravelRequestResource;
use App\Contracts\Services\TravelRequestServiceInterface;
use App\Models\TravelRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ListTravelRequestsRequest;
use App\Exceptions\TravelRequestException;

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
            'meta' => [
                'total' => $travelRequests->total(),
                'per_page' => $travelRequests->perPage(),
                'current_page' => $travelRequests->currentPage(),
                'last_page' => $travelRequests->lastPage(),
            ],
        ]);
    }

    public function updateOwner(UpdateOwnerTravelRequestRequest $request, TravelRequest $travelRequest): JsonResponse
    {
        if (!\Illuminate\Support\Facades\Gate::allows('updateOwner', $travelRequest)) {
            return response()->json([
                'message' => 'You are not authorized to update this travel request',
            ], 403);
        }

        try {
            $updated = $this->travelRequestService->update([
                'status' => $travelRequest->status, // keep status unchanged
                ...$request->validated(),
            ], $travelRequest);

            return response()->json([
                'message' => 'Travel request updated successfully',
                'data' => new TravelRequestResource($updated),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update travel request',
                'error' => $e->getMessage(),
            ], 500);
        }
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
        if (!\Illuminate\Support\Facades\Gate::allows('updateStatus', $travelRequest)) {
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
        } catch (TravelRequestException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update travel request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(TravelRequest $travelRequest): JsonResponse
    {
        if (!\Illuminate\Support\Facades\Gate::allows('view', $travelRequest)) {
            return response()->json([
                'message' => 'Travel request not found',
            ], 404);
        }
        return response()->json([
            'data' => new TravelRequestResource($travelRequest),
        ]);
    }
}
