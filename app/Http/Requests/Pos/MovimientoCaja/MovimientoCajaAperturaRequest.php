<?php

namespace App\Http\Requests\Pos\MovimientoCaja;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class MovimientoCajaAperturaRequest extends FormRequest
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
            'caja' => [
                'required',
                'exists:caja,id',
                function ($attribute, $value, $fail) {
                    $caja = DB::table('caja')->where('id', $value)->first();

                    if (!$caja) {
                        $fail('La caja seleccionada no existe.');
                    } elseif ($caja->estado !== 'ACTIVO') {
                        $fail('La caja seleccionada no está activa.');
                    } elseif ($caja->estado_caja !== 'CERRADA') {
                        $fail('La caja seleccionada ya está aperturada.');
                    }
                }
            ],
            'cajero_id' => [
                'required',
                'exists:colaboradores,id',
                function ($attribute, $value, $fail) {
                    $cajero = DB::table('colaboradores')
                        ->where('id', $value)
                        ->where('estado', 'ACTIVO')
                        ->first();
                    if (!$cajero) {
                        $fail('El cajero seleccionado no está disponible o no cumple los requisitos.');
                    }
                }
            ],
            'saldo_inicial' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'caja.required'             => 'Debe seleccionar una caja.',
            'caja.exists'               => 'La caja seleccionada no existe.',

            'cajero_id.required'        => 'Debe seleccionar un cajero.',
            'cajero_id.exists'          => 'El cajero seleccionado no existe.',

            'saldo_inicial.required'    => 'El saldo inicial es obligatorio.',
            'saldo_inicial.numeric'     => 'El saldo inicial debe ser un número.',
            'saldo_inicial.min'         => 'El saldo inicial no puede ser negativo.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
