<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'El campo de email es requerido',
            'email.email' => 'Por favor ingrese un email valido',
            'password.required' => 'El campo de password es obligatorio',
            'password.confirmed' => 'La contraseña y la confirmacion de contraseña deben ser iguales',
        ];
    }
}
