<?php

namespace App\Http\Requests\Seguridad\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'usuario' => mb_strtoupper(trim($this->usuario ?? ''), 'UTF-8'),
            'email'   => mb_strtoupper(trim($this->email ?? ''), 'UTF-8'),
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'usuario' => [
                'required',
                Rule::unique('users', 'usuario')->where(function ($query) {
                    $query->whereIn('estado', ['ACTIVO', 'ANULADO']);
                })->ignore($id),
            ],
            'colaborador_id'   => 'required',
            'email'            => [
                'required',
                Rule::unique('users', 'email')->where(function ($query) {
                    $query->whereIn('estado', ['ACTIVO', 'ANULADO']);
                })->ignore($id),
            ],
            'password'         => 'required',
            'confirm_password' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'usuario.required'          => 'El campo usuario es obligatorio.',
            'usuario.unique'            => 'El campo usuario debe ser único.',
            'colaborador_id.required'   => 'El campo colaborador es obligatorio.',
            'email.required'            => 'El campo email es obligatorio.',
            'email.unique'              => 'El campo email debe ser único.',
            'password.required'         => 'El campo contraseña es obligatorio.',
            'confirm_password.required' => 'Debe confirmar la contraseña.',
            'confirm_password.same'     => 'Las contraseñas no coinciden.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
