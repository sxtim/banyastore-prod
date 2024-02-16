<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ShopController extends Controller
{

    public function index(): View
    {
        return view('backend.shop.index');
    }
}
