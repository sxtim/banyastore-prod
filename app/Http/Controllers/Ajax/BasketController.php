<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Services\Basket\BasketInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BasketController extends Controller
{
    public function getCount(BasketInterface $basket): JsonResponse
    {
        return response()->json([
            'count' => $basket->getCount()
        ]);
    }

    public function add(BasketInterface $basket, Request $request): JsonResponse
    {
        $basket->add($request->input('product_id'));

        return response()->json([
            'count' => $basket->getCount()
        ]);
    }
}
