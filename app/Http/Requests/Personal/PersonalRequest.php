<?php

namespace App\Http\Requests\Personal;

use App\Rules\Phone;
use Illuminate\Foundation\Http\FormRequest;

class PersonalRequest extends FormRequest
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
            'name' => ['required', 'min:3'],
            'phone' => ['required', 'numeric', 'unique:users,phone,'.$this->route('id'), new Phone()],
            'email' => ['required', 'email', 'unique:users,email,'.$this->route('id')],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Пожалуйста, введите адрес электронной почты',
            'email.unique' => 'Данный адрес электронной почты уже зарегистрирован',
            'name.required' => 'Необходимо заполнить поле Имя',
            'name.min' => 'Минимальное количество символов в имени: 3',
            'phone.required' => 'Необходимо заполнить поле Номер телефона',
            'phone.numeric' => 'В номере телефона не должно быть букв',
            'phone.unique' => 'Номер уже зарегистрирован',
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
