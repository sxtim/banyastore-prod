<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Services\Basket\BasketInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BasketController extends Controller
{
    public function getBasket(BasketInterface $basket): JsonResponse
    {
        return response()->json([
            'items' => $basket->getBasket()
        ]);
    }

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

    public function remove(BasketInterface $basket, Request $request): JsonResponse
    {
        $basket->remove($request->input('product_id'));

        return response()->json([
            'items' => $basket->getBasket(),
            'count' => $basket->getCount()
        ]);
    }

    public function increment(BasketInterface $basket, Request $request): JsonResponse
    {
        $basket->increment($request->input('product_id'));

        return response()->json([
            'items' => $basket->getBasket(),
            'count' => $basket->getCount()
        ]);
    }

    public function decrement(BasketInterface $basket, Request $request): JsonResponse
    {
        $basket->decrement($request->input('product_id'));

        return response()->json([
            'items' => $basket->getBasket(),
            'count' => $basket->getCount()
        ]);
    }
}
