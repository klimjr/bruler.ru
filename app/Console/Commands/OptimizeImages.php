<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $optimizerChain = OptimizerChainFactory::create();

        $images_products = Storage::files('public/products');
        $this->info('Optimizing ' . count($images_products) . ' images');

        foreach ($images_products as $image) {
            $this->info('Optimizing ' . $image);
            $storePath = storage_path('app/' . $image);
            $optimizerChain->optimize($storePath);
        }

        $images_product_variants = Storage::files('public/product-variants');
        $this->info('Optimizing ' . count($images_product_variants) . ' images');

        foreach ($images_product_variants as $image) {
            $this->info('Optimizing ' . $image);
            $storePath = storage_path('app/' . $image);
            $optimizerChain->optimize($storePath);
        }
    }
}
