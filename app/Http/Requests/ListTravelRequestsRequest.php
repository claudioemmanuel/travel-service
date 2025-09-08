<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListTravelRequestsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'nullable|in:requested,approved,cancelled',
            'destination' => 'nullable|string',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date|after_or_equal:departure_date',
            'order_id' => 'nullable|string',
            'requester_name' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'The status must be either requested, approved, or cancelled.',
            'destination.string' => 'The destination must be a string.',
            'departure_date.date' => 'The departure date must be a date.',
            'return_date.date' => 'The return date must be a date.',
            'return_date.after_or_equal' => 'The return date must be after or equal to the departure date.',
            'order_id.string' => 'The order ID must be a string.',
            'requester_name.string' => 'The requester name must be a string.',
        ];
    }
}
