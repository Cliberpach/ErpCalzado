<?php

namespace App\Http\Requests\Mantenimiento\TipoPago;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class TipoPagoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'descripcion' => $this->input('descripcion_edit'),
        ]);
    }

    public function rules()
    {
        return [
            'descripcion' => [
                'required',
                'string',
                'max:160',
                Rule::unique('tipos_pago', 'descripcion')
                    ->ignore($this->route('id'))
                    ->where(function ($query) {
                        return $query
                            ->where('estado', '<>', 'ANULADO');
                    }),
            ]
        ];
    }

    public function messages()
    {
        return [
            'descripcion.required' => 'El campo nombre es obligatorio.',
            'descripcion.string'   => 'El campo nombre debe ser una cadena de texto.',
            'descripcion.max'      => 'El campo nombre no puede tener más de 160 caracteres.',
            'descripcion.unique'   => 'El nombre ya está en uso.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
