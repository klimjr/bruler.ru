{{--@extends('layouts.admin')--}}

{{--@section('content')--}}
<div class="container">
    <h1>Мониторинг задач</h1>

    <div class="card mb-4">
        <div class="card-header">Статистика очередей</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Задачи в БД</h5>
                    <p>Ожидающие: <strong>{{ $pendingJobs }}</strong></p>
                    <p>Неудачные: <strong>{{ $failedJobs }}</strong></p>
                </div>

                @if(count($redisQueues) > 0)
                <div class="col-md-6">
                    <h5>Redis очереди</h5>
                    @foreach($redisQueues as $queue => $count)
                        <p>{{ $queue }}: <strong>{{ $count }}</strong></p>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Последние задачи</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Очередь</th>
                        <th>Попытки</th>
                        <th>Создана</th>
                        <th>Данные</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentJobs as $job)
                    <tr>
                        <td>{{ $job->id }}</td>
                        <td>{{ $job->queue }}</td>
                        <td>{{ $job->attempts }}</td>
                        <td>{{ $job->created_at }}</td>
                        <td>
                            <button class="btn btn-sm btn-info" data-toggle="collapse" data-target="#job-{{ $job->id }}">
                                Показать
                            </button>
                            <div id="job-{{ $job->id }}" class="collapse mt-2">
                                <pre>{{ json_decode($job->payload, true) }}</pre>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{--@endsection--}}
