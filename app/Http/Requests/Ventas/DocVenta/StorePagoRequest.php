<?php

namespace App\Http\Requests\Ventas\DocVenta;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class StorePagoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $request = $this; 

        return [
            'tipo_pago_id'  => [
                'required',
                'integer',
                Rule::exists('tipos_pago', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],
            'efectivo'      => 'required|numeric|min:0',
            'importe'       => 'required|numeric|min:0',
            'cuenta_id'     => [
                'nullable',
                'integer',
                Rule::requiredIf(function () use ($request) {
                    return $request->tipo_pago_id != 1;
                }),
                Rule::exists('cuentas', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],
            'nro_operacion' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return $request->tipo_pago_id != 1;
                }),
                'string',
            ],
            'fecha_pago' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return $request->tipo_pago_id != 1;
                }),
                'date',
            ],
            'hora_pago' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return $request->tipo_pago_id != 1;
                }),
                'date_format:H:i',
            ],
        ];
    }

    public function messages()
    {
        return [
            'tipo_pago_id.required'    => 'El campo modo de pago es obligatorio.',
            'tipo_pago_id.exists'      => 'El modo de pago no es válido o está inactivo.',
            'importe.required'         => 'El campo importe es obligatorio.',
            'importe.numeric'          => 'El importe debe ser un número.',
            'efectivo.required'        => 'El campo efectivo es obligatorio.',
            'efectivo.numeric'         => 'El efectivo debe ser un número.',
            'cuenta_id.required'       => 'Debe seleccionar una cuenta si el modo de pago no es EFECTIVO.',
            'cuenta_id.exists'         => 'La cuenta seleccionada no es válida o está inactiva.',
            'nro_operacion.required'   => 'Debe ingresar el número de operación si el modo de pago no es EFECTIVO.',
            'fecha_pago.required'      => 'Debe ingresar la fecha de pago si el modo de pago no es EFECTIVO.',
            'fecha_pago.date'          => 'La fecha de pago no tiene un formato válido.',
            'hora_pago.required'       => 'Debe ingresar la hora de pago si el modo de pago no es EFECTIVO.',
            'hora_pago.date_format'    => 'La hora de pago debe tener el formato HH:MM (ej. 14:30).',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
