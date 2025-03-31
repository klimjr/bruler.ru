<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Product::all()->each(function ($product) {
            $new_gallery = $product->gallery;
            if (count($new_gallery) !== 0) {
                foreach ($new_gallery as &$item) {
                    $new_images = [];
                    if (count($item['images']) !== 0) {
                        foreach ($item['images'] as $imageKey => $image) {
                            $new_images[$imageKey] = [
                                'image' => $image,
                                'alt' => 'alt'
                            ];
                        }
                        $item['images'] = $new_images;
                    }
                }
                unset($item);
                $product->gallery = $new_gallery;
                $product->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
