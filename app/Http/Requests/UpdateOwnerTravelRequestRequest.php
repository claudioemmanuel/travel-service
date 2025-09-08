<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOwnerTravelRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requester_name' => 'sometimes|required|string|max:255',
            'destination' => 'sometimes|required|string|max:255',
            'departure_date' => 'sometimes|required|date|after:today',
            'return_date' => 'sometimes|required|date|after:departure_date',
        ];
    }
}
