<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ReadyFashionController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 4); // Количество продуктов, по умолчанию 4
        $collection = Product::take($limit)->get();

        $image = [
            'url' => asset('images/fashion/01JENYEG2FN9W7NYX2YGGZ7HJ2.jpg'),
            'alt' => 'Описание изображения',
        ];

        return view('ready-fashion', [
            'image' => $image,
            'collection' => $collection,
        ]);
    }
}
