<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
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
            'municipality' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'state_registration' => 'nullable|string|max:255',
            'total_area' => 'required|numeric|min:0',
            'farmer_id' => 'required',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'municipality.required' => 'O município é obrigatório',
            'state.required' => 'A UF é obrigatória',
            'state_registration.required' => 'O registro estadual é obrigatório',
            'total_area.required' => 'A área total é obrigatória',
            'farmer_id.required' => 'O produtor é obrigatório',
        ];
    }
}
