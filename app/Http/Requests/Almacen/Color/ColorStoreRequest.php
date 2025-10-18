<?php

namespace App\Http\Requests\Almacen\Color;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ColorStoreRequest extends FormRequest
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
            'descripcion' => [
                'required',
                'string',
                'max:191',
                Rule::unique('colores')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                }),
            ],
            /*'codigo' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('colores')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                }),
            ],*/
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'El campo "descripción" es obligatorio.',
            'descripcion.string'   => 'El campo "descripción" debe ser una cadena de texto.',
            'descripcion.max'      => 'El campo "descripción" no debe exceder los 191 caracteres.',
            'descripcion.unique'   => 'Ya existe un color con esta descripción en estado ACTIVO.',

            'codigo.string'        => 'El campo "código" debe ser una cadena de texto.',
            'codigo.max'           => 'El campo "código" no debe exceder los 20 caracteres.',
            'codigo.unique'        => 'Ya existe un color con este código en estado ACTIVO.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
