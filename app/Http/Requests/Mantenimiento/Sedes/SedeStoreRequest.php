<?php

namespace App\Http\Requests\Mantenimiento\Sedes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class SedeStoreRequest extends FormRequest
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
            'telefono'          =>  'nullable|max:20',
            'correo'            =>  'nullable|email|max:160',
            'departamento'      =>  'required|max:10',
            'provincia'         =>  'required|max:10',
            'distrito'          =>  'required|max:10',
            'codigo_local'      =>  'required|max:10',
            'serie'             =>  'required|alpha_num|size:3'
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'direccion.required'         => 'La dirección es obligatoria.',
            'direccion.max'              => 'La dirección no puede exceder los 100 caracteres.',
            'telefono.max'               => 'El teléfono no puede exceder los 20 caracteres.',
            'correo.email'               => 'El correo debe ser una dirección de correo válida.',
            'correo.max'                 => 'El correo no puede exceder los 160 caracteres.',
            'departamento.required'      => 'El departamento es obligatorio.',
            'departamento.max'           => 'El departamento no puede exceder los 10 caracteres.',
            'provincia.required'        => 'La provincia es obligatoria.',
            'provincia.max'             => 'La provincia no puede exceder los 10 caracteres.',
            'distrito.required'         => 'El distrito es obligatorio.',
            'distrito.max'              => 'El distrito no puede exceder los 10 caracteres.',
            'codigo_local.required'     => 'El código local es obligatorio.',
            'codigo_local.max'          => 'El código local no puede exceder los 10 caracteres.',
            'serie.required'            => 'La serie es obligatoria',
            'serie.alpha_num'           =>  'La serie solo permite letras y números',
            'serie.size'                =>  'La serie debe tener 3 caracteres'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   =>  false,
            'message'   =>  'Errores de validación al crear sede.',
            'errors'    =>  $validator->errors()
        ], 422));
    }
}
