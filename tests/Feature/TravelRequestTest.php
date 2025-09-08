<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\TravelRequest;

class TravelRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_can_list_travel_requests_with_filters()
    {
        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => TravelRequest::STATUS_APPROVED,
            'destination' => 'Paris',
            'departure_date' => now()->addDays(1),
            'return_date' => now()->addDays(2),
        ]);

        $response = $this->getJson('/api/v1/travel-requests?status=approved', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    }

    public function test_can_create_travel_request()
    {
        $payload = [
            'order_id' => (string) fake()->unique()->numerify('ORD########'),
            'requester_name' => fake()->name(),
            'destination' => fake()->city(),
            'departure_date' => now()->addDays(5)->toDateString(),
            'return_date' => now()->addDays(10)->toDateString(),
            'status' => 'requested',
        ];

        $response = $this->postJson('/api/v1/travel-requests', $payload, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Travel request created successfully',
            ]);

        $this->assertDatabaseHas('travel_requests', [
            'order_id' => $payload['order_id'],
            'requester_name' => $payload['requester_name'],
            'destination' => $payload['destination'],
            'status' => TravelRequest::STATUS_REQUESTED,
        ]);
    }

    public function test_can_update_travel_request()
    {
        $approver = User::factory()->approver()->create();
        $approverToken = JWTAuth::fromUser($approver);

        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => TravelRequest::STATUS_APPROVED,
        ]);

        $response = $this->patchJson('/api/v1/travel-requests/' . $travelRequest->id . '/status', [
            'status' => TravelRequest::STATUS_CANCELLED,
            'cancellation_reason' => 'Test cancellation reason',
        ], [
            'Authorization' => 'Bearer ' . $approverToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Travel request updated successfully',
            ]);

        $this->assertDatabaseHas('travel_requests', [
            'id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_CANCELLED,
            'cancellation_reason' => 'Test cancellation reason',
        ]);
    }

    public function test_cannot_update_travel_request_if_not_authorized()
    {
        $otherUser = User::factory()->create();
        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $otherUser->id,
            'status' => TravelRequest::STATUS_APPROVED,
        ]);

        $response = $this->patchJson('/api/v1/travel-requests/' . $travelRequest->id . '/status', [
            'status' => TravelRequest::STATUS_CANCELLED,
            'cancellation_reason' => 'Test cancellation reason',
        ], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You are not authorized to update this travel request',
            ]);
    }

    public function test_cannot_update_travel_request_if_status_is_not_approved()
    {
        $approver = User::factory()->approver()->create();
        $approverToken = JWTAuth::fromUser($approver);

        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => TravelRequest::STATUS_REQUESTED,
        ]);

        $response = $this->patchJson('/api/v1/travel-requests/' . $travelRequest->id . '/status', [
            'status' => TravelRequest::STATUS_CANCELLED,
            'cancellation_reason' => 'Test cancellation reason',
        ], [
            'Authorization' => 'Bearer ' . $approverToken,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Travel request cannot be cancelled: 
                - not approved yet
                - already approved without 24 hours before the departure date
                - already cancelled',
            ]);

        $this->assertDatabaseHas('travel_requests', [
            'id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_REQUESTED,
        ]);
    }

    public function test_cannot_cancel_approved_request_within_48_hours_before_departure_date()
    {
        $approver = User::factory()->approver()->create();
        $approverToken = JWTAuth::fromUser($approver);

        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => TravelRequest::STATUS_APPROVED,
            'departure_date' => now()->addDays(1),
        ]);

        $response = $this->patchJson('/api/v1/travel-requests/' . $travelRequest->id . '/status', [
            'status' => TravelRequest::STATUS_CANCELLED,
            'cancellation_reason' => 'Test cancellation reason',
        ], [
            'Authorization' => 'Bearer ' . $approverToken,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Travel request cannot be cancelled: 
                - not approved yet
                - already approved without 24 hours before the departure date
                - already cancelled',
            ]);

        $this->assertDatabaseHas('travel_requests', [
            'id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_APPROVED,
        ]);
    }

    public function test_show_endpoint_returns_request_for_owner()
    {
        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/v1/travel-requests/' . $travelRequest->id, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'order_id', 'requester_name', 'destination']]);
    }

    public function test_show_endpoint_404_for_non_owner_when_not_approver()
    {
        $otherUser = User::factory()->create();
        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->getJson('/api/v1/travel-requests/' . $travelRequest->id, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(404);
    }

    public function test_show_endpoint_allows_approver_to_view()
    {
        $approver = User::factory()->approver()->create();
        $approverToken = JWTAuth::fromUser($approver);
        $travelRequest = TravelRequest::factory()->create();

        $response = $this->getJson('/api/v1/travel-requests/' . $travelRequest->id, [
            'Authorization' => 'Bearer ' . $approverToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'order_id']]);
    }
}
