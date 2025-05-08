<?php

namespace App\Http\Requests\Mantenimiento\Colaborador;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ColaboradorStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sede' => [
                'required',
                function ($attribute, $value, $fail) {
                    $sede = DB::table('empresa_sedes')
                        ->where('id', $value)
                        ->where('estado', 'ACTIVO')
                        ->first();

                    if (!$sede) {
                        $fail('La sede no existe en la BD!!!');
                    }
                }
            ],
            'tipo_documento' => [
                'required', 
                'exists:tabladetalles,id,estado,ACTIVO'
            ],
           'nro_documento' => [
                'required', 
                'numeric', 
                function ($attribute, $value, $fail) {
                    $tipoDocumento = $this->input('tipo_documento');

                    if ($tipoDocumento == 6 && strlen($value) != 8) {
                        $fail('El número de documento debe tener 8 dígitos si el tipo de documento es DNI.');
                    }

                    if ($tipoDocumento == 7 && (strlen($value) < 6 || strlen($value) > 20)) {
                        $fail('El número de documento debe tener entre 6 y 20 dígitos si el tipo de documento es Carnet de Extranjería.');
                    }
                },
                'unique:colaboradores,nro_documento,NULL,id,estado,ACTIVO'
            ],
            'nombre'        => 'required|max:260|unique:colaboradores,nombre',
            'cargo'         => 'required|exists:tabladetalles,id',
            'direccion'     => 'nullable|max:200',
            'telefono'      => ['nullable', 'max:20', 'regex:/^[0-9]+$/'],
            'dias_descanso' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'dias_trabajo'  => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'pago_mensual'  => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'sede.required'     => 'La sede es obligatoria.',
            'sede.exists'       => 'La sede seleccionada no es válida o no está activa.',

            'tipo_documento.required'   => 'El tipo de documento es obligatorio.',
            'tipo_documento.integer'    => 'El tipo de documento debe ser un número entero.',
            'tipo_documento.in'         => 'El tipo de documento no es válido.',

            'nro_documento.required'    => 'El número de documento es obligatorio.',
            'nro_documento.numeric'     => 'El número de documento debe ser numérico.',
            'nro_documento.unique'      => 'El número de documento ya está registrado.',

            'nombre.required'           => 'El nombre es obligatorio.',
            'nombre.max'                => 'El nombre no debe superar los 260 caracteres.',
            'nombre.unique'             => 'El nombre ya está en uso, por favor elige otro.',

            'cargo.required'            => 'El campo cargo es obligatorio.',
            'cargo.exists'              => 'El cargo seleccionado no es válido.',

            'direccion.max'             => 'La dirección no debe superar los 200 caracteres.',

            'telefono.max'              => 'El teléfono no debe superar los 20 caracteres.',
            'telefono.regex'            => 'El teléfono debe contener solo números.',

            'dias_trabajo.required'     => 'Las horas de la semana son obligatorias.',
            'dias_trabajo.numeric'      => 'Las horas de la semana deben ser un número.',
            'dias_trabajo.regex'        => 'Las horas de la semana deben tener como máximo 2 decimales.',

            'dias_descanso.required'     => 'Las horas de la semana son obligatorias.',
            'dias_descanso.numeric'      => 'Las horas de la semana deben ser un número.',
            'dias_descanso.regex'        => 'Las horas de la semana deben tener como máximo 2 decimales.',

            'pago_mensual.numeric'       => 'El pago semanal debe ser un número.',
            'pago_mensual.regex'         => 'El pago semanal debe tener como máximo 2 decimales.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
