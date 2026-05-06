<?php

namespace App\Http\Requests\Almacen\Modelo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ModeloStoreRequest extends FormRequest
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
        $data = $this->all();
        $newData = [];

        foreach ($data as $key => $value) {

            if (preg_match('/^(.*)_model$/', $key, $matches)) {
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
                'max:191',
                Rule::unique('modelos', 'descripcion')
                    ->where(function ($query) {
                        return $query->where('estado', 'ACTIVO');
                    }),
            ]
        ];
    }

    public function messages()
    {
        return [
            'descripcion.required' => 'el nombre del modelo es obligatorio',
            'descripcion.max' => 'el nombre del modelo no debe superar los 160 caracteres',
            'descripcion.unique' => 'el nombre del modelo ya se encuentra registrado'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
