<?php

namespace IwslibLaravel\Providers;

use Illuminate\Contracts\Foundation\Application;
use IwslibLaravel\Codes\EnvironmentName;
use IwslibLaravel\Codes\QueueName;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use IwslibLaravel\Console\Commands\HeartBeat;

class AppServiceProvider extends ServiceProvider
{

    public $singletons = [
        \IwslibLaravel\Util\DBUtil::class
    ];



    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // マイグレーション
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // ビュー
        $this->loadViewsFrom(__DIR__ . '/../resources/views/emails', 'iwsliblaravel-emails');

        //Queue関連
        Queue::before(function (JobProcessing $event) {
            // Logのドライバー設定
            $queueName = $event->job->getQueue();
            if ($queueName === QueueName::EMAIL->value) {
                Log::setDefaultDriver('queue-email');
            } else if ($queueName === QueueName::JOB->value) {
                Log::setDefaultDriver('queue-job');
            }
        });

        if (request() !== null) {
            Log::setDefaultDriver('web');
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment(EnvironmentName::LOCAL->value)) {
            // IDEヘルパー登録
            if (class_exists(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class)) {
                $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            }
        }

        // Artisanコマンド登録
        $this->app->singleton("command.iwslib.heatbeat", function (Application $app) {
            return new HeartBeat();
        });

        $this->commands(
            "command.iwslib.heatbeat",
        );

        // 設定ファイル
        // ログ関連
        if (config("logging.channels.web") === null) {
            config(["logging.channels.web" => [
                'driver' => 'daily',
                'path' => storage_path('logs/web.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 14,
                'permission' => 0666,
            ]]);
        }
        if (config("logging.channels.batch") === null) {
            config(["logging.channels.batch" => [
                'driver' => 'daily',
                'path' => storage_path('logs/batch.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 14,
                'permission' => 0666,
            ]]);
        }
        if (config("logging.channels.queue-email") === null) {
            config(["logging.channels.queue-email" => [
                'driver' => 'daily',
                'path' => storage_path('logs/email.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 14,
                'permission' => 0666,
            ]]);
        }
        if (config("logging.channels.queue-job") === null) {
            config(["logging.channels.queue-job" => [
                'driver' => 'daily',
                'path' => storage_path('logs/job.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 14,
                'permission' => 0666,
            ]]);
        }
    }
}
