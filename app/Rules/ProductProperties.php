<?php

namespace App\Rules;


use App\Models\Shop\Property\Property;
use Illuminate\Contracts\Validation\Rule;


class ProductProperties implements Rule
{
    private string $messageProperties;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->messageProperties = '';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $values
     * @return bool
     */
    public function passes($attribute, $values): bool
    {
        $noError = true;

        $listProperties = Property::with(['values'])->where('is_required', true)->get();
        foreach ($listProperties as $property) {
            $propError = true;
            foreach ($property->values as $valueProp) {
                if (isset($values) && in_array($valueProp->id, $values) === true) {
                    $propError = false;
                }
            }

            if ($propError === true) {
                $noError = false;
                $this->messageProperties .= 'Незаполнено обязательное свойство: ' .$property->name . '! ';
            }
        }

        return $noError;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->messageProperties;
    }
}
