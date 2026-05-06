<?php

namespace App\Http\Requests\Almacen\Marca;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class MarcaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $data = $this->all();
        $newData = [];

        foreach ($data as $key => $value) {

            if (str_ends_with($key, '_edit')) {

                $newKey = str_replace('_edit', '', $key);

                $newData[$newKey] = is_string($value)
                    ? mb_strtoupper(trim($value), 'UTF-8')
                    : $value;

                $this->request->remove($key);
            }
        }

        $this->merge($newData);
    }

    public function rules()
    {
        return [
            'descripcion' => [
                'required',
                'string',
                'max:191',
                'regex:/\S+/',
                Rule::unique('marcas', 'marca')
                    ->ignore($this->route('id'))
                    ->where(function ($query) {
                        return $query->where('estado', 'ACTIVO');
                    }),
            ],

            'procedencia' => [
                'nullable',
                'string',
                'max:191',
            ],
        ];
    }

    public function messages()
    {
        return [
            // descripcion
            'descripcion.required' => 'el nombre de la marca es obligatorio',
            'descripcion.max' => 'el nombre de la marca no debe superar los 191 caracteres',
            'descripcion.unique' => 'el nombre de la marca ya se encuentra registrado',
            'descripcion.regex' => 'el nombre de la marca no puede estar vacío',

            // procedencia
            'procedencia.max' => 'la procedencia no debe superar los 191 caracteres',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
