<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TravelRequest;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelRequest>
 */
class TravelRequestFactory extends Factory
{
    protected $model = TravelRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departureDate = fake()->dateTimeBetween('now', '+1 year');
        $returnDate = fake()->dateTimeBetween($departureDate, '+1 year');
        $user = User::factory()->create();

        return [
            'order_id' => (string) fake()->unique()->numerify('ORD########'),
            'requester_name' => fake()->name(),
            'destination' => fake()->city(),
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'status' => fake()->randomElement([
                TravelRequest::STATUS_REQUESTED,
                TravelRequest::STATUS_APPROVED,
                TravelRequest::STATUS_CANCELLED
            ]),
            'user_id' => $user->id,
        ];
    }

    public function requested(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TravelRequest::STATUS_REQUESTED,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TravelRequest::STATUS_APPROVED,
            'approved_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TravelRequest::STATUS_CANCELLED,
            'cancelled_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'cancellation_reason' => fake()->sentence(),
        ]);
    }
}
