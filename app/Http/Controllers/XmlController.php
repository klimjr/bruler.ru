<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleXMLElement;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;

class XmlController extends Controller
{
    public function generateXml($sale = null)
    {
        $allProducts = [];
        $products = Product::query()
            ->when($sale, function ($query) {
                $query->where('discount', '>', 0)
                    ->orWhereNotNull('final_price');
            })
            ->get();

        foreach ($products as $product) {
            if ($product->type != Product::TYPE_CERTIFICATE) {
                $available = ProductVariant::where('product_id', $product->id)->where('amount', '>', 0)->exists() ? 'true' : 'false';
                $allProducts['offer id="' . $product->id . '" available="' . $available . '"'] = [
                    'name' => $product->name . ' Bruler',
                    'vendor' => 'Bruler',
                    'url' => env('APP_URL') . '/' . Category::where('id', $product->category_id)->pluck('slug')->first() . '/' . $product->slug,
                    'currencyId' => 'RUB',
                    'categoryId' => $product->category_id,
                    'picture' => env('APP_URL') . '/storage/' . $product->image,
                    'description' => $product->description
                ];

                $discountSum = 0;
                $newPrice = 0;

                if ($product->discount && $product->discount != 0) {
                    $discountSum = ($product->price / 100) * $product->discount;
                    $newPrice = $product->price - $discountSum;

                    $allProducts['offer id="' . $product->id . '" available="' . $available . '"']['price'] = $newPrice;
                    $allProducts['offer id="' . $product->id . '" available="' . $available . '"']['oldprice'] = $product->price;
                } else {
                    $allProducts['offer id="' . $product->id . '" available="' . $available . '"']['price'] = $product->price;
                }
            }
        }

        $data = [
            'shop' => [
                'name' => 'Bruler',
                'company' => 'ООО «БРУЛЕР»',
                'url' => env('APP_URL'),
                'currencies' => [
                    'currency id="RUB" rate="1"' => ''
                ],
                'categories' => [
                    'category id="1"' => 'Футболки',
                    'category id="2"' => 'Худи',
                    'category id="3"' => 'Зипы',
                    'category id="4"' => 'Анораки',
                    'category id="5"' => 'Брюки',
                    'category id="6"' => 'Подарочные сертификаты',
                    'category id="7"' => 'Аксессуары',
                    'category id="8"' => 'Джинсы',
                    'category id="9"' => 'Брюки',
                    'category id="10"' => 'Рубашки',
                    'category id="11"' => 'Свитера',
                ],
                'delivery' => true,
                'offers' => $allProducts
            ]
        ];

        $dateNow = Carbon::now();

        // Создание корневого элемента XML
        $xml = new SimpleXMLElement('<yml_catalog date="' . $dateNow->format('Y-m-d\TH:i:sP') . '" />');

        // Функция для добавления данных в XML
        $this->arrayToXml($data, $xml);

        // Путь к файлу в публичной директории
        $filePath = public_path('yandex_products_feed.xml');
        if($sale) {
            $filePath = public_path('yandex_products_sale_feed.xml');
        }

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->encoding = 'UTF-8';
        $dom->formatOutput = true;
        file_put_contents($filePath, $dom->saveXML());

        return response($dom->saveXML(), 200)
            ->header('Content-Type', 'application/xml');
    }

    // Функция для рекурсивного преобразования массива в XML
    private function arrayToXml(array $data, SimpleXMLElement &$xml)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Проверка, если ключ имеет атрибуты
                if (preg_match('/(\w+)(.*)/', $key, $matches)) {
                    $subnode = $xml->addChild($matches[1]);
                    // Добавление атрибутов, если они есть
                    if (!empty($matches[2])) {
                        preg_match_all('/(\w+)="([^"]+)"/', $matches[2], $attributes);
                        foreach ($attributes[1] as $index => $attrName) {
                            $subnode->addAttribute($attrName, $attributes[2][$index]);
                        }
                    }
                } else {
                    $subnode = $xml->addChild($key);
                }
                $this->arrayToXml($value, $subnode);
            } else {
                if (preg_match('/(\w+)(.*)/', $key, $matches)) {
                    $subnode = $xml->addChild($matches[1], htmlspecialchars($value));
                    // Добавление атрибутов, если они есть
                    if (!empty($matches[2])) {
                        preg_match_all('/(\w+)="([^"]+)"/', $matches[2], $attributes);
                        foreach ($attributes[1] as $index => $attrName) {
                            $subnode->addAttribute($attrName, $attributes[2][$index]);
                        }
                    }
                } else {
                    $xml->addChild($key, htmlspecialchars($value));
                }
            }
        }
    }
}
