<?php

namespace App\Http\Requests\Despachos\EnvioVenta;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class EnvioVentaStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Ubicación obligatoria
            'departamento' => ['required', 'integer'],
            'provincia'    => ['required', 'integer'],
            'distrito'     => ['required', 'integer'],

            // Tipo de envío (solo 188, 189, 190 y validación en tabladetalles tabla_id=35)
            'tipo_envio'   => [
                'required',
                Rule::in([187, 188, 189]),
                Rule::exists('tabladetalles', 'id')->where(function ($q) {
                    $q->where('estado', 'ACTIVO')->where('tabla_id', 35);
                }),
            ],

            // Empresa envío obligatoria y debe estar activa
            'empresa_envio' => [
                'required',
                Rule::exists('empresas_envio', 'id')->where(function ($q) {
                    $q->where('estado', 'ACTIVO');
                }),
            ],

            // Sede envío obligatoria si tipo_envio != 189
            'sede_envio' => [
                'required_if:tipo_envio,187,189',
                //Rule::exists('empresa_envio_sedes', 'id'),
            ],

            // Destinatario (array opcional con reglas internas)
            'destinatario' => ['nullable', 'array'],

            // Dirección entrega
            'direccion_entrega' => ['nullable', 'string', 'max:191'],

            // Entrega domicilio (boolean)
            'entrega_domicilio' => ['boolean'],

            // Origen venta (tabladetalles tabla_id=36)
            'origen_venta' => [
                'required',
                Rule::exists('tabladetalles', 'id')->where(function ($q) {
                    $q->where('estado', 'ACTIVO')->where('tabla_id', 36);
                }),
            ],

            // Fecha propuesta
            'fecha_envio_propuesta' => ['nullable', 'date'],

            // Observaciones
            'obs_rotulo'   => ['nullable', 'string', 'max:200'],
            'obs_despacho' => ['nullable', 'string', 'max:200'],

            // Tipo pago envío (tabladetalles tabla_id=37)
            'tipo_pago_envio' => [
                'required',
                Rule::exists('tabladetalles', 'id')->where(function ($q) {
                    $q->where('estado', 'ACTIVO')->where('tabla_id', 37);
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'departamento.required' => 'El departamento es obligatorio.',
            'provincia.required'    => 'La provincia es obligatoria.',
            'distrito.required'     => 'El distrito es obligatorio.',

            'tipo_envio.required' => 'El tipo de envío es obligatorio.',
            'tipo_envio.in'       => 'El tipo de envío seleccionado no es válido.',
            'tipo_envio.exists'   => 'El tipo de envío no existe o no está activo.',

            'empresa_envio.required' => 'La empresa de envío es obligatoria.',
            'empresa_envio.exists'   => 'La empresa de envío seleccionada no es válida o está inactiva.',

            'sede_envio.required_if' => 'La sede de envío es obligatoria para este tipo de envío.',
            'sede_envio.exists'      => 'La sede de envío seleccionada no existe.',

            'destinatario.array' => 'El destinatario debe ser un objeto válido.',

            'documento_id.required' => 'El documento relacionado es obligatorio.',

            'direccion_entrega.max' => 'La dirección de entrega no debe superar los 191 caracteres.',

            'entrega_domicilio.boolean' => 'El campo entrega a domicilio debe ser verdadero o falso.',

            'origen_venta.required' => 'El origen de la venta es obligatorio.',
            'origen_venta.exists'   => 'El origen de la venta no existe o no está activo.',

            'fecha_envio_propuesta.date' => 'La fecha de envío propuesta no es válida.',

            'obs_rotulo.max'   => 'La observación del rótulo no debe superar los 200 caracteres.',
            'obs_despacho.max' => 'La observación del despacho no debe superar los 200 caracteres.',

            'tipo_pago_envio.required' => 'El tipo de pago de envío es obligatorio.',
            'tipo_pago_envio.exists'   => 'El tipo de pago de envío no existe o no está activo.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
