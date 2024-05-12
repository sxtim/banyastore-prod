<?php

namespace App\Http\Requests\Personal;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'pass' => ['required', 'min:8', 'regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/'],
        ];
    }

    public function messages(): array
    {
        return [
            'pass.required' => 'Пожалуйста, введите пароль',
            'pass.min' => 'Минимальное количество символов в пароле 6',
            'pass.regex' => 'Пароль должен содержать цифры и английские буквы в верхнем и в нижнем регистре',
        ];
    }


    protected function prepareForValidation()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->get('phone'));

        $this->merge([
            'phone' => $phone,
        ]);
    }
}
