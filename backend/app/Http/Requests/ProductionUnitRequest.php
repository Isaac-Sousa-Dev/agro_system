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
            'property_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'crop_name.required' => 'O nome da cultura é obrigatório',
            'total_area_ha.required' => 'A área total é obrigatória',
            'geographic_coordinates.required' => 'As coordenadas geográficas são obrigatórias',
            'property_id.required' => 'A propriedade é obrigatória',
        ];
    }
}
