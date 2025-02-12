<?php

namespace App\Http\Requests\Ventas\DocVenta;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;


class DocVentaStoreRequest extends FormRequest
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

            'almacenSeleccionado.required'  => 'El campo almacén seleccionado es obligatorio.',
            'almacenSeleccionado.exists'    => 'El almacén seleccionado no es válido o no está activo.',

            'condicion_id.required'         => 'El campo condición es obligatorio.',
            'condicion_id.exists'           => 'La condición seleccionada no es válida o no está activa.',

            'cliente_id.required'           => 'El campo cliente es obligatorio.',
            'cliente_id.exists'             => 'El cliente seleccionado no es válido o no está activo.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
