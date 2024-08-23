<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteUpdateRequest extends FormRequest
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
        $id = $this->route('id'); 

        return [
            'tipo_documento' => 'required',
            'documento' => [
                'required',
                'numeric',
                Rule::unique('clientes', 'documento')->where(function ($query) {
                    $query->whereIn('estado', ["ACTIVO"]);
                })->ignore($id)
            ],
            'nombre' => 'required',
            'zona' => 'required',
            'departamento' => 'required',
            'provincia' => 'required',
            'distrito' => 'required',
            'direccion' => 'required',
            'telefono_movil' => 'required|numeric',
            'activo' => 'required',
            'correo_electronico' => 'required|email',
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
            'provincia.required' => 'El campo Provincia es obligatorio',
            'zona.required' => 'El campo Zona es obligatorio',
            'distrito.required' => 'El campo Distrito es obligatorio',
            'direccion.required' => 'El campo Dirección completa es obligatorio',
            'telefono_movil.required' => 'El campo Teléfono móvil es obligatorio',
            'telefono_movil.numeric' => 'El campo Teléfono móvil debe ser numérico',
            'activo.required' => 'El campo Estado es obligatorio',
            'correo_electronico.required' => 'El campo Correo electrónico es obligatorio',
            'correo_electronico.email' => 'El campo Correo electrónico es de tipo Email (@).',
            'logo.image' => 'El campo Logo no contiene el formato imagen.',
            'logo.max' => 'El tamaño máximo del Logo para cargar es de 40 MB.',
        ];
    }
}
