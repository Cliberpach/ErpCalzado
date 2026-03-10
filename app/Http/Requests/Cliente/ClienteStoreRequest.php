<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ClienteStoreRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge([
            'department' => str_pad($this->department, 2, '0', STR_PAD_LEFT),
            'province'   => str_pad($this->province, 4, '0', STR_PAD_LEFT),
            'district'   => str_pad($this->district, 6, '0', STR_PAD_LEFT),
        ]);
    }


    public function rules(): array
    {
        return [
            'type_customer' => [
                'required',
                Rule::exists('tipos_clientes', 'id')
                    ->where(function ($query) {
                        $query->where('estado', 'ACTIVO');
                    }),
            ],
            'type_identity_document' => [
                'required',
                Rule::exists('tabladetalles', 'id')->where(function ($query) {
                    $query->where('estado', 'ACTIVO');
                }),
            ],
            'nro_document' => [
                'required',
                function ($attribute, $value, $fail) {
                    $tipoDocumento = $this->tipo_documento;

                    if ($tipoDocumento == 6 && (!is_numeric($value) || strlen($value) != 8)) {
                        $fail('El número de documento debe ser numérico y tener 8 dígitos para DNI.');
                    }

                    if ($tipoDocumento == 8 && (!is_numeric($value) || strlen($value) != 11)) {
                        $fail('El número de documento debe ser numérico y tener 11 dígitos para RUC.');
                    }

                    if (!in_array($tipoDocumento, [6, 8]) && strlen($value) > 20) {
                        $fail('El número de documento no debe exceder los 20 caracteres para los demás tipos de documento.');
                    }
                },
                Rule::unique('clientes', 'documento')
                    ->where('estado', 'ACTIVO'),
            ],
            'name'    => ['required', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:160'],
            'phone'  => ['nullable', 'string', 'max:20'],
            'email'    => ['nullable', 'email', 'max:160'],
            'deparment' => [
                'nullable',
                'sometimes',
                'string',
                'size:2',
                Rule::exists('departamentos', 'id'),
            ],
            'province' => [
                'nullable',
                'sometimes',
                'string',
                'size:4',
                Rule::exists('provincias', 'id'),
            ],
            'district' => [
                'nullable',
                'sometimes',
                'string',
                'size:6',
                Rule::exists('distritos', 'id'),
            ],
            // 'limite_credito' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99']
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type_identity_document.required'   => 'El tipo de documento es obligatorio.',
            'type_identity_document.exists'     => 'El tipo de documento debe existir y estar ACTIVO.',

            'nro_document.required'    => 'El número de documento es obligatorio.',
            'nro_document.numeric'     => 'El número de documento debe ser numérico.',
            'nro_document.unique'      => 'El número de documento ya está registrado para otro cliente ACTIVO.',

            'name.required'   => 'El nombre es obligatorio.',
            'name.max'        => 'El nombre no debe exceder 160 caracteres.',

            'address.max'     => 'La dirección no debe exceder 160 caracteres.',

            'phone.max'      => 'El teléfono no debe exceder 20 caracteres.',

            'email.email'      => 'El correo debe tener un formato válido.',
            'email.max'        => 'El correo no debe exceder 160 caracteres.',

            'department.required' => 'El departamento es obligatorio.',
            'department.size'     => 'El departamento debe tener 2 caracteres.',
            'department.exists'   => 'El departamento seleccionado no es válido.',

            'province.required'    => 'La provincia es obligatoria.',
            'province.size'        => 'La provincia debe tener 4 caracteres.',
            'province.exists'      => 'La provincia seleccionada no es válida.',

            'district.required'     => 'El distrito es obligatorio.',
            'district.size'         => 'El distrito debe tener 6 caracteres.',
            'district.exists'       => 'El distrito seleccionado no es válido.',

            'type_customer.required' => 'El tipo de cliente es obligatorio.',
            'type_customer.exists'   => 'El tipo de cliente seleccionado no existe o no está activo.'

        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
