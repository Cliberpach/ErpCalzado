<?php

namespace App\Http\Requests\Pedidos\Pedido;

use Illuminate\Foundation\Http\FormRequest;

class PedidoDocVentaRequest extends FormRequest
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
            'modo' => 'required|in:ATENCION,RESERVA',
        ];
    }

    /**
     * Get the custom error messages for the validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'modo.in' => 'El campo modo solo puede ser ATENCION o RESERVA.',
            'modo.required' => 'El campo modo es obligatorio.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Devuelve una respuesta JSON con los errores de validación
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'success'   =>  false,
            'errors' => $validator->errors()
        ], 422));
    }
}
