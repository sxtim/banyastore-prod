<?php

namespace App\Listeners\Auth;


use App\Models\Basket\SessionBasket;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        /** @var User $user */
        $user = $event->user;

        $sessionCart = SessionBasket::where('session_uid','=', Session::get(SessionBasket::SESSION_UID_NAME))->first();

        $basket = $user->basket;

        if ($sessionCart && $sessionCart->products()->first()) {

            $newCartItems = [];

            foreach ($sessionCart->products()->get() as $cartItem) {
                if ($basket && $basket->products->where('product_id','=',$cartItem->product_id)->first()) {
                    $basket
                        ->products
                        ->where('product_id','=',$cartItem->product_id)
                        ->first()
                        ->update([
                            'quantity' => $cartItem->quantity,
                        ]);
                } else {
                    $newCartItems[] = [
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                    ];
                }


            }

            try {
                $basket
                    ->products()
                    ->createMany($newCartItems);
            } catch (\Throwable $e) {
                Log::error($e->getMessage());
            }

        //    $sessionCart->products()->delete();
        //    $sessionCart->touch();
        }


    }
}
