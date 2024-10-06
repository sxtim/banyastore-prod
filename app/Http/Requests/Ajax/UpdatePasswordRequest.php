<?php

namespace App\Http\Requests\Ajax;

use App\Rules\Phone;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
            'password_confirmation' => ['required'],
            'token' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Необходимо указать электронный адрес',
            'email.email' => 'Неверно указан электронный адрес',
            'exists.exists' => 'Неверно указан электронный адрес',
            'password.required' => 'Необходимо указать пароль',
            'password_confirmation.required' => 'Необходимо подвтердить пароль',
            'token.required' => 'Нет токена',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()->all(),
            'status' => 422,
        ], 422));
    }

    protected function prepareForValidation()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->get('phone'));

        $this->merge([
            'phone' => $phone,
        ]);
    }
}
