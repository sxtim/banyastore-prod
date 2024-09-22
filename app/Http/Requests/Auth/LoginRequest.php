<?php

namespace App\Http\Requests\Auth;

use App\Rules\Phone;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', new Phone()],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Необходимо указать пароль.',
            'phone.required' => 'Необходимо указать телефон.'
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
