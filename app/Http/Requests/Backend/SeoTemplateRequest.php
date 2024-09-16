<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class SeoTemplateRequest extends FormRequest
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
            'text_template' => ['required'],
            'type_material' => ['required'],
            'type_template' => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'text_template.required' => 'Необходимо ввести текст шаблона',
            'type_material.required' => 'Необходимо указать материал шаблона',
            'type_template.required' => 'Необходимо указать тип шаблона'
        ];
    }
}
