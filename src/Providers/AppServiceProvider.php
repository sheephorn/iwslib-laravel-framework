<?php

namespace IwslibLaravel\Providers;

use IwslibLaravel\Codes\EnvironmentName;
use IwslibLaravel\Codes\QueueName;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{

    public $singletons = [
        \IwslibLaravel\Util\DBUtil::class
    ];


    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        logger("booted");

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->environment(EnvironmentName::LOCAL->value)) {
            // IDEヘルパー登録
            if (class_exists(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class)) {
                $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            }
        }

        //Queue関連
        Queue::before(function (JobProcessing $event) {
            // Logのドライバー設定
            $queueName = $event->job->getQueue();
            if ($queueName === QueueName::EMAIL->value) {
                Log::setDefaultDriver('queue-email');
            } else if ($queueName === QueueName::JOB->value) {
                Log::setDefaultDriver('queue-job');
            }

            Log::withContext([
                '__job_uuid__' => strval(Str::uuid()),
            ]);
        });


        //デフォルトルート登録
        $this->routes(function () {
            Route::middleware('web')
                ->group(__DIR__ . '/../routes/web.php');
        });
    }
}
