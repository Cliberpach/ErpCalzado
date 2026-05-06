<?php

namespace App\Http\Requests\Almacen\Almacen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

class AlmacenStoreRequest extends FormRequest
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
        $this->merge([
            'descripcion'   => mb_strtoupper(trim($this->descripcion ?? ''), 'UTF-8'),
            'ubicacion'     => mb_strtoupper(trim($this->ubicacion ?? ''), 'UTF-8'),
            'tipo_almacen'  => mb_strtoupper(trim($this->tipo_almacen ?? ''), 'UTF-8'),
        ]);
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
