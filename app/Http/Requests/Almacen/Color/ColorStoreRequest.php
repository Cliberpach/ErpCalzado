<?php

namespace App\Http\Requests\Almacen\Color;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ColorStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'descripcion' => [
                'required',
                'string',
                'max:191',
                Rule::unique('colores')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                }),
            ],

            'imagen' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:2048'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'El campo "descripción" es obligatorio.',
            'descripcion.string'   => 'El campo "descripción" debe ser una cadena de texto.',
            'descripcion.max'      => 'El campo "descripción" no debe exceder los 191 caracteres.',
            'descripcion.unique'   => 'Ya existe un color con esta descripción en estado ACTIVO.',

            // imagen
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpg, jpeg, png, webp o avif.',
            'imagen.max' => 'La imagen no debe superar los 2 MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
