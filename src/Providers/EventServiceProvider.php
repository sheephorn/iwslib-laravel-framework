<?php

namespace IwslibLaravel\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use IwslibLaravel\Events\Model\CreatedEvent;
use IwslibLaravel\Events\Model\CreatingEvent;
use IwslibLaravel\Events\Model\DeletedEvent;
use IwslibLaravel\Events\Model\DeletingEvent;
use IwslibLaravel\Events\Model\UpdatingEvent;
use IwslibLaravel\Listeners\Model\CreatedListener;
use IwslibLaravel\Listeners\Model\CreatingListener;
use IwslibLaravel\Listeners\Model\DeletedListener;
use IwslibLaravel\Listeners\Model\DeletingListener;
use IwslibLaravel\Listeners\Model\UpdatingListener;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        // モデル関連
        CreatingEvent::class => [CreatingListener::class],
        CreatedEvent::class => [CreatedListener::class],
        UpdatingEvent::class => [UpdatingListener::class],
        DeletingEvent::class => [DeletingListener::class],
        DeletedEvent::class => [DeletedListener::class],
    ];
}
