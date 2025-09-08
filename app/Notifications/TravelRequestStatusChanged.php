<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelRequestStatusChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private TravelRequest $travelRequest
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("Travel Request {$this->travelRequest->status} - {$this->travelRequest->order_id}")
                    ->greeting("Hello {$notifiable->name}!")
                    ->line("Your travel request has been {$this->travelRequest->status}.")
                    ->line("Order ID: {$this->travelRequest->order_id}")
                    ->line("Destination: {$this->travelRequest->destination}")
                    ->line("Departure: {$this->travelRequest->departure_date->format('M d, Y')}")
                    ->when($this->travelRequest->cancellation_reason, function ($message) {
                        return $message->line("Reason: {$this->travelRequest->cancellation_reason}");
                    });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
