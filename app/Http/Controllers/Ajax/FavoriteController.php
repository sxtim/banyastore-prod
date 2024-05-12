<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Shop\Product;
use App\Services\FavoritesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function getData(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'products' => [],
            ]);
        }

        return response()->json([
            'products' => Product::with(['favorites'])->whereHas('favorites', function ($query) use ($user){
                return $query->where('user_id', '=', $user->id);
            })->get(),
        ]);
    }

    public function product(Request $request, FavoritesService $favoritesService): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'no auth',
            ]);
        }

        $product = Product::findOrFail($request->input('product_id'));
        $favoritesService->favorite($user->id, $product);

        return response()->json([
            'status' => 'success'
        ]);
    }
}
