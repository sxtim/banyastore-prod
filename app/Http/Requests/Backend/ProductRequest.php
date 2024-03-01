<?php

namespace App\Http\Requests\Backend;

use App\Rules\ProductProperties;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'category' => 'required',
            'price' => 'required|numeric',
            'image' => 'image|max:1000', //2 MB
            'sort' => 'numeric',
            'properties' => new ProductProperties()
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Необходимо ввести название товара',
            'category.required' => 'Необходимо выбрать категорию',
            'price.required' => 'Необходимо ввести стоимость товара',
            'price.numeric' => 'Стоимость товара должна быть числом',
            'image.required' => 'Необходимо загрузить изображение',
            'image.image' => 'Изображение: ожидается файл форматов jpg, png или gif',
            'image.max' => 'Изображение: максимальный размер файла 1Мб',
            'sort.numeric' => 'Укажите сортировку числом',
        ];
    }
}
