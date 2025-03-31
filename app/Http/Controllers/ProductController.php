<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Set;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $minPriceProduct;
    protected $maxPriceProduct;

    public function __construct()
    {
        $this->minPriceProduct = Product::min('price');
        $this->maxPriceProduct = Product::max('price');
    }

    public function catalog()
    {
        $products = Product::query()
            ->where('show', true)
            ->orderBy('sort')
            ->get();
        return view('catalog', compact('products'));
    }

    public function getKit($slug = null)
    {
        $kits = Set::query()
            ->when($slug, function ($query) use ($slug) {
                return $query->where('slug', $slug);
            })
            ->orderBy('position')->get();
        $kits_count = $kits->count();

        $productIds = $kits->pluck('products')->flatten()->toArray();
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->orderByRaw(sprintf('FIELD(id, %s)', implode(',', $productIds)))
            ->get();
        return view('kits', compact('products', 'kits_count', 'slug'));
    }

    public function list(Request $request, Category $category)
    {
        $products = $category->products()->paginate(10);
        return view('products', compact('products', 'category'));
    }

    public function viewProduct(Request $request, Category $category, Product $product)
    {
        return view('product', compact('product'));
    }

    public function filterProducts(Request $request)
    {
        $categoriesAndProducts = Category::with([
            'products' => function ($query) use ($request) {
                $this->applyFilters($query, $request);
            }
        ])->get()->mapWithKeys(function ($category) {
            $sortedProducts = $category->products
                ->sortBy(function ($product) {
                    return $product->variants->every(function ($variant) {
                        return $variant->amount == 0;
                    }) ? 1 : 0;
                })
                ->sortByDesc(function ($product) {
                    return $product->discount > 0 ? 1 : 0;
                })
                ->sortBy(function ($product) {
                    return !$product->preorder && !$product->new;
                })
                ->sortByDesc(function ($product) {
                    return $product->new ? 1 : 0;
                })
                ->sortByDesc(function ($product) {
                    return $product->preorder ? 1 : 0;
                });

            return [$category->name => $sortedProducts];
        });

        $totalFilteredProducts = $categoriesAndProducts->flatten()->count();

        return view('collection', [
            'categoriesAndProducts' => $categoriesAndProducts,
            'totalFilteredProducts' => $totalFilteredProducts,
            'minPriceProduct' => $this->minPriceProduct,
            'maxPriceProduct' => $this->maxPriceProduct,
        ]);
    }

    public function getCollectionsAndProducts()
    {
        $collectionsAndProducts = Collection::with('products')->get();

        $totalFilteredProducts = Product::count();

        return view('collection', [
            'collectionsAndProducts' => $collectionsAndProducts,
            'totalFilteredProducts' => $totalFilteredProducts,
            'minPriceProduct' => $this->minPriceProduct,
            'maxPriceProduct' => $this->maxPriceProduct,
        ]);
    }

    public function getCategoriesAndProducts($collection)
    {
        $categoriesAndProducts = Category::with([
            'products' => function ($query) use ($collection) {
                $this->applyCollectionFilter($query, $collection);
            }
        ])->get()->mapWithKeys(function ($category) {
            return [$category->name => $category->products];
        });

        $totalFilteredProducts = $categoriesAndProducts->flatten()->count();

        return view('collection', [
            'categoriesAndProducts' => $categoriesAndProducts,
            'totalFilteredProducts' => $totalFilteredProducts,
            'minPriceProduct' => $this->minPriceProduct,
            'maxPriceProduct' => $this->maxPriceProduct,
        ]);
    }

    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('in_stock') && $request->filled('to_order')) {
        } else {
            if ($request->filled('in_stock')) {
                $query->where('preorder', false)
                    ->whereHas('variants', function ($q) {
                        $q->where('amount', '>', 0);
                    });
            }

            if ($request->filled('to_order')) {
                $query->where('preorder', true);
            }
        }

        if ($request->filled('price-from')) {
            $query->where('price', '>=', $request->input('price-from'));
        }

        if ($request->filled('price-to')) {
            $query->where('price', '<=', $request->input('price-to'));
        }

        if ($request->filled('category')) {
            $query->whereIn('category_id', $request->input('category'));
        }

        if ($request->filled('technology')) {
            $query->whereIn('technology_id', $request->input('technology'));
        }

        if ($request->filled('color')) {
            $colors = $request->input('color');
            $query->whereHas('variants', function ($q) use ($colors) {
                $q->whereIn('color_id', $colors);
            });
        }

        if ($request->filled('size')) {
            $sizes = $request->input('size');
            $query->whereHas('variants', function ($q) use ($sizes) {
                $q->whereIn('size_id', $sizes);
            });
        }
    }

    protected function applyCollectionFilter($query, $collection)
    {
        switch ($collection) {
            case 'sale':
                $query->whereNotNull('discount')->where('discount', '!=', 0);
                break;
            case 'new':
                $query->where('new', true);
                break;
            case 'beachclb':
                $query->where('collection', 2);
                break;
            case is_numeric($collection):
                $query->where('collection', $collection);
                break;
            default:
                break;
        }
    }
}
