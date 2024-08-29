<?php

namespace App\Http\Requests\Pedido;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class DetalleGenerarOrdenProduccion extends FormRequest
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
            'fecha_propuesta_atencion'  =>  'required|date',
            'observacion'               =>  'nullable|string|max:260',
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
            'fecha_propuesta_atencion.required' => 'La fecha propuesta de atención es obligatoria.',
            'fecha_propuesta_atencion.date'     => 'La fecha propuesta de atención debe ser una fecha válida.',
            'observacion.max'                   => 'La observación no debe exceder los 260 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   =>  false,
            'message'   =>  'Errores de validación al crear Cliente.',
            'errors'    =>  $validator->errors()
        ], 422));
    }
}
