<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReciboCajaRequest extends FormRequest
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
            'fecha_documento' => 'required|date',
            'empresa' => 'required|exists:empresas,id',
            'fecha_atencion' => 'required|date',
            'vendedor' => 'required|exists:vendedores,id',
            'cliente' => 'required|exists:clientes,id',
            'monto' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'fecha_documento.required' => 'El campo fecha del documento es obligatorio.',
            'fecha_documento.date' => 'El campo fecha del documento debe ser una fecha válida.',
            'empresa.required' => 'El campo empresa es obligatorio.',
            'empresa.exists' => 'La empresa seleccionada no es válida.',
            'fecha_atencion.required' => 'El campo fecha de atención es obligatorio.',
            'fecha_atencion.date' => 'El campo fecha de atención debe ser una fecha válida.',
            'vendedor.required' => 'El campo vendedor es obligatorio.',
            'vendedor.exists' => 'El vendedor seleccionado no es válido.',
            'cliente.required' => 'El campo cliente es obligatorio.',
            'cliente.exists' => 'El cliente seleccionado no es válido.',
            'monto.required' => 'El campo monto es obligatorio.',
            'monto.numeric' => 'El campo monto debe ser un número.',
            'monto.min' => 'El campo monto debe ser un número positivo.',
        ];
    }
}
