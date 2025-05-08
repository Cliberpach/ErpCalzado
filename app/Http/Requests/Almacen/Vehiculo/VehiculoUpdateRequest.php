<?php

namespace App\Http\Requests\Almacen\Vehiculo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class VehiculoUpdateRequest extends FormRequest
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
        $vehiculoId = $this->route('id');

        return [
           'placa'     => [
                'required',
                'string',
                'min:6',
                'max:8',
                'regex:/^[A-Za-z0-9]+$/', 
                function($attribute, $value, $fail) {
                    if (preg_match('/^0+$/', $value)) {
                        $fail('La placa no puede contener solo ceros.');
                    }
                },
                Rule::unique('vehiculos')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO'); 
                })
            ],
            'modelo'    => 'required|string|max:100',
            'marca'     => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'placa.regex'           => 'La placa solo puede contener letras y números; sin espacios,guiones,etc.',
            'placa.unique'          => 'Esta placa ya está registrada para un vehículo activo.',
            'placa.min'             => 'La placa debe tener al menos 6 caracteres.',
            'placa.max'             => 'La placa no puede tener más de 8 caracteres.',
            'placa.required'        => 'El campo de la placa es obligatorio.',
            'placa.custom_ceros'    => 'La placa no puede contener solo ceros.',

            
            'modelo.required'   => 'El modelo es obligatorio.',
            'modelo.string'     => 'El modelo debe ser una cadena de texto.',
            'modelo.max'        => 'El modelo no debe exceder 100 caracteres.',
            
            'marca.required'    => 'La marca es obligatoria.',
            'marca.string'      => 'La marca debe ser una cadena de texto.',
            'marca.max'         => 'La marca no debe exceder 100 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
