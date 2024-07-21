<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
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
        $data = [
            'name' => ['required'],
            'sort' => ['required'],
            'image' => ['image','max:2000']
        ];

        if($this->method() !== 'PATCH') {
            $data['image'] = ['required','image','max:2000'];
        }
        return $data;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Необходимо ввести название баннера',
            'sort.required' => 'Необходимо указать значение сортировки',
            'image.required' => 'Необходимо загрузить изображение',
            'image.image' => 'изображение: ожидается файл форматов jpg, png или gif',
            'image.max' => 'изображение: максимальный размер файла 2Мб'
        ];
    }
}
