<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequest extends FormRequest
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
            'name' => 'required',
            'discount' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Необходимо ввести название скидки',
            'discount.required' => 'Необходимо ввести значение скидки',
        ];
    }
}
