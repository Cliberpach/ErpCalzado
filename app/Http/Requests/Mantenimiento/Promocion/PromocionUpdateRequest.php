<?php

namespace App\Http\Requests\Mantenimiento\Promocion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class PromocionUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $cleanData = [];

        foreach ($this->all() as $key => $value) {

            if (str_ends_with($key, '_edit')) {

                $newKey = str_replace('_edit', '', $key);

                $cleanData[$newKey] = is_string($value)
                    ? mb_strtoupper(trim($value), 'UTF-8')
                    : $value;
            }
        }

        $this->replace($cleanData);
    }

    public function rules()
    {
        return [

            'nombre' => [

                'required',
                'string',
                'max:160',

                Rule::unique('promociones', 'nombre')
                    ->ignore($this->route('id'))
                    ->where(function ($query) {

                        return $query->where('estado', 'ACTIVO');
                    }),
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tipo_promocion' => [

                'required',

                'in:DESCUENTO_FIJO,DESCUENTO_PORCENTAJE,PRECIO_TOTAL',
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

            'nombre.required' => 'el nombre de la promoción es obligatorio',
            'nombre.string' => 'el nombre debe ser texto',
            'nombre.max' => 'el nombre no debe superar los 160 caracteres',
            'nombre.unique' => 'ya existe una promoción activa con ese nombre',

            'tipo_promocion.required' => 'debe seleccionar el tipo de promoción',
            'tipo_promocion.in' => 'el tipo de promoción no es válido',

            'valor.required' => 'el valor es obligatorio',
            'valor.numeric' => 'el valor debe ser numérico',
            'valor.min' => 'el valor no puede ser negativo',

            'cantidad_minima.required' => 'la cantidad mínima es obligatoria',
            'cantidad_minima.integer' => 'la cantidad mínima debe ser un número entero',
            'cantidad_minima.min' => 'la cantidad mínima debe ser al menos 1',

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
