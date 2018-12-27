<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class GodfatherRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|digits:10',
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas deben coincidir',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'first_name.required' => 'Escriba su nombre por favor',
            'last_name.required' => 'Escriba su apellido por favor',
            'phone.required' => 'El numero de telefono es obligatorio',
            'phone.digits' => 'El numero de telefono debe contener unicamente digitos',
            'password_confirmation.required' => 'El campo de confirmacion de contraseña es obligatorio',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['status' => 'Error',
            'messages' => $this->formattedValidatorErrorsArray($validator)]), 422);
    }

    private function formattedValidatorErrorsArray($validator)
    {
        foreach ($validator->errors()->toArray() as $error) {
            $errors[] = $error;
        }
        return $errors;
    }

}
