<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FarmerRequest extends FormRequest
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
        if($this->isMethod('post')){
            return [
                'name' => 'required|string|max:255',
                'cpf_cnpj' => 'required|string|unique:farmers,cpf_cnpj',
                'phone' => 'nullable|string|max:20|unique:farmers,phone',
                'email' => 'nullable|email|max:255|unique:farmers,email',
                'address' => 'nullable|string'
            ];
        } else {
            return [
                'name' => 'required|string|max:255',
                'cpf_cnpj' => 'required|string|unique:farmers,cpf_cnpj,' . $this->route('farmer')->id,
                'phone' => 'nullable|string|max:20|unique:farmers,phone,' . $this->route('farmer')->id,
                'email' => 'nullable|email|max:255|unique:farmers,email,' . $this->route('farmer')->id,
                'address' => 'nullable|string'
            ];
        }

    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'cpf_cnpj.required' => 'O CPF/CNPJ é obrigatório',
            'cpf_cnpj.unique' => 'O CPF/CNPJ já existe',
            'phone.unique' => 'O telefone já existe',
            'email.unique' => 'O email já existe',
        ];
    }

}
