<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class JobMonitorController extends Controller
{
    public function index()
    {
        // Получаем статистику из базы данных jobs
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        // Если используете Redis
        $redisQueues = [];
        if (class_exists('Redis')) {
            $queues = ['default', 'emails', 'notifications']; // Укажите ваши очереди
            foreach ($queues as $queue) {
                $redisQueues[$queue] = Redis::llen('queues:' . $queue);
            }
        }

        // Получаем последние 10 задач
        $recentJobs = DB::table('jobs')
            ->select('id', 'queue', 'payload', 'attempts', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.job-monitor', compact('pendingJobs', 'failedJobs', 'redisQueues', 'recentJobs'));
    }
}
