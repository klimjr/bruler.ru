<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductVariant;
use App\Models\Size;

class ProductAmountController extends Controller
{
    public function updateAmount(Request $request)
    {
        $token = $request->query('token');
        if ($token !== env('API_TOKEN')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $products = $request->all();
        unset($products['token']);

        $validator = Validator::make($products, [
            '*.Товар' => 'required|string',
            '*.Остаток' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($products as $product) {
            $article = $product['Товар'];
            $amount = $product['Остаток'];

            if (isset($product['Характеристики']) && $product['Характеристики']) {
                $size = Size::where('size', $product['Характеристики'])->first();

                if ($size) {
                    $sizeId = $size['id'];

                    ProductVariant::where('article', $article)
                        ->where('size_id', $sizeId)
                        ->update(['amount' => $amount]);
                }
            } else {
                ProductVariant::where('article', $article)
                    ->update(['amount' => $amount]);
            }
        }

        return response()->json(['success' => 'Amount updated successfully']);
    }
}
