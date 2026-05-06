<?php

namespace App\Http\Requests\Almacen\Color;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ColorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $data = $this->all();

        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_color')) {
                $newKey = str_replace('_color', '', $key);

                $data[$newKey] = $value;
                unset($data[$key]);
            }
        }

        $this->replace($data);

        if ($this->descripcion) {
            $this->merge([
                'descripcion' => mb_strtoupper(trim($this->descripcion), 'UTF-8')
            ]);
        }
    }

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

            'codigo' => [
                'nullable',
                'string',
                'max:12',
                Rule::unique('colores', 'codigo')->where(function ($query) {
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

            // codigo
            'codigo.string' => 'El campo "código" debe ser una cadena de texto.',
            'codigo.max'    => 'El campo "código" no debe superar los 12 caracteres.',
            'codigo.unique' => 'Ya existe un código en estado ACTIVO.',

            // imagen
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpg, jpeg, png, webp o avif.',
            'imagen.max'   => 'La imagen no debe superar los 2 MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
