<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Illuminate\Contracts\View\View;

class OrderController extends Controller
{
    public function success(): View
    {
        return view('successful-order');
    }

    public function error(): View
    {
        return view('error-order');
    }
}
