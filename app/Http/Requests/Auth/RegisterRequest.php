<?php

namespace App\Http\Requests\Auth;

use App\Rules\Phone;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3'],
            'surname' => ['required', 'min:3'],
            'phone' => ['required', 'numeric', new Phone(),'unique:users,phone'],
            'email' => ['required', 'email', 'unique:users,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Необходимо указать имя.',
            'name.min' => 'Минимальное количество символов в имени: 3.',
            'surname.required' => 'Необходимо указать фамилию.',
            'surname.min' => 'Минимальное количество символов в фамилии: 3.',
            'phone.required' => 'Необходимо указать телефон.',
            'phone.unique' => 'Нверно указан телефон.',
            'email.required' => 'Необходимо указать почту.',
            'email.email' => 'Неверно указана почта.',
            'email.unique' => 'Неверно указана почта.',
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
