<?php

namespace App\Http\Requests\Ventas\Guias;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

class GuiaStoreRequest extends FormRequest
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
        $rules = [
            'categoria_M1L'       => 'nullable',
            'almacen'             => ['required', 'exists:almacenes,id,estado,ACTIVO'],
            'fecha_emision'       => ['required', 'date'],
            'cliente'             => ['required_if:motivo_traslado,01', 'exists:clientes,id,estado,ACTIVO'],
            'modalidad_traslado'  => ['required', Rule::in(['01', '02'])],
            'motivo_traslado'     => ['required', 'exists:tabladetalles,id,estado,ACTIVO'],
            'fecha_traslado'      => ['required', 'date'],
            'peso'                => ['required', 'numeric', 'min:0.1'],
            'unidad'              => ['required', Rule::in(['KGM', 'TNE'])],
        ];
    
        if ($this->input('categoria_M1L') !== 'on') {
            $rules['vehiculo'] = ['required', 'exists:vehiculos,id,estado,ACTIVO'];
            $rules['conductor'] = [
                'required', 
                'exists:conductores,id,estado,ACTIVO',
                function ($attribute, $value, $fail) {
                    $modalidad = $this->input('modalidad_traslado');
                    $modo = $modalidad === '01' ? 'PUBLICO' : 'PRIVADO';
                    if (!DB::table('conductores')->where('id', $value)->where('modalidad_transporte', $modo)->exists()) {
                        $fail("El conductor no tiene la modalidad de transporte correspondiente a la modalidad de traslado seleccionada.");
                    }
                }
            ];
        } else {
            $rules['vehiculo'] = 'nullable';
            $rules['conductor'] = 'nullable';
        }
    
        return $rules;
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'almacen.required'             => 'El campo almacén es obligatorio.',
            'almacen.exists'               => 'El almacén seleccionado no es válido o no está activo.',

            'fecha_emision.required'       => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date'           => 'La fecha de emisión debe ser una fecha válida.',

            'cliente.required_if'          => 'El cliente es obligatorio cuando el motivo de traslado es VENTA.',
            'cliente.exists'               => 'El cliente seleccionado no es válido o no está activo.',

            'modalidad_traslado.required'  => 'La modalidad de traslado es obligatoria.',
            'modalidad_traslado.in'        => 'La modalidad de traslado debe ser "01" (TRANSPORTE PUBLICO) o "02" (TRANSPORTE PRIVADO).',
            'motivo_traslado.required'     => 'El motivo de traslado es obligatorio.',
            'motivo_traslado.exists'       => 'El motivo de traslado no es válido o no está activo.',

            'fecha_traslado.required'      => 'La fecha de traslado es obligatoria.',
            'fecha_traslado.date'          => 'La fecha de traslado debe ser una fecha válida.',

            'peso.required'                => 'El peso es obligatorio.',
            'peso.numeric'                 => 'El peso debe ser un número.',
            'peso.min'                     => 'El peso mínimo permitido es 0.1.',

            'unidad.required'              => 'La unidad es obligatoria.',
            'unidad.in'                    => 'La unidad debe ser "KGM" o "TNE".',

            'vehiculo.required_unless'     => 'El vehículo es obligatorio si no se ha seleccionado la categoría M1L.',
            'vehiculo.exists'              => 'El vehículo seleccionado no es válido o no está activo.',

            'conductor.required_unless'    => 'El conductor es obligatorio si no se ha seleccionado la categoría M1L.',
            'conductor.exists'             => 'El conductor seleccionado no es válido o no está activo.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}