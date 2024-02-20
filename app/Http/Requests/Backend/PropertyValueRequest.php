<?php

namespace App\Http\Requests\Backend;

use App\Models\Shop\Property\PropertyValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyValueRequest extends FormRequest
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
            'name' => ['required', Rule::unique('property_values','name')->where('property_id', $this->route()->parameter('propertyId')) ]
        ];

        if($this->method() == 'PATCH') {
            $propertyValueId = $this->route()->parameter('propertyValueId');
            $propertyValue = PropertyValue::findOrFail($propertyValueId);
            $data = [
                'name' => [
                    'required',
                    Rule::unique('property_values','name')->ignore($propertyValueId)->where('property_id', $propertyValue->property_id) ]
            ];
        }

        return $data;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Необходимо ввести значение свойства',
            'name.unique' => 'У этого свойства уже есть такое значение'
        ];
    }
}
