<?php

namespace App\Http\Requests\Ajax;

use App\Rules\Phone;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FeedbackRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3'],
            'phone' => ['required', new Phone()],
            'mess' => ['required', 'max:15000', 'min:3']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Необходимо указать имя',
            'name.min' => 'Минимальное количество символов в имени: 3',
            'phone.required' => 'Необходимо указать телефон',
            'mess.required' => 'Необходимо текст сообщения',
            'mess.min' => 'Минимальное количество символов в сообщении: 3',
            'mess.max' => 'Максимальное количество символов в сообщении: 15000',
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
