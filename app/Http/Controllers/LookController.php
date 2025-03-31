<?php

namespace App\Http\Controllers;

use App\Models\Look;
use App\Models\Product;

class LookController extends Controller
{
    public function index($slug)
    {
        $look = Look::where('slug', $slug)->first();
        if (!$look) {
            abort(404);
        }
        $products = collect();
        if($look->products) {
            $products = Product::query()
                ->whereIn('id', $look->products)
                ->get();
        }
        return view('look', compact('look', 'products', 'slug'));
    }
}
