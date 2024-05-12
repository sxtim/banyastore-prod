<?php

namespace App\Services;


use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Model;

class FavoritesService
{

    /**
     * Модели доступные для избранного
     */
    const FAVORITES_MODELS = [
        Product::class
    ];

    /**
     * @throws \Exception
     */
    public function favorite(int $userId, Model $model): void
    {
        $isValid = $this->modelValidate($model);

        if (!$isValid) {
            throw new \Exception('Model not found in array');
        }

        $model->favorite($userId);

    }

    /**
     * @throws \Exception
     */
    private function modelValidate(Model $model): bool
    {
        $isValid = in_array(get_class($model),self::FAVORITES_MODELS);

        return $isValid;
    }
}
