<?php

namespace App\Http\Requests\Mantenimiento\Cuentas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class CuentaUpdateRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $sanitized = [];
        foreach ($this->all() as $key => $value) {
            $newKey = str_ends_with($key, '_edit') ? substr($key, 0, -5) : $key;
            $sanitized[$newKey] = $value;
        }

        if (isset($sanitized['nro_cuenta'])) {
            $sanitized['nro_cuenta'] = preg_replace('/\s+/', '', $sanitized['nro_cuenta']);
        }
        if (isset($sanitized['cci'])) {
            $sanitized['cci'] = preg_replace('/\s+/', '', $sanitized['cci']);
        }

        $this->merge($sanitized);
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
                Rule::unique('cuentas', 'nro_cuenta')
                    ->where(fn($query) => $query->where('estado', 'ACTIVO'))
                    ->ignore($this->route('id')),
            ],
            'cci' => [
                'required',
                'string',
                'max:100',
                Rule::unique('cuentas', 'cci')
                    ->where(fn($query) => $query->where('estado', 'ACTIVO'))
                    ->ignore($this->route('id')),
            ],
            'celular' => [
                'nullable',
                'regex:/^\d{1,20}$/',
            ]
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

            'itf.numeric'           => 'El ITF debe ser un número.',
            'itf.regex'             => 'El ITF debe tener hasta 16 dígitos en total y hasta 2 decimales.',

            'cuenta_contable.max'               => 'La cuenta contable no puede superar los 20 caracteres.',
            'celular.regex' => 'El celular debe contener solo números y tener un máximo de 20 dígitos.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
