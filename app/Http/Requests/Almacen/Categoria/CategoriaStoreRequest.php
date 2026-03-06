<?php

namespace App\Http\Requests\Almacen\Categoria;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoriaStoreRequest extends FormRequest
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
            'nombre' => [
                'required',
                'string',
                'max:191',
                Rule::unique('categorias', 'descripcion')->where(function ($query) {
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

    public function messages()
    {
        return [

            // nombre
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 191 caracteres.',
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',

            // imagen
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpg, jpeg, png, webp o avif.',
            'imagen.max' => 'La imagen no debe superar los 2 MB.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Devuelve una respuesta JSON con los errores de validación
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'success'   =>  false,
            'errors' => $validator->errors()
        ], 422));
    }
}
