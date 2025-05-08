<?php

namespace App\Http\Requests\Pedidos\Pedido;

use App\Ventas\Pedido;
use Illuminate\Foundation\Http\FormRequest;

class PedidoUpdateRequest extends FormRequest
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
        $pedidoId       = $this->route('id');
        
        $pedido         = Pedido::findOrFail($pedidoId);
        $estadoPedido   = $pedido->estado;

        $rules = [
            'condicion_id'      => 'required|exists:condicions,id,estado,ACTIVO',
            'fecha_propuesta'   => 'required|date',
            'cliente'           => 'required|exists:clientes,id,estado,ACTIVO',
        ];

        if ($estadoPedido === 'PENDIENTE') {
            $rules['almacen'] = 'required|exists:almacenes,id,estado,ACTIVO';
        } elseif ($estadoPedido === 'ATENDIENDO') {
            $rules['almacen'] = 'nullable|exists:almacenes,id,estado,ACTIVO';
        }

        return $rules;
    }

    /**
     * Get the custom error messages for the validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'almacen.required'  => 'El campo almacén es obligatorio cuando el estado del pedido es PENDIENTE.',
            'almacen.exists'    => 'El almacén seleccionado no existe o no está activo.',
            
            'condicion_id.required' => 'El campo condición es obligatorio.',
            'condicion_id.exists'   => 'La condición seleccionada no existe o no está activa.',

            'fecha_propuesta.required'  => 'El campo fecha propuesta es obligatorio.',
            'fecha_propuesta.date'      => 'El campo fecha propuesta debe ser una fecha válida.',

            'cliente.required'      => 'El campo cliente es obligatorio.',
            'cliente.exists'        => 'El cliente seleccionado no existe o no está activo.',
        ];
    }

       /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Devuelve una respuesta JSON con los errores de validación
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'success'   =>  false,
            'errors' => $validator->errors()
        ], 422));
    }
}
