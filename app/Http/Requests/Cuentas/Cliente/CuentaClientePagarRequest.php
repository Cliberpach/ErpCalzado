<?php

namespace App\Http\Requests\Cuentas\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CuentaClientePagarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'nro_operacion' => $this->nro_operacion ? trim($this->nro_operacion) : null,
        ]);
    }

    public function rules()
    {
        return [
            'pago' => [
                'required',
                Rule::in(['TODO', 'A CUENTA']),
            ],
            'fecha' => [
                'required',
                'date',
            ],
            'cantidad' => [
                'required',
                'numeric',
                'gt:0',
                function ($attribute, $value, $fail) {
                    $importeVenta = floatval($this->input('importe_venta', 0));
                    $efectivoVenta = floatval($this->input('efectivo_venta', 0));
                    if (round($value, 2) !== round($importeVenta + $efectivoVenta, 2)) {
                        $fail('La cantidad debe ser igual a la suma de importe_venta + efectivo_venta.');
                    }
                }
            ],
            'observacion' => [
                'nullable',
                'string',
                'max:200',
            ],
            'efectivo_venta' => [
                'nullable',
                'numeric',
                'gte:0',
            ],
            'importe_venta' => [
                'nullable',
                'numeric',
                'gte:0',
            ],
            'modo_pago' => [
                'required',
                Rule::exists('tipos_pago', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],
            'cuenta' => array_merge(
                $this->input('modo_pago') != 1 ? ['required'] : ['nullable'],
                $this->input('modo_pago') != 1
                    ? [
                        Rule::exists('tipo_pago_cuentas', 'cuenta_id')->where(function ($query) {
                            $query->where('tipo_pago_id', $this->input('modo_pago'));
                        }),
                    ]
                    : []
            ),
            'nro_operacion' => [
                Rule::requiredIf(fn() => $this->input('modo_pago') != 1),
                'nullable',
                'string',
                'max:20',
            ],
            'url_imagen' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }

    public function messages()
    {
        return [
            'pago.required' => 'El campo pago es obligatorio.',
            'pago.in' => 'El pago debe ser TODO o A CUENTA.',

            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no tiene un formato válido.',

            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.numeric' => 'La cantidad debe ser numérica.',
            'cantidad.gt' => 'La cantidad debe ser mayor a 0.',

            'observacion.max' => 'La observación no debe exceder los 200 caracteres.',

            'efectivo_venta.numeric' => 'El efectivo de venta debe ser numérico.',
            'efectivo_venta.gte' => 'El efectivo de venta debe ser mayor o igual a 0.',

            'importe_venta.numeric' => 'El importe de venta debe ser numérico.',
            'importe_venta.gte' => 'El importe de venta debe ser mayor o igual a 0.',

            'modo_pago.required' => 'El modo de pago es obligatorio.',
            'modo_pago.exists' => 'El modo de pago seleccionado no es válido o no está activo.',

            'cuenta.required' => 'La cuenta es obligatoria.',
            'cuenta.exists' => 'La cuenta seleccionada no es válida para el modo de pago elegido.',

            'nro_operacion.required' => 'El número de operación es obligatorio para este modo de pago.',
            'nro_operacion.max' => 'El número de operación no debe exceder los 20 caracteres.',

            'url_imagen.file' => 'La imagen debe ser un archivo.',
            'url_imagen.mimes' => 'La imagen debe ser formato JPG, JPEG o PNG.',
            'url_imagen.max' => 'La imagen no debe superar los 2MB.',
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
