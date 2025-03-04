<?php

namespace App\Http\Requests\Ventas\DocVenta;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

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
        return [
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
        ];
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
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
