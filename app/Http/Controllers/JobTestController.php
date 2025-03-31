<?php
// app/Http/Controllers/JobTestController.php

namespace App\Http\Controllers;

use App\Jobs\TestJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class JobTestController extends Controller
{
    public function test()
    {
        // Отправляем тестовую задачу в очередь
        TestJob::dispatch();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Тестовая задача отправлена в очередь',
            'last_run' => Cache::get('last_test_job_run')
        ]);
    }
    
    public function status()
    {
        $lastRun = Cache::get('last_test_job_run');
        $status = $lastRun ? 'ok' : 'unknown';
        
        // Если последний запуск был более 30 минут назад, считаем что есть проблема
        if ($lastRun && now()->diffInMinutes($lastRun) > 30) {
            $status = 'warning';
        }
        
        return response()->json([
            'status' => $status,
            'last_run' => $lastRun,
            'minutes_ago' => $lastRun ? now()->diffInMinutes($lastRun) : null
        ]);
    }
}
