<?php

namespace App\Http\Requests\Mantenimiento\Empresa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EmpresaUpdateRequest extends FormRequest
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
        $id = $this->route('id');

        return [

            // EMPRESA
            'ruc' => ['required', 'digits:11', "unique:empresas,ruc,$id"],
            'razon_social' => ['required', 'string', 'max:191'],
            'razon_social_abreviada' => ['nullable', 'string', 'max:191'],

            // UBICACION
            'direccion_fiscal' => ['required', 'string'],
            'direccion_llegada' => ['required', 'string'],

            'departamento' => [
                'required',
                Rule::exists('departamentos', 'id')
            ],

            'provincia' => [
                'required',
                Rule::exists('provincias', 'id')
            ],

            'distrito' => [
                'required',
                Rule::exists('distritos', 'id')
            ],

            'urbanizacion' => ['required', 'string', 'max:150'],
            'cod_local' => ['required', 'string', 'max:150'],

            // CONTACTO
            'correo' => ['nullable', 'email', 'max:191'],
            'telefono' => ['nullable', 'string', 'max:191'],
            'celular' => ['nullable', 'string', 'max:191'],

            // REPRESENTANTE
            'dni_representante' => ['required', 'digits:8'],
            'nombre_representante' => ['required', 'string', 'max:191'],

            // SUNARP
            'num_partida' => ['required', 'string', 'max:191'],
            'num_asiento' => ['required', 'string', 'max:191'],

            // REDES
            'facebook' => ['nullable', 'string', 'max:191'],
            'instagram' => ['nullable', 'string', 'max:191'],
            'web' => ['nullable', 'string', 'max:191'],

            // CONFIG
            'igv' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['required', 'in:ACTIVO,ANULADO'],
            'estado_fe' => ['required', 'in:0,1'],

            // LOGO
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [

            // EMPRESA
            'ruc.required' => 'El RUC es obligatorio',
            'ruc.digits' => 'El RUC debe tener 11 dígitos',
            'ruc.unique' => 'El RUC ya está registrado',

            'razon_social.required' => 'La razón social es obligatoria',
            'razon_social.max' => 'La razón social es demasiado larga',

            // UBICACION
            'direccion_fiscal.required' => 'La dirección fiscal es obligatoria',
            'direccion_llegada.required' => 'La dirección de planta es obligatoria',

            'departamento.required' => 'Debe seleccionar un departamento',
            'departamento.exists' => 'El departamento seleccionado no es válido',

            'provincia.required' => 'Debe seleccionar una provincia',
            'provincia.exists' => 'La provincia seleccionada no es válida',

            'distrito.required' => 'Debe seleccionar un distrito',
            'distrito.exists' => 'El distrito seleccionado no es válido',

            'urbanizacion.required' => 'La urbanización es obligatoria',
            'cod_local.required' => 'El código de local es obligatorio',

            // CONTACTO
            'correo.email' => 'El correo no es válido',

            // REPRESENTANTE
            'dni_representante.required' => 'El DNI del representante es obligatorio',
            'dni_representante.digits' => 'El DNI debe tener 8 dígitos',

            'nombre_representante.required' => 'El nombre del representante es obligatorio',

            // SUNARP
            'num_partida.required' => 'El número de partida es obligatorio',
            'num_asiento.required' => 'El número de asiento es obligatorio',

            // CONFIG
            'igv.numeric' => 'El IGV debe ser numérico',

            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado seleccionado no es válido',

            'estado_fe.required' => 'El estado de facturación es obligatorio',
            'estado_fe.in' => 'El estado de facturación no es válido',

            // LOGO
            'logo.image' => 'El archivo debe ser una imagen',
            'logo.max' => 'La imagen no debe pesar más de 2MB',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
