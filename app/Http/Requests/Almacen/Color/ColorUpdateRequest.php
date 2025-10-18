<?php

namespace App\Http\Requests\Almacen\Color;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ColorUpdateRequest extends FormRequest
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
            'descripcion_edit' => [
                'required',
                'string',
                'max:191',
                Rule::unique('colores', 'descripcion')
                    ->ignore($this->route('id')) // Ignora el registro actual
                    ->where(function ($query) {
                        return $query->where('estado', 'ACTIVO');
                    }),
            ],
            /*'codigo_edit' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('colores', 'codigo')
                    ->ignore($this->route('id')) // Ignora el registro actual
                    ->where(function ($query) {
                        return $query->where('estado', 'ACTIVO');
                    }),
            ],*/
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion_edit.required' => 'El campo "descripción" es obligatorio.',
            'descripcion_edit.string'   => 'El campo "descripción" debe ser una cadena de texto.',
            'descripcion_edit.max'      => 'El campo "descripción" no debe exceder los 191 caracteres.',
            'descripcion_edit.unique'   => 'Ya existe un color con esta descripción en estado ACTIVO.',

            'codigo_edit.string'        => 'El campo "código" debe ser una cadena de texto.',
            'codigo_edit.max'           => 'El campo "código" no debe exceder los 20 caracteres.',
            'codigo_edit.unique'        => 'Ya existe un color con este código en estado ACTIVO.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
