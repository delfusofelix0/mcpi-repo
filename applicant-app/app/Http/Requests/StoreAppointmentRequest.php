<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust this if you need authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'regex:/^(09|\+639|639)\d{9}$/', 'max:13'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'string', 'regex:/^([1-9]|1[0-2]):00 (AM|PM) - ([1-9]|1[0-2]):00 (AM|PM)$/'],
            'office_id' => ['required', 'exists:offices,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'name.max' => 'Name must not exceed 255 characters.',
            'contact.required' => 'Please provide a contact email or phone number.',
            'contact.max' => 'Contact information must not exceed 255 characters.',
            'date.required' => 'Please select an appointment date.',
            'date.date' => 'The appointment date must be a valid date.',
            'date.after_or_equal' => 'The appointment date must be today or a future date.',
            'time.required' => 'Please select an appointment time.',
            'time.regex' => 'The appointment time must be in the format "HH:00 AM/PM - HH:00 AM/PM".',
            'office_id.required' => 'Please select an office.',
            'office_id.exists' => 'The selected office is invalid.',
        ];
    }
}
