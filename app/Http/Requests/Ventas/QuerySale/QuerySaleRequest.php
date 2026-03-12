<?php

namespace App\Http\Requests\Ventas\QuerySale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class QuerySaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
            'tipo_doc'       => 'required|in:01,03,07',
            'fecha_emision'  => 'required|date',
            'serie'          => 'required|size:4',
            'correlativo'    => 'required|numeric',
            'doc_cliente'    => 'required|max:20',
            'monto_total'    => 'required|numeric',
        ];
    }

    /**
     * Get the custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tipo_doc.required'      => 'El tipo de documento es obligatorio.',
            'tipo_doc.in'            => 'El tipo de documento debe ser uno de los siguientes valores: 01, 03, 07.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date'     => 'La fecha de emisión debe ser una fecha válida.',
            'serie.required'         => 'La serie es obligatoria.',
            'serie.size'             => 'La serie debe tener exactamente 4 caracteres.',
            'correlativo.required'   => 'El correlativo es obligatorio.',
            'correlativo.numeric'    => 'El correlativo debe ser un número.',
            'doc_cliente.required'   => 'El documento del cliente es obligatorio.',
            'doc_cliente.max'        => 'El documento del cliente no puede tener más de 20 caracteres.',
            'monto_total.required'   => 'El monto total es obligatorio.',
            'monto_total.numeric'    => 'El monto total debe ser un número (puede ser entero o decimal).',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
