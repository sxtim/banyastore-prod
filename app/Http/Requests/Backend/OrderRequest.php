<?php

namespace App\Http\Requests\Backend;

use App\Rules\Phone;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderRequest extends FormRequest
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
            'name' => ['required'],
            'phone' => ['required', new Phone()],
            'mail' => ['required', 'email'],
            'city_name' => 'required',
            'street' => ['required'],
            'house' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Необходимо указать имя',
            'phone.required' => 'Необходимо указать телефон',
            'mail.required' => 'Необходимо указать почту',
            'mail.email' => 'Неверно указана почта',
            'city_name.required' => 'Необходимо указать город',
            'street.required' => 'Необходимо указать улицу',
            'house.required' => 'Необходимо указать Строение/Дом'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()->all(),
            'status' => 422,
        ], 422));
    }
}
