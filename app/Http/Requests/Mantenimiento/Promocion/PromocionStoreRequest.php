<?php

namespace App\Http\Requests\Mantenimiento\Promocion;

use App\Classes\TipoPromocion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PromocionStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            'nombre' => [

                'required',
                'string',
                'max:160',

                Rule::unique('promociones', 'nombre')
                    ->where(function ($query) {

                        return $query->where(
                            'estado',
                            'ACTIVO'
                        );
                    }),
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tipo_promocion' => [

                'required',

                TipoPromocion::reglaValidacion(),
            ],

            'valor' => [

                'required',
                'numeric',
                'min:0',
            ],

            'cantidad_minima' => [

                'required',
                'integer',
                'min:1',
            ],

            'fecha_inicio' => [

                'nullable',
                'date',
            ],

            'fecha_fin' => [

                'nullable',
                'date',
                'after_or_equal:fecha_inicio',
            ],
        ];
    }

    public function messages()
    {
        return [

            // NOMBRE
            'nombre.required' => 'el nombre de la promoción es obligatorio',

            'nombre.string' => 'el nombre debe ser texto',

            'nombre.max' => 'el nombre no debe superar los 160 caracteres',

            'nombre.unique' => 'ya existe una promoción activa con ese nombre',


            // TIPO PROMOCION
            'tipo_promocion.required' => 'debe seleccionar el tipo de promoción',

            'tipo_promocion.in' => 'el tipo de promoción no es válido',


            // VALOR
            'valor.required' => 'el valor de la promoción es obligatorio',

            'valor.numeric' => 'el valor debe ser numérico',

            'valor.min' => 'el valor no puede ser negativo',


            // CANTIDAD
            'cantidad_minima.required' => 'la cantidad mínima es obligatoria',

            'cantidad_minima.integer' => 'la cantidad mínima debe ser un número entero',

            'cantidad_minima.min' => 'la cantidad mínima debe ser al menos 1',


            // FECHAS
            'fecha_inicio.date' => 'la fecha inicio no es válida',

            'fecha_fin.date' => 'la fecha fin no es válida',

            'fecha_fin.after_or_equal' =>
                'la fecha fin debe ser mayor o igual a la fecha inicio',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(

            $validator,

            response()->json([

                'errors' => $validator->errors()

            ], 422)
        );
    }
}
