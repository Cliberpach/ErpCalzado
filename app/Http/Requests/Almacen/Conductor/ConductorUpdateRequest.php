<?php

namespace App\Http\Requests\Almacen\Conductor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ConductorUpdateRequest extends FormRequest
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

        $rules = [
            'modalidad_transporte' => 'required|in:PRIVADO,PUBLICO',  

            'tipo_documento' => [
                'required|in:1,3,6,8',
                function ($attribute, $value, $fail) {
                    if ($this->modalidad_transporte == 'PUBLICO' && $value != 8) {
                        $fail('Si la modalidad de transporte es PUBLICO, el tipo de documento debe ser RUC.');
                    }
                }
            ],

            'nro_documento'  => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if ($this->tipo_documento == 6 && !preg_match('/^\d{8}$/', $value)) {
                        $fail('El número de documento debe tener exactamente 8 dígitos para tipo 6.');
                    } elseif ($this->tipo_documento == 8 && !preg_match('/^\d{11}$/', $value)) {
                        $fail('El número de documento debe tener exactamente 11 dígitos para tipo 8.');
                    } elseif (!in_array($this->tipo_documento, [6, 8]) && (strlen($value) < 10 || strlen($value) > 20)) {
                        $fail('El número de documento debe tener entre 10 y 20 caracteres.');
                    }
                },
                Rule::unique('conductores', 'nro_documento')
                ->ignore($this->route('id'))
                ->where(fn($query) => $query->where('estado', 'ACTIVO')),
            ],
            'nombre'            => 'required|string|max:150',
        ];
    
        // Validaciones condicionales dependiendo de modalidad_transporte
        if ($this->modalidad_transporte == 'PRIVADO') {
            $rules = array_merge($rules, [
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
                    Rule::unique('conductores', 'licencia')
                    ->ignore($this->route('id'))
                    ->where(fn($query) => $query->where('estado', 'ACTIVO')),
                ],
                'telefono'          => 'nullable|string|max:20', // Opcional, máximo 20 caracteres
            ]);
        } else {
            $rules = array_merge($rules, [
                'registro_mtc' => 'required|string|max:20|regex:/^[A-Z0-9]+$/|not_regex:/^0+$/',
            ]);
        }
    
        return $rules;

    }

    public function messages()
    {
        return [
          'modalidad_transporte.required' => 'La modalidad de transporte es obligatoria.',
            'modalidad_transporte.in'       => 'La modalidad de transporte debe ser PRIVADO o PUBLICO.',

            
            'tipo_documento.required'       => 'El tipo de documento es obligatorio.',
            'tipo_documento.in'             => 'El tipo de documento debe ser 1 (DNI), 3 (Carnet de extranjería), 6 o 8.',
            
            'nro_documento.required'        => 'El número de documento es obligatorio.',
            'nro_documento.max'             => 'El número de documento no puede exceder los 150 caracteres.',
            'nro_documento.unique'          => 'El número de documento ya está registrado en un conductor ACTIVO.',

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

            'registro_mtc.max'          => 'El campo REGISTRO MTC no puede tener más de 20 caracteres.',
            'registro_mtc.regex'        => 'El campo REGISTRO MTC solo puede contener letras mayúsculas y números, sin espacios ni símbolos.',
            'registro_mtc.not_regex'    => 'El campo REGISTRO MTC no puede estar compuesto solo por ceros.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
