<?php

namespace App\Http\Requests\Almacen\Conductor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ConductorStoreRequest extends FormRequest
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
    public function rules()
    {
        return [
            'tipo_documento'    => 'required|in:1,3', // Solo puede ser 1 o 3
            'nro_documento'     => [
                'required',
                'string',
                Rule::when($this->tipo_documento == 1, 'digits:8'), // Si tipo_documento es 1, debe ser exactamente 8 dígitos
                Rule::when($this->tipo_documento == 3, 'string|min:10|max:20'), // Si tipo_documento es 3, entre 10 y 20 caracteres
            ],
            'nombre'            => 'required|string|max:150',
            'apellido'          => 'required|string|max:150',
            'licencia'          => [
                'required',
                'string',
                'min:9',
                'max:10',
                'regex:/^[A-Za-z0-9]+$/', // Solo letras y números
                function($attribute, $value, $fail) {
                    if (preg_match('/^0+$/', $value)) {
                        $fail('La licencia no puede contener solo ceros.');
                    }
                },
                Rule::unique('conductores')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                }),
            ],
            'telefono'          => 'nullable|string|max:20', // Opcional, máximo 20 caracteres
        ];
    }

    public function messages()
    {
        return [
            'tipo_documento.required'       => 'El tipo de documento es obligatorio.',
            'tipo_documento.in'             => 'El tipo de documento debe ser 1 (DNI) o 3 (CARNET DE EXTRANJERÍA).',
            
            'nro_documento.required'        => 'El número de documento es obligatorio.',
            'nro_documento.max'             => 'El número de documento no puede exceder los 150 caracteres.',
            
            'nombre.required'               => 'El nombre es obligatorio.',
            'nombre.max'                    => 'El nombre no puede exceder los 150 caracteres.',
            
            'apellido.required'             => 'El apellido es obligatorio.',
            'apellido.max'                  => 'El apellido no puede exceder los 150 caracteres.',
            
            'licencia.regex'                 => 'La licencia solo puede contener letras y números, sin espacios ni caracteres especiales.',
            'licencia.required'             => 'La licencia es obligatoria.',
            'licencia.min'                  => 'La licencia debe tener al menos 9 caracteres.',
            'licencia.max'                  => 'La licencia no puede exceder los 10 caracteres.',
            'licencia.unique'               => 'La licencia ya está registrada y debe ser única.',

            'telefono.max'                  => 'El teléfono no puede exceder los 20 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
