<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'sort' => 'numeric',
            'image' => 'image|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Необходимо ввести название категории',
            'sort.numeric' => 'Укажите сортировку числом',
            'image.image' => 'Изображение: ожидается файл форматов jpg, png или gif',
            'image.max' => 'Изображение: максимальный размер файла 1Мб'
        ];
    }
}
