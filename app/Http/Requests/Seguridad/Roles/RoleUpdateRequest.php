<?php

namespace App\Http\Requests\Seguridad\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class RoleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Rules
     */
    public function rules()
    {
        return [

            //========================
            // DATOS GENERALES
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'name')->ignore($this->route('id')),
            ],

            'slug' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'slug')->ignore($this->route('id')),
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            //========================
            // FULL ACCESS
            //========================
            'full-access' => [
                'required',
                Rule::in(['SI', 'NO']),
            ],

            //========================
            // PUNTO VENTA
            //========================
            'punto-venta' => [
                'required',
                Rule::in(['SI', 'NO']),
            ],

        ];
    }

    /**
     * Messages
     */
    public function messages()
    {
        return [

            //========================
            // NAME
            //========================
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.string' => 'El nombre del rol debe ser texto válido.',
            'name.max' => 'El nombre del rol no puede superar los 50 caracteres.',
            'name.unique' => 'El nombre del rol ya se encuentra registrado.',

            //========================
            // SLUG
            //========================
            'slug.required' => 'El slug es obligatorio.',
            'slug.string' => 'El slug debe ser texto válido.',
            'slug.max' => 'El slug no puede superar los 50 caracteres.',
            'slug.unique' => 'El slug ya se encuentra registrado.',

            //========================
            // DESCRIPTION
            //========================
            'description.string' => 'La descripción debe ser texto válido.',
            'description.max' => 'La descripción no puede superar los 255 caracteres.',

            //========================
            // FULL ACCESS
            //========================
            'full-access.required' => 'Debe seleccionar una opción para Full Access.',
            'full-access.in' => 'La opción seleccionada en Full Access no es válida.',

            //========================
            // PUNTO VENTA
            //========================
            'punto-venta.required' => 'Debe seleccionar una opción para Punto de Venta.',
            'punto-venta.in' => 'La opción seleccionada en Punto de Venta no es válida.',

        ];
    }

    /**
     * Response JSON Validation
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
