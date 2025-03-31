<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promocode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
  public function getFavourites(Request $request)
  {
    $user = Auth::user();

    if ($user) {
      $products = $user->favourites;
    } else {
      $favourites = session()->get('favourites', []);
      $products = Product::whereIn('id', $favourites)->get();
    }

    return view('profile.favourites', compact('products'));
  }

  public function getOrders(Request $request)
  {
    $user = Auth::user();
    $orders = $user->orders->map(function ($order) {
      $order->products = $this->getProducts($order);
      $order->db_products = $this->getDBProducts($order);
      return $order;
    });
    return view('profile.orders.index', compact('orders'));
  }

  public function getOrder(Request $request, Order $order)
  {
    $order->products = $this->getProducts($order);
    $order->db_products = $this->getDBProducts($order);
    $order->user = User::find($order->user_id);

    if (isset($order->promocode)) {
      $_promocode = Promocode::where('code', $order->promocode)->first();
      if ($_promocode)
        $order->promocode = $_promocode;
    }

    return view('profile.orders.order', compact('order'));
  }

  private function getProducts(Order $order)
  {
    return collect($order->products)->map(function ($product) {
      $db_product = Product::find($product['id']);
      if ($db_product) {
        $product_variant = isset($product['variant']) ? ProductVariant::find($product['variant']) : '';

        $price = 0;

        if (isset($product['type']) && $product['type'] === Product::TYPE_CERTIFICATE) {
          $price = $product['certificate']['price'];
        } else {
          if (isset($db_product->discount)) {
            $price = $db_product->getDiscountedPrice();
          } else {
            $price = $product['price'];
          }
        }

        $product_color = $product_variant ? $product_variant->color : '';
        $product_size = $product_variant ? $product_variant->size : '';
        $image_url = $product_variant ? $product_variant->getImageUrlAttribute() : $db_product->getImageUrlAttribute();

        return [
          'id' => $product['id'],
          'name' => $db_product->name,
          'type' => $product['type'] ?? Product::TYPE_PRODUCT,
          'price' => $price,
          'color' => $product_color,
          'size' => $product_size,
          'image_url' => $image_url,
          'quantity' => $product['quantity'],
        ];
      }
    })->filter();
  }

  private function getDBProducts(Order $order)
  {
    return collect($order->products)->map(function ($product) {
      $_product = Product::find($product['id']);
      if (isset($_product))
        $_product->image = $_product->image ? asset('storage/' . $_product->image) : '';
      return $_product;
    })->filter();
  }
}
