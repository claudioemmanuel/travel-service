<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTravelRequestRequest extends FormRequest
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
            'order_id' => 'required|string|unique:travel_requests,order_id',
            'requester_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date|after:today',
            'return_date' => 'required|date|after:departure_date',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'The order ID is required.',
            'order_id.unique' => 'The order ID must be unique.',
            'requester_name.required' => 'The requester name is required.',
            'requester_name.string' => 'The requester name must be a string.',
            'requester_name.max' => 'The requester name must be less than 255 characters.',
            'destination.required' => 'The destination is required.',
            'destination.string' => 'The destination must be a string.',
            'destination.max' => 'The destination must be less than 255 characters.',
            'departure_date.required' => 'The departure date is required.',
            'departure_date.date' => 'The departure date must be a date.',
            'departure_date.after' => 'The departure date must be after today.',
            'return_date.required' => 'The return date is required.',
            'return_date.date' => 'The return date must be a date.',
            'return_date.after' => 'The return date must be after the departure date.',
        ];
    }
}
