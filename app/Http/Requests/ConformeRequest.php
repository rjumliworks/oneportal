<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConformeRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|size:11|regex:/^[0-9]+$/',
            'position' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'contact_no.required' => 'The contact number is required.',
            'contact_no.size' => 'The contact number must be exactly 11 digits.',
            'contact_no.regex' => 'The contact number must contain only numbers.',
            'position.required' => 'The position field is required.',
        ];
    }
}
