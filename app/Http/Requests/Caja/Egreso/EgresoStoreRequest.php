<?php

namespace App\Http\Requests\Caja\Egreso;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class EgresoStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cuenta' => [
                'required',
                Rule::exists('tabladetalles', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],

            'tipo_documento' => ['required', 'string'],

            'monto' => ['required', 'numeric', 'gt:0'],

            'documento' => ['required', 'string', 'max:191'],

            'descripcion' => ['nullable', 'string', 'max:200'],

            'efectivo' => ['nullable', 'numeric', 'gte:0'],

            'modo_pago' => [
                'required',
                Rule::exists('tipos_pago', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],

            'importe' => [ 'numeric', 'gte:0'],

            'cuenta_bancaria' => [
                Rule::requiredIf(fn () => $this->modo_pago != 1),
                'nullable',
                'integer',
            ],

            'nro_operacion' => [
                Rule::requiredIf(fn () => $this->modo_pago != 1),
                'nullable',
                'string',
                'max:20',
            ],

            'fecha_operacion' => ['required', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'cuenta.required' => 'La cuenta es obligatoria.',
            'cuenta.exists' => 'La cuenta seleccionada no es válida o no está activa.',

            'tipo_documento.required' => 'El tipo de documento es obligatorio.',

            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser numérico.',
            'monto.gt' => 'El monto debe ser mayor a 0.',

            'documento.required' => 'El documento es obligatorio.',
            'documento.max' => 'El documento no puede exceder 191 caracteres.',

            'descripcion.max' => 'La descripción no puede exceder 200 caracteres.',

            'efectivo.numeric' => 'El efectivo debe ser numérico.',
            'efectivo.gte' => 'El efectivo debe ser mayor o igual a 0.',

            'modo_pago.required' => 'El modo de pago es obligatorio.',
            'modo_pago.exists' => 'El modo de pago seleccionado no es válido o no está activo.',

            'importe.numeric' => 'El importe debe ser numérico.',
            'importe.gte' => 'El importe debe ser mayor o igual a 0.',

            'cuenta_bancaria.required' => 'La cuenta bancaria es obligatoria cuando el modo de pago no es efectivo.',
            'cuenta_bancaria.integer' => 'La cuenta bancaria debe ser un valor válido.',

            'nro_operacion.required' => 'El número de operación es obligatorio cuando el modo de pago no es efectivo.',
            'nro_operacion.max' => 'El número de operación no puede exceder 20 caracteres.',

            'fecha_operacion.required' => 'La fecha de operación es obligatoria.',
            'fecha_operacion.date' => 'La fecha de operación debe ser una fecha válida.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Errores de validación al crear egreso.',
            'errors'    => $validator->errors()
        ], 422));
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $monto = (float) $this->monto;
            $importe = (float) $this->importe;
            $efectivo = (float) $this->efectivo;

            if ($monto !== ($importe + $efectivo)) {
                $validator->errors()->add('monto', 'El monto debe ser igual a la suma de importe más efectivo.');
            }

            if ($this->modo_pago != 1 && $this->filled('cuenta_bancaria')) {
                $exists = DB::table('tipo_pago_cuentas')
                    ->where('tipo_pago_id', $this->modo_pago)
                    ->where('cuenta_id', $this->cuenta_bancaria)
                    ->exists();

                if (!$exists) {
                    $validator->errors()->add('cuenta_bancaria', 'La cuenta bancaria seleccionada no está asociada al modo de pago elegido.');
                }
            }
        });
    }
}
