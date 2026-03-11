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


            /* -------- CAMPOS ADICIONALES -------- */
            'direccion_negocio' => ['nullable', 'string', 'max:191'],

            'fecha_aniversario' => [
                'nullable',
                'sometimes',
                'date'
            ],

            'observaciones' => ['nullable', 'string'],

            'facebook' => ['nullable', 'string', 'max:191'],

            'instagram' => ['nullable', 'string', 'max:191'],

            'web' => ['nullable', 'string', 'max:191'],

            'hora_inicio' => ['nullable', 'string', 'max:191'],

            'hora_termino' => ['nullable', 'string', 'max:191'],

            'nombre_propietario' => ['nullable', 'string', 'max:191'],

            'direccion_propietario' => ['nullable', 'string', 'max:191'],

            'fecha_nacimiento_prop' => [
                'nullable',
                'date'
            ],

            'celular_propietario' => ['nullable', 'string', 'max:191'],

            'correo_propietario' => ['nullable', 'email', 'max:191'],

            'url_logo' => ['nullable', 'string', 'max:191'],

            /* -------- IMAGEN -------- */
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:2048' // 2MB
            ],
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
            'type_customer.exists'   => 'El tipo de cliente seleccionado no existe o no está activo.',

            //========= CAMPOS EXTRA ==============

            'direccion_negocio.string' => 'La dirección del negocio debe ser texto.',
            'direccion_negocio.max' => 'La dirección del negocio no debe superar los 191 caracteres.',

            'fecha_aniversario.date' => 'La fecha de aniversario debe tener un formato de fecha válido.',

            'observaciones.string' => 'Las observaciones deben ser texto.',

            'facebook.string' => 'El campo Facebook debe ser texto.',
            'facebook.max' => 'El campo Facebook no debe superar los 191 caracteres.',

            'instagram.string' => 'El campo Instagram debe ser texto.',
            'instagram.max' => 'El campo Instagram no debe superar los 191 caracteres.',

            'web.string' => 'El campo web debe ser texto.',
            'web.max' => 'El campo web no debe superar los 191 caracteres.',

            'hora_inicio.string' => 'La hora de inicio debe ser texto.',
            'hora_inicio.max' => 'La hora de inicio no debe superar los 191 caracteres.',

            'hora_termino.string' => 'La hora de término debe ser texto.',
            'hora_termino.max' => 'La hora de término no debe superar los 191 caracteres.',

            'nombre_propietario.string' => 'El nombre del propietario debe ser texto.',
            'nombre_propietario.max' => 'El nombre del propietario no debe superar los 191 caracteres.',

            'direccion_propietario.string' => 'La dirección del propietario debe ser texto.',
            'direccion_propietario.max' => 'La dirección del propietario no debe superar los 191 caracteres.',

            'fecha_nacimiento_prop.date' => 'La fecha de nacimiento del propietario debe ser una fecha válida.',

            'celular_propietario.string' => 'El celular del propietario debe ser texto.',
            'celular_propietario.max' => 'El celular del propietario no debe superar los 191 caracteres.',

            'correo_propietario.email' => 'El correo del propietario debe tener un formato válido.',
            'correo_propietario.max' => 'El correo del propietario no debe superar los 191 caracteres.',

            'url_logo.string' => 'La URL del logo debe ser texto.',
            'url_logo.max' => 'La URL del logo no debe superar los 191 caracteres.',

            'logo.image' => 'El archivo del logo debe ser una imagen.',
            'logo.mimes' => 'El logo debe ser un archivo de tipo: jpg, jpeg, png, webp o avif.',
            'logo.max' => 'El logo no debe superar los 2MB.',

        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
