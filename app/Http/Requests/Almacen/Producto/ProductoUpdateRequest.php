<?php

namespace App\Http\Requests\Almacen\Producto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class ProductoUpdateRequest extends FormRequest
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
        $productoId = $this->route('id');

        return [
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('productos')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                })->ignore($productoId),
            ],
            'categoria' => 'required',
            'marca'     => 'required',
            'modelo'    => 'required',
            'costo'     => 'nullable|numeric',
            'precio1'   => 'required|numeric',
            'precio2'   => 'required|numeric',
            'precio3'   => 'required|numeric',
            'almacen'   => [
                function ($attribute, $value, $fail) {
                    $colores = json_decode($this->input('coloresJSON'), true);
                    if (is_array($colores) && count($colores) > 0 && empty($value)) {
                        $fail('Almacén es obligatorio si se asignan colores.');
                    }
                },
            ],

            'coloresJSON'   =>  'nullable',

            'imagen1' => 'nullable|file|mimes:jpg,jpeg,webp,avif|max:2048',
            'imagen2' => 'nullable|file|mimes:jpg,jpeg,webp,avif|max:2048',
            'imagen3' => 'nullable|file|mimes:jpg,jpeg,webp,avif|max:2048',
            'imagen4' => 'nullable|file|mimes:jpg,jpeg,webp,avif|max:2048',
            'imagen5' => 'nullable|file|mimes:jpg,jpeg,webp,avif|max:2048',

            'mostrar_en_web' => 'nullable|boolean'

        ];
    }

    public function messages()
    {
        return [
            'nombre.required'       => 'El campo nombre es obligatorio.',
            'nombre.string'         => 'El campo nombre debe ser una cadena de texto.',
            'nombre.max'            => 'El campo nombre no puede tener más de 150 caracteres.',
            'nombre.unique'         => 'El valor del campo nombre ya está en uso.',

            'categoria.required'    => 'El campo categoría es obligatorio.',

            'marca.required'        => 'El campo marca es obligatorio.',

            'modelo.required'       => 'El campo modelo es obligatorio.',

            'costo.numeric'         => 'El campo costo debe ser numérico.',

            'precio1.required'      => 'El campo precio 1 es obligatorio.',
            'precio1.numeric'       => 'El campo precio 1 debe ser numérico.',

            'precio2.required'      => 'El campo precio 2 es obligatorio.',
            'precio2.numeric'       => 'El campo precio 2 debe ser numérico.',

            'precio3.required'      => 'El campo precio 3 es obligatorio.',
            'precio3.numeric'       => 'El campo precio 3 debe ser numérico.',

            'imagen1.mimes' => 'La imagen 1 debe ser un archivo de tipo: jpg, jpeg, webp, avif.',
            'imagen1.max'   => 'La imagen 1 no puede superar los 2 MB.',
            'imagen2.mimes' => 'La imagen 2 debe ser un archivo de tipo: jpg, jpeg, webp, avif.',
            'imagen2.max'   => 'La imagen 2 no puede superar los 2 MB.',
            'imagen3.mimes' => 'La imagen 3 debe ser un archivo de tipo: jpg, jpeg, webp, avif.',
            'imagen3.max'   => 'La imagen 3 no puede superar los 2 MB.',
            'imagen4.mimes' => 'La imagen 4 debe ser un archivo de tipo: jpg, jpeg, webp, avif.',
            'imagen4.max'   => 'La imagen 4 no puede superar los 2 MB.',
            'imagen5.mimes' => 'La imagen 5 debe ser un archivo de tipo: jpg, jpeg, webp, avif.',
            'imagen5.max'   => 'La imagen 5 no puede superar los 2 MB.',

            'mostrar_en_web.boolean' => 'El campo MOSTRAR EN WEB debe ser verdadero o falso.',

        ];
    }
}
