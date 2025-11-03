<?php

namespace App\Http\Requests\Ventas\DocVenta;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

class DocVentaUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules  =   [
            'tipo_venta' => [
                'required',
                Rule::exists('tabladetalles', 'id')->where('estado', 'ACTIVO'),
            ],
            'almacen' => [
                'required',
                Rule::exists('almacenes', 'id')->where('estado', 'ACTIVO'),
            ],
            'condicion_id' => [
                'required',
                Rule::exists('condicions', 'id')->where('estado', 'ACTIVO'),
            ],
            'cliente' => [
                'required',
                Rule::exists('clientes', 'id')->where('estado', 'ACTIVO'),
            ],
            'observacion' => [
                'nullable',
                'string',
                'max:200',
            ],

            //======== PARÁMETROS DE PAGO ==========
            'metodo_pago_1' => [
                'nullable',
                Rule::exists('tipos_pago', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],

            'img_pago_1' => 'nullable|mimes:png,jpg,jpeg|max:4096',

        ];

        // Si es EFECTIVO (1) Y monto y fecha tienen valor
        if ($this->metodo_pago_1 == 1 && $this->filled('monto_1') && $this->filled('fecha_operacion_1')) {
            $rules['monto_1'] = 'numeric|gt:0';
            $rules['fecha_operacion_1'] = 'date';
        }

        // Si es distinto de 1 Y además tiene valores en cuenta, monto, nroOperacion y fecha
        if (
            $this->metodo_pago_1 &&
            $this->metodo_pago_1 != 1 &&
            $this->filled('cuenta_1') &&
            $this->filled('monto_1') &&
            $this->filled('nro_operacion_1') &&
            $this->filled('fecha_operacion_1')
        ) {
            $rules['cuenta_1'] = [
                Rule::exists('cuentas', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
                function ($attribute, $value, $fail) {
                    $exists = DB::table('tipo_pago_cuentas')
                        ->where('tipo_pago_id', $this->metodo_pago_1)
                        ->where('cuenta_id', $value)
                        ->exists();

                    if (!$exists) {
                        $fail('La cuenta seleccionada no está asociada al método de pago.');
                    }
                }
            ];

            $rules['monto_1'] = 'numeric|gt:0';
            $rules['nro_operacion_1'] = [
                'max:20',
                Rule::unique('cotizacion_documento', 'pago_1_nro_operacion')
                    ->where(function ($query) {
                        $query->where('sunat', '<>', '2');
                    })->ignore($this->route('id'))
            ];
            $rules['fecha_operacion_1'] = 'date';
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tipo_venta.required'           => 'El campo tipo de venta es obligatorio.',
            'tipo_venta.exists'             => 'El tipo de venta seleccionado no es válido o no está activo.',

            'almacen.required'  => 'El campo almacén seleccionado es obligatorio.',
            'almacen.exists'    => 'El almacén seleccionado no es válido o no está activo.',

            'condicion_id.required'         => 'El campo condición es obligatorio.',
            'condicion_id.exists'           => 'La condición seleccionada no es válida o no está activa.',

            'cliente.required'           => 'El campo cliente es obligatorio.',
            'cliente.exists'             => 'El cliente seleccionado no es válido o no está activo.',

            'observacion.string'    => 'La observación debe ser un texto.',
            'observacion.max'       => 'La observación no puede tener más de 200 caracteres.',

            // Mensajes de pago
            'metodo_pago_1.exists'   => 'El método de pago seleccionado no es válido o no está activo.',

            'cuenta_1.exists'   => 'La cuenta de pago seleccionada no es válida o no está activa.',

            'monto_1.numeric'  => 'El monto de pago debe ser un número.',
            'monto_1.gt'       => 'El monto de pago debe ser mayor a 0.',

            'nro_operacion_1.max'      => 'El número de operación no puede exceder los :max caracteres.',
            'nro_operacion_1.unique'   => 'El número de operación ya ha sido registrado en otra venta.',

            'img_pago_1.mimes' => 'La imagen de pago debe ser un archivo de tipo: png, jpg o jpeg.',
            'img_pago_1.max'   => 'La imagen de pago no debe superar los 4 MB.',

            'fecha_operacion_1.date'     => 'La fecha de la operación debe ser una fecha válida.',

        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
