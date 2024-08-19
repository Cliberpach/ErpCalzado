<?php

namespace App\Http\Requests\Pedido;

use Illuminate\Foundation\Http\FormRequest;

class PedidoUpdateRequest extends FormRequest
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
            'empresa'       => 'required',
            'cliente'       => 'required',
            'condicion_id'  => 'required',
        ];
    }

    /**
     * Get the custom messages for the validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'empresa.required'       => 'El campo Empresa es obligatorio',
            'cliente.required'       => 'El campo Cliente es obligatorio',
            'condicion_id.required'  => 'El campo Condición es obligatorio',
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
