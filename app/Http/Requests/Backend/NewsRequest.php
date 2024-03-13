<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
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
            'main_image' => 'image|max:2000', //2 MB
            'preview_image' => 'image|max:2000', //2 MB
            'sort' => 'numeric',
            'detail_text' => 'required',
            'preview_text' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Необходимо ввести название новости',
            'main_image.required' => 'Необходимо загрузить изображение',
            'main_image.image' => 'Главное изображение: ожидается файл форматов jpg, png или gif',
            'main_image.max' => 'Главное изображение: максимальный размер файла 2Мб',
            'preview_image.image' => 'Превью изображение: ожидается файл форматов jpg, png или gif',
            'preview_image.max' => 'Превью изображение: максимальный размер файла 2Мб',
            'sort.numeric' => 'Укажите сортировку числом',
            'detail_text.required' => 'Необходимо ввести детальный текст новости',
            'preview_text.required' => 'Необходимо ввести краткое описание новости',
        ];
    }
}
