<?php

namespace App\Http\Requests\Cuentas\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CuentaClienteComprobanteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cliente' => [
                'required',
                Rule::exists('clientes', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],

            'tipo_comprobante' => [
                'required',
                Rule::exists('tabladetalles', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],

            'observacion' => ['nullable', 'string', 'max:200'],

        ];
    }

    public function messages()
    {
        return [
            'cliente.required' => 'El campo cliente es obligatorio.',
            'cliente.exists' => 'El cliente seleccionado no existe o no está activo.',

            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio.',
            'tipo_comprobante.exists' => 'El tipo de comprobante seleccionado no existe o no está activo.',

            'observacion.string' => 'La observación debe ser un texto válido.',
            'observacion.max' => 'La observación no debe exceder los 200 caracteres.',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación al pagar cuenta cliente.',
            'errors' => $validator->errors()
        ], 422));
    }
}
