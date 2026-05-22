<?php

namespace App\Http\Requests\Mantenimiento\Sedes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SedeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([

            'departamento' => $this->departamento
                ? str_pad($this->departamento, 2, '0', STR_PAD_LEFT)
                : null,

            'provincia' => $this->provincia
                ? str_pad($this->provincia, 4, '0', STR_PAD_LEFT)
                : null,

            'distrito' => $this->distrito
                ? str_pad($this->distrito, 6, '0', STR_PAD_LEFT)
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre'        => 'required|max:160',
            'direccion'     => 'required|max:150',
            'telefono'      => 'nullable|max:20',
            'correo'        => 'nullable|email|max:160',
            'departamento'  => 'required|max:10',
            'provincia'     => 'required|max:10',
            'distrito'      => 'required|max:10',
            'codigo_local'  => 'required|max:10',
            'urbanizacion'  => 'nullable|max:200',
            'img_empresa'   => 'nullable|image|mimes:jpg,jpeg,webp|max:1024',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'       => 'El nombre es obligatorio.',
            'nombre.max'            => 'El nombre no puede exceder los 160 caracteres.',

            'direccion.required'    => 'La dirección es obligatoria.',
            'direccion.max'         => 'La dirección no puede exceder los 150 caracteres.',

            'telefono.max'          => 'El teléfono no puede exceder los 20 caracteres.',

            'correo.email'          => 'El correo debe ser una dirección válida.',
            'correo.max'            => 'El correo no puede exceder los 160 caracteres.',

            'departamento.required' => 'El departamento es obligatorio.',
            'provincia.required'    => 'La provincia es obligatoria.',
            'distrito.required'     => 'El distrito es obligatorio.',

            'codigo_local.required' => 'El código local es obligatorio.',
            'codigo_local.max'      => 'El código local no puede exceder los 10 caracteres.',

            'urbanizacion.max'      => 'La urbanización no puede exceder los 200 caracteres.',

            'img_empresa.image'     => 'El archivo debe ser una imagen.',
            'img_empresa.mimes'     => 'Solo se permiten imágenes JPG, JPEG o WEBP.',
            'img_empresa.max'       => 'La imagen no puede superar 1 MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación al actualizar sede.',
            'errors'  => $validator->errors()
        ], 422));
    }
}
