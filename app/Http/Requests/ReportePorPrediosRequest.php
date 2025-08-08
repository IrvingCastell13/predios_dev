<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportePorPrediosRequest extends FormRequest
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
            // El campo 'predio_ids' es obligatorio y debe ser un array.
            'predio_ids' => 'required|array',
            // Cada elemento ('*') dentro del array 'predio_ids' debe ser un string.
            'predio_ids.*' => 'string',
        ];
    }
}
