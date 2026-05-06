<?php

namespace App\Http\Requests\Almacen\Talla;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class TallaStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'descripcion'   => mb_strtoupper(trim($this->descripcion ?? ''), 'UTF-8'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'descripcion' => [
                'required',
                'string',
                'max:160',
                Rule::unique('tallas', 'descripcion')
                    ->where(function ($query) {
                        return $query->where('estado', 'ACTIVO');
                    }),
            ]
        ];
    }

    public function messages()
    {
        return [
            'descripcion.required' => 'el nombre del color es obligatorio',
            'descripcion.max' => 'el nombre del color no debe superar los 160 caracteres',
            'descripcion.unique' => 'el nombre del color ya se encuentra registrado',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
