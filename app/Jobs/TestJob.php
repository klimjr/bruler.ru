<?php
// app/Jobs/TestJob.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        // Записываем время выполнения
        $now = now();
//        Log::info('TestJob выполнена в ' . $now);
        Cache::put('last_test_job_run', $now->toDateTimeString(), 60 * 24); // хранить 24 часа
    }
}
