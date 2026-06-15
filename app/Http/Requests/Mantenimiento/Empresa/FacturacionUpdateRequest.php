<?php

namespace App\Http\Requests\Mantenimiento\Empresa;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FacturacionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Credenciales API Guía Remisión
            'id_api_guia_remision'    => ['nullable', 'string', 'max:160'],
            'clave_api_guia_remision' => ['nullable', 'string', 'max:160'],

            // Credenciales Consulta CPE SUNAT
            'cpe_client_id'     => ['nullable', 'string', 'max:160'],
            'cpe_client_secret' => ['nullable', 'string', 'max:160'],

            // SOL
            'sol_user' => ['nullable', 'string', 'max:160'],
            'sol_pass' => ['nullable', 'string', 'max:160'],

            // Certificado digital (pfx / p12 / pem)
            'certificado' => ['nullable', 'file', 'max:2048'],

            // Requerida solo si el certificado subido es .pfx o .p12
            'contra_certificado' => [
                Rule::requiredIf(function () {
                    $file = $this->file('certificado');
                    if (!$file) {
                        return false;
                    }
                    $ext = strtolower($file->getClientOriginalExtension());
                    return in_array($ext, ['pfx', 'p12']);
                }),
                'nullable',
                'string',
                'max:160',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $file = $this->file('certificado');
            if (!$file) {
                return;
            }

            $ext     = strtolower($file->getClientOriginalExtension());
            $allowed = ['pfx', 'p12', 'pem'];

            if (!in_array($ext, $allowed)) {
                $v->errors()->add(
                    'certificado',
                    'El certificado debe ser un archivo .pfx, .p12 o .pem.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'id_api_guia_remision.max'    => 'ID API no debe exceder 160 caracteres.',
            'clave_api_guia_remision.max' => 'Clave API no debe exceder 160 caracteres.',
            'cpe_client_id.max'           => 'Client ID CPE no debe exceder 160 caracteres.',
            'cpe_client_secret.max'       => 'Client Secret CPE no debe exceder 160 caracteres.',
            'sol_user.max'                => 'Usuario SOL no debe exceder 160 caracteres.',
            'sol_pass.max'                => 'Contraseña SOL no debe exceder 160 caracteres.',
            'certificado.file'            => 'El certificado debe ser un archivo válido.',
            'certificado.max'             => 'El certificado no debe pesar más de 2MB.',
            'contra_certificado.required' => 'La contraseña del certificado es obligatoria para archivos .pfx y .p12.',
            'contra_certificado.max'      => 'La contraseña del certificado no debe exceder 160 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator, response()->json([
            'success' => false,
            'message' => collect($validator->errors()->all())->first(),
            'errors'  => $validator->errors(),
        ], 422));
    }
}
