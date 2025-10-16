<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductionUnitRequest extends FormRequest
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
            'crop_name' => 'required|string|max:255',
            'total_area_ha' => 'required|numeric|min:0',
            'geographic_coordinates' => 'required|string|max:255',
            'property_id' => 'required|exists:properties,id',
        ];
    }

    public function messages(): array
    {
        return [
            'crop_name.required' => 'The crop name is required',
            'crop_name.string' => 'The crop name must be a string',
            'crop_name.max' => 'The crop name must be less than 255 characters',
            'total_area_ha.required' => 'The total area is required',
        ];
    }
}
