<?php

namespace App\Http\Requests\Almacen\Producto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class ProductoStoreRequest extends FormRequest
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
                'max:150',
                Rule::unique('productos')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                }),
            ],
            'categoria' => 'required',
            'marca'     => 'required',
            'modelo'    => 'required',
            'costo'     => 'nullable|numeric',
            'precio1'   => 'required|numeric|min:0.01', 
            'precio2'   => 'required|numeric|min:0.01', 
            'precio3'   => 'required|numeric|min:0.01',
            'almacen'   => [
                function ($attribute, $value, $fail) {
                    $colores = json_decode($this->input('coloresJSON'), true);
                    if (is_array($colores) && count($colores) > 0 && empty($value)) {
                        $fail('Almacén es obligatorio si se asignan colores.');
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre.required'   => 'El campo nombre es obligatorio.',
            'nombre.string'     => 'El campo nombre debe ser una cadena de texto.',
            'nombre.max'        => 'El campo nombre no puede tener más de 150 caracteres.',
            'nombre.unique'     => 'El valor del campo nombre ya está en uso.',

            'categoria.required' => 'El campo categoría es obligatorio.',

            'marca.required'    => 'El campo marca es obligatorio.',
            'modelo.required'   => 'El campo modelo es obligatorio.',

            'costo.numeric'     => 'El campo costo debe ser numérico.',

            'precio1.required'  => 'El campo precio 1 es obligatorio.',
            'precio1.numeric'   => 'El campo precio 1 debe ser numérico.',
            'precio1.min'       => 'El campo precio 1 debe ser mayor a 0.',

            'precio2.required'  => 'El campo precio 2 es obligatorio.',
            'precio2.numeric'   => 'El campo precio 2 debe ser numérico.',
            'precio2.min'       => 'El campo precio 2 debe ser mayor a 0.',

            'precio3.required'  => 'El campo precio 3 es obligatorio.',
            'precio3.numeric'   => 'El campo precio 3 debe ser numérico.',
            'precio3.min'       => 'El campo precio 3 debe ser mayor a 0.',
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
