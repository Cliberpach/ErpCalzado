<?php

namespace App\Http\Requests\Mantenimiento\Sedes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class NumeracionStoreRequest extends FormRequest
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
        return [
            'comprobante_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('tabladetalles')
                        ->where('id', $value)
                        ->where('tabla_id', 21)
                        ->exists();
                    if (!$exists) {
                        $fail('El comprobante seleccionado no es válido.');
                    }
                }
            ],
            'serie' => [
                'required',
                'regex:/^[A-Za-z0-9]+$/',
                function ($attribute, $value, $fail) {
                                    
                    $comprobanteId      = request()->get('comprobante_id');

                    $tipo_comprobante   =   DB::select('select 
                                            td.parametro
                                            from tabladetalles as td
                                            where td.id = ?',[$comprobanteId])[0];

                    dd($tipo_comprobante);

                    $length         = in_array($tipo_comprobante->parametro, ['FF', 'BB']) ? 2 : 3;
                    if (strlen($value) !== $length) {
                        $fail("La serie debe tener exactamente $length caracteres.");
                    }

                    $tipo_comprobante =     DB::select('select 
                                            td.parametro
                                            from tabladetalles as td
                                            where 
                                            td.id = ?
                                            and td.tabla_id = 21
                                            and td.estado = "ACTIVO"',
                                            [request()->get('comprobante_id')])[0];
                    
                    //======== VALIDAR QUE LA SERIE NO SE REPITA EN OTRAS SEDES ======
                    $exists =   DB::table('empresa_numeracion_facturaciones')
                                ->where('serie', $tipo_comprobante->parametro.$value)
                                ->where('tipo_comprobante', $comprobanteId)
                                ->where('sede_id', '!=', request()->get('sede_id')) 
                                ->exists();
        
                    if ($exists) {
                        $fail("La serie ya está registrada en otra sede.");
                    }
                }
            ],
            'nro_inicio' => [
                'required',
                'numeric',
                'min:1'
            ],
            'sede_id' => [
                'required',
                'exists:empresa_sedes,id'
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'comprobante_id.required'   => 'El comprobante es obligatorio.',
            'comprobante_id.exists'     => 'El comprobante seleccionado no es válido.',

            'serie.required'        => 'La serie es obligatoria.',
            'serie.regex'           => 'La serie solo puede contener letras y números, sin símbolos.',

            'nro_inicio.required'   => 'El número de inicio es obligatorio.',
            'nro_inicio.numeric'    => 'El número de inicio debe ser un valor numérico.',
            'nro_inicio.min'        => 'El número de inicio debe ser mayor a 0.',

            'sede_id.required'      => 'La sede es obligatoria.',
            'sede_id.exists'        => 'La sede seleccionada no es válida.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   =>  false,
            'message'   =>  'Errores de validación al añadir numeración a la sede.',
            'errors'    =>  $validator->errors()
        ], 422));
    }
}
