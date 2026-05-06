<?php

namespace App\Http\Requests\Almacen\Almacen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

class AlmacenUpdateRequest extends FormRequest
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
                Rule::unique('almacenes', 'descripcion')
                    ->ignore($this->route('id'))
                    ->where(function ($query) {
                        return $query->where('estado', 'ACTIVO');
                    }),
            ],

            'ubicacion' => [
                'required',
                'string',
                'max:191',
            ],

            'tipo_almacen' => [
                'required',
                Rule::in(['PRINCIPAL', 'SECUNDARIO']),
                function ($attribute, $value, $fail) {

                    if ($value === 'PRINCIPAL') {

                        $existe = DB::table('almacenes')
                            ->where('sede_id', $this->sede_id)
                            ->where('tipo_almacen', 'PRINCIPAL')
                            ->where('estado', 'ACTIVO')
                            ->where('id', '!=', $this->route('id'))
                            ->exists();

                        if ($existe) {
                            $fail('ya existe un almacén principal activo para esta sede');
                        }
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'descripcion.required' => 'el nombre del almacén es obligatorio',
            'descripcion.max' => 'el nombre del almacén no debe superar los 160 caracteres',
            'descripcion.unique' => 'el nombre del almacén ya se encuentra registrado',

            'ubicacion.required' => 'la ubicación es obligatoria',
            'ubicacion.max' => 'la ubicación no debe superar los 191 caracteres',

            'tipo_almacen.required' => 'debe seleccionar un tipo de almacén',
            'tipo_almacen.in' => 'el tipo de almacén debe ser principal o secundario',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
