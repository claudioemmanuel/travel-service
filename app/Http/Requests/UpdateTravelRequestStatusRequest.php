<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTravelRequestStatusRequest extends FormRequest
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
            'status' => 'required|string|in:approved,cancelled',
            'cancellation_reason' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'The status is required.',
            'status.string' => 'The status must be a string.',
            'status.in' => 'The status must be either approved or cancelled.',
            'cancellation_reason.string' => 'The cancellation reason must be a string.',
            'cancellation_reason.max' => 'The cancellation reason must be less than 255 characters.',
        ];
    }
}
