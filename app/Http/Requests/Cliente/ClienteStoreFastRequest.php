<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class ClienteStoreFastRequest extends FormRequest
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
        $rules = [
            'tipo_documento'    => 'required',
            'documento'         => ['required','numeric', Rule::unique('clientes','documento')->where(function ($query) {
                $query->whereIn('estado',["ACTIVO"]);
            })],
            'nombre'            => 'required',
            'tipo_cliente_id'   => 'required',
            'departamento'      => 'required',
            'zona'              => 'required',
            'provincia'         => 'required',
            'distrito'          => 'required',
            'direccion'         => 'required',
            'telefono_movil'    => 'required|numeric',
            'activo'            => 'required',
        ];

        return $rules;
    }


    public function messages(){
        $messages = [
            'tipo_documento.required'   => 'El campo Tipo de documento es obligatorio.',
            'tipo_cliente_id.required'  => 'El campo Tipo de cliente es obligatorio.',
            'documento.required'        => 'El campo Nro. Documento es obligatorio',
            'documento.unique'          => 'El Nro. documento ya está registrado.',
            'documento.numeric'         => 'El campo Nro. Documento debe ser numérico',
            'departamento.required'     => 'El campo Departamento es obligatorio',
            'zona.required'             => 'El campo Zona es obligatorio',
            'provincia.required'        => 'El campo Provincia es obligatorio',
            'distrito.required'         => 'El campo Distrito es obligatorio',
            'direccion.required'        => 'El campo direccion es obligatorio',
            'telefono_movil.required'   => 'El campo telefono movil es obligatorio',
            'activo.required'           => 'El campo Estado es obligatorio',
        ];

        return $messages;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   =>  false,
            'message'   =>  'Errores de validación al crear Cliente.',
            'errors'    =>  $validator->errors()
        ], 422));
    }

}
