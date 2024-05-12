<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personal\PasswordRequest;
use App\Http\Requests\Personal\PersonalRequest;
use App\Models\Shop\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class PersonalController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        return view('personal.index',compact('user'));
    }

    public function update(int $id, PersonalRequest $request): RedirectResponse
    {
        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email')
        ]);

        return redirect()->route('personal.index')->with('success', 'Данные обновлены');
    }

    public function password(int $id, PasswordRequest $request): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($request->input('pass') !== $request->input('newpass')) {
            return redirect()->route('personal.index')->withErrors(['Не соответствие паролей']);
        }

        $user->update([
            'password' => Hash::make($request->input('pass'))
        ]);

        return redirect()->route('personal.index')->with('success', 'Данные обновлены');
    }

    public function favorites(): View
    {
        $user = User::findOrFail(Auth::user()->id);

        $products = Product::with(['favorites'])->whereHas('favorites', function ($query) use ($user){
            return $query->where('user_id', '=', $user->id);
        })->get();

        return view('personal.favorites',compact('products'));
    }
}
