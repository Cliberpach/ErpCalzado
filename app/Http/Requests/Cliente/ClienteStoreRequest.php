<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tipo_documento' => 'required',
            'documento' => ['required', 'numeric', Rule::unique('clientes', 'documento')->where(function ($query) {
                $query->whereIn('estado', ["ACTIVO"]);
            })],
            'nombre' => 'required',
            'tipo_cliente' => 'required',
            'departamento' => 'required',
            'zona' => 'required',
            'provincia' => 'required',
            'distrito' => 'required',
            'direccion' => 'required',
            'telefono_movil' => 'required|numeric',
            'activo' => 'required',
            'direccion_negocio' => 'nullable',
            'logo' => 'image|mimetypes:image/jpeg,image/png,image/jpg|max:40000|required_if:estado_fe,==,on',
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
            'tipo_documento.required' => 'El campo Tipo de documento es obligatorio.',
            'documento.required' => 'El campo Nro. Documento es obligatorio',
            'documento.unique' => 'El campo Nro. Documento debe ser único',
            'documento.numeric' => 'El campo Nro. Documento debe ser numérico',
            'departamento.required' => 'El campo Departamento es obligatorio',
            'zona.required' => 'El campo Zona es obligatorio',
            'provincia.required' => 'El campo Provincia es obligatorio',
            'distrito.required' => 'El campo Distrito es obligatorio',
            'direccion.required' => 'El campo Dirección completa es obligatorio',
            'telefono_movil.required' => 'El campo Teléfono móvil es obligatorio',
            'telefono_movil.numeric' => 'El campo Teléfono móvil debe ser numérico',
            'activo.required' => 'El campo Estado es obligatorio',
            'logo.image' => 'El campo Logo no contiene el formato imagen.',
            'logo.max' => 'El tamaño máximo del Logo para cargar es de 40 MB.',
        ];
    }
}
