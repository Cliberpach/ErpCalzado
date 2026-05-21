<?php

namespace App\Http\Requests\Ventas\DocVenta;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class StorePagoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'venta_id'    => 'required|integer|exists:cotizacion_documento,id',
            'monto_venta' => 'required|numeric',
            'lstPagos'    => 'required|string',
            'hora_pago'   => 'required|date_format:H:i',
        ];
    }

    public function messages()
    {
        return [
            'venta_id.required'    => 'El id de la venta es obligatorio.',
            'venta_id.exists'      => 'El documento de venta no existe.',
            'monto_venta.required' => 'El monto total es obligatorio.',
            'lstPagos.required'    => 'Debe enviar al menos un pago.',
            'hora_pago.required'   => 'La hora de pago es obligatoria.',
            'hora_pago.date_format' => 'La hora de pago debe tener el formato HH:MM (ej. 14:30).',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
