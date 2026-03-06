<?php

namespace App\Http\Requests\Ventas\TipoCliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class TipoClienteUpdateRequest extends FormRequest
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
            'nombre_edit' => [
                'required',
                'string',
                Rule::unique('tipos_clientes', 'nombre')
                    ->ignore($this->route('id'))
                    ->where(function ($query) {
                        $query->where('estado', '!=', 'ANULADO');
                    }),
            ],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre_edit.required' => 'El campo nombre es obligatorio.',
            'nombre_edit.string' => 'El campo nombre debe ser una cadena de texto.',
            'nombre_edit.unique' => 'El nombre ya existe',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
