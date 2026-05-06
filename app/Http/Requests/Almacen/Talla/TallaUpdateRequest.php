<?php

namespace App\Http\Requests\Almacen\Talla;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class TallaUpdateRequest extends FormRequest
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

            if (str_ends_with($key, '_edit')) {

                $newKey = str_replace('_edit', '', $key);

                $newData[$newKey] = is_string($value)
                    ? mb_strtoupper(trim($value), 'UTF-8')
                    : $value;

                $this->request->remove($key);
            }
        }

        // merge final
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
                'max:160',
                Rule::unique('tallas', 'descripcion')
                    ->ignore($this->route('id'))
                    ->where(function ($query) {
                        return $query->where('estado', 'ACTIVO');
                    }),
            ]
        ];
    }

    public function messages()
    {
        return [
            'descripcion.required' => 'el nombre de la talla es obligatorio',
            'descripcion.max' => 'el nombre de talla no debe superar los 160 caracteres',
            'descripcion.unique' => 'el nombre de talla ya se encuentra registrado',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
