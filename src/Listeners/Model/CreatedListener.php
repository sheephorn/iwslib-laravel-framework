<?php

namespace IwslibLaravel\Listeners\Model;

use IwslibLaravel\Events\Model\CreatedEvent;

class CreatedListener extends ModelListener
{
    protected const ACTION = '作成';

    public function handle(CreatedEvent $event): void
    {
        $this->handleEvent($event->model);
    }
}
