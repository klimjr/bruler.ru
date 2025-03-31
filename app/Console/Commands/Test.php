<?php

namespace App\Console\Commands;

use App\Http\Controllers\DolyamiController;
use App\Livewire\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

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
        $test = new DolyamiController();
        $order = \App\Models\Order::first();
        $test->createOrder($order, '');
    }
}
