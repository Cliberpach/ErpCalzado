<?php

namespace App\Http\Requests\Ventas\DocVenta;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

class DocVentaStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'tipo_venta' => [
                'required',
                Rule::exists('tabladetalles', 'id')->where('estado', 'ACTIVO'),
            ],
            'almacenSeleccionado' => [
                'required',
                Rule::exists('almacenes', 'id')->where('estado', 'ACTIVO'),
            ],
            'condicion_id' => [
                'required',
                Rule::exists('condicions', 'id')->where('estado', 'ACTIVO'),
            ],
            'cliente_id' => [
                'required',
                Rule::exists('clientes', 'id')->where('estado', 'ACTIVO'),
            ],
            'telefono' => [
                'nullable',
                'regex:/^[0-9]+$/',
                'min:9',
                'max:20',
            ],

            'metodoPagoId' => [
                'nullable',
                Rule::exists('tipos_pago', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],

            'imgPago' => 'nullable|mimes:png,jpg,jpeg|max:4096',
        ];

        // Si es EFECTIVO (1) Y monto y fecha tienen valor
        if ($this->metodoPagoId == 1 && $this->filled('montoPago') && $this->filled('fechaOperacionPago')) {
            $rules['montoPago'] = 'numeric|gt:0';
            $rules['fechaOperacionPago'] = 'date';
        }

        // Si es distinto de 1 Y además tiene valores en cuenta, monto, nroOperacion y fecha
        if (
            $this->metodoPagoId &&
            $this->metodoPagoId != 1 &&
            $this->filled('cuentaPagoId') &&
            $this->filled('montoPago') &&
            $this->filled('nroOperacionPago') &&
            $this->filled('fechaOperacionPago')
        ) {
            $rules['cuentaPagoId'] = [
                Rule::exists('cuentas', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
                function ($attribute, $value, $fail) {
                    $exists = DB::table('tipo_pago_cuentas')
                        ->where('tipo_pago_id', $this->metodoPagoId)
                        ->where('cuenta_id', $value)
                        ->exists();

                    if (!$exists) {
                        $fail('La cuenta seleccionada no está asociada al método de pago.');
                    }
                }
            ];

            $rules['montoPago'] = 'numeric|gt:0';
            $rules['nroOperacionPago'] = [
                'max:20',
                Rule::unique('cotizacion_documento', 'pago_1_nro_operacion')
                    ->where(function ($query) {
                        $query->where('sunat', '<>', '2');
                    }),
            ];
            $rules['fechaOperacionPago'] = 'date';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'tipo_venta.required'              => 'El campo tipo de venta es obligatorio.',
            'tipo_venta.exists'                => 'El tipo de venta seleccionado no es válido o no está activo.',

            'almacenSeleccionado.required'     => 'El campo almacén seleccionado es obligatorio.',
            'almacenSeleccionado.exists'       => 'El almacén seleccionado no es válido o no está activo.',

            'condicion_id.required'            => 'El campo condición es obligatorio.',
            'condicion_id.exists'              => 'La condición seleccionada no es válida o no está activa.',

            'cliente_id.required'              => 'El campo cliente es obligatorio.',
            'cliente_id.exists'                => 'El cliente seleccionado no es válido o no está activo.',

            'telefono.regex'                   => 'El teléfono solo puede contener números.',
            'telefono.min'                     => 'El teléfono debe tener al menos :min dígitos.',
            'telefono.max'                     => 'El teléfono no puede tener más de :max dígitos.',

            // Mensajes de pago
            'metodoPagoId.exists'   => 'El método de pago seleccionado no es válido o no está activo.',

            'cuentaPagoId.exists'   => 'La cuenta de pago seleccionada no es válida o no está activa.',

            'montoPago.numeric'  => 'El monto de pago debe ser un número.',
            'montoPago.gt'       => 'El monto de pago debe ser mayor a 0.',

            'nroOperacionPago.max'      => 'El número de operación no puede exceder los :max caracteres.',
            'nroOperacionPago.unique'   => 'El número de operación ya ha sido registrado en otra venta.',

            'imgPago.mimes' => 'La imagen de pago debe ser un archivo de tipo: png, jpg o jpeg.',
            'imgPago.max'   => 'La imagen de pago no debe superar los 4 MB.',

            'fechaOperacionPago.date'     => 'La fecha de la operación debe ser una fecha válida.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
