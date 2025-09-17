<?php

namespace App\Http\Requests\Mantenimiento\Cuentas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class CuentaStoreRequest extends FormRequest
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
            'titular' => [
                'required',
                'string',
                'max:200',
            ],
            'banco_id' => [
                'required',
                Rule::exists('tabladetalles', 'id')->where('estado', 'ACTIVO'),
            ],
            'moneda' => [
                'required',
                'in:SOLES,DOLARES',
            ],
            'nro_cuenta' => [
                'required',
                'string',
                'max:100',
                Rule::unique('cuentas', 'nro_cuenta')->where(
                    fn($query) =>
                    $query->where('estado', 'ACTIVO')
                ),
            ],
            'cci' => [
                'required',
                'string',
                'max:100',
                Rule::unique('cuentas', 'cci')->where(
                    fn($query) =>
                    $query->where('estado', 'ACTIVO')
                ),
            ],
            'celular' => [
                'nullable',
                'regex:/^\d{1,20}$/',
            ],
        ];
    }

    public function messages()
    {
        return [
            'banco_id.required'     => 'El banco es obligatorio.',
            'banco_id.exists'       => 'El banco seleccionado no es válido o no está activo.',

            'moneda.required'       => 'La moneda es obligatoria.',
            'moneda.in'             => 'La moneda debe ser SOLES o DÓLARES.',

            'nro_cuenta.required'   => 'El número de cuenta es obligatorio.',
            'nro_cuenta.max'        => 'El número de cuenta no puede superar los 100 caracteres.',
            'nro_cuenta.unique'     => 'El número de cuenta ya está registrado en una cuenta activa.',

            'cci.required'          => 'El CCI es obligatorio.',
            'cci.max'               => 'El CCI no puede superar los 100 caracteres.',
            'cci.unique'            => 'El CCI ya está registrado en una cuenta activa.',

            'celular.regex' => 'El celular debe contener solo números y tener un máximo de 20 dígitos.',

            'cuenta_contable.max'               => 'La cuenta contable no puede superar los 20 caracteres.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'nro_cuenta'    => preg_replace('/\s+/', '', $this->nro_cuenta),
            'cci'           => preg_replace('/\s+/', '', $this->cci),
        ]);
    }


    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
