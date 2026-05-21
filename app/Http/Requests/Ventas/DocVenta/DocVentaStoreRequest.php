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

            'lstPagos' => 'nullable|string',
        ];

        // validar imágenes de pagos
        $rules['lstImgsPagos.0'] = 'nullable|mimes:png,jpg,jpeg,webp|max:5120';
        $rules['lstImgsPagos.1'] = 'nullable|mimes:png,jpg,jpeg,webp|max:5120';

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

            'lstImgsPagos.0.mimes' => 'La imagen del pago 1 debe ser jpg, png o webp.',
            'lstImgsPagos.0.max'   => 'La imagen del pago 1 no debe superar los 5 MB.',
            'lstImgsPagos.1.mimes' => 'La imagen del pago 2 debe ser jpg, png o webp.',
            'lstImgsPagos.1.max'   => 'La imagen del pago 2 no debe superar los 5 MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
