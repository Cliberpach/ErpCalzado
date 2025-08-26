<?php

namespace App\Http\Requests\Ventas\Cotizacion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class CotizacionStoreRequest extends FormRequest
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
            'almacen' => [
                'required',
                'exists:almacenes,id',
                Rule::exists('almacenes', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],
            'cliente' => [
                'required',
                Rule::exists('clientes', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],
            'condicion_id' => [
                'required',
                Rule::exists('condicions', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],
            'telefono' => [
                'nullable',
                'digits_between:1,9',
                'numeric',
            ],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'almacen.required'  => 'El campo almacén es obligatorio.',
            'almacen.exists'    => 'El almacén seleccionado no existe o está ANULADO.',

            'cliente.required'  => 'El campo cliente es obligatorio.',
            'cliente.exists'    => 'El cliente seleccionado no existe o no está activo.',

            'condicion_id.required' => 'El campo condición es obligatorio.',
            'condicion_id.exists'   => 'La condición seleccionada no existe o no está activa.',

            'telefono.numeric' => 'El teléfono debe contener solo números.',
            'telefono.digits_between' => 'El teléfono no debe exceder los 9 dígitos.',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
