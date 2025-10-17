<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HerdRequest extends FormRequest
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
            'species' => 'required|string|max:255',
            'quantity' => 'required',
            'property_id' => 'required',
            'purpose' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'species.required' => 'A espécie é obrigatória',
            'quantity.required' => 'A quantidade é obrigatória',
            'property_id.required' => 'A propriedade é obrigatória',
            'purpose.required' => 'A finalidade é obrigatória',
        ];
    }
}
