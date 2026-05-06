<?php

namespace App\Http\Requests\Almacen\Marca;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class MarcaStoreRequest extends FormRequest
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

            if (preg_match('/^(.*)_brand$/', $key, $matches)) {
                $newKey = $matches[1];

                $newData[$newKey] = mb_strtoupper(trim($value ?? ''), 'UTF-8');

                $this->request->remove($key);
            } else {
                $newData[$key] = is_string($value)
                    ? mb_strtoupper(trim($value), 'UTF-8')
                    : $value;
            }
        }

        $this->merge($newData);
    }

    // protected function prepareForValidation()
    // {
    //     $this->merge([
    //         'descripcion' => mb_strtoupper(trim($this->descripcion ?? ''), 'UTF-8'),
    //         'procedencia' => $this->procedencia !== null
    //             ? mb_strtoupper(trim($this->procedencia), 'UTF-8')
    //             : null,
    //     ]);
    // }

    public function rules()
    {
        return [
            'descripcion' => [
                'required',
                'string',
                'max:191',
                'regex:/\S+/',
                Rule::unique('marcas', 'marca')
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
            'descripcion.required' => 'el nombre del almacén es obligatorio',
            'descripcion.max' => 'el nombre del almacén no debe superar los 191 caracteres',
            'descripcion.unique' => 'el nombre del almacén ya se encuentra registrado',
            'descripcion.regex' => 'el nombre del almacén no puede estar vacío',

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
