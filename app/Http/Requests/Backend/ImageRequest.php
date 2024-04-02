<?php

namespace App\Http\Requests\Backend;

use App\Rules\ProductProperties;
use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
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
            'image' => 'image|max:2000', //2 MB
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => 'Изображение: ожидается файл форматов jpg, png или gif',
            'image.max' => 'Изображение: максимальный размер файла 2Мб',
        ];
    }
}
