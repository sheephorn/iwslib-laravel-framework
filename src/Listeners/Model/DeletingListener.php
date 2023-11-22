<?php

namespace IwslibLaravel\Listeners\Model;

use IwslibLaravel\Events\Model\DeletingEvent;

class DeletingListener extends ModelListener
{
    protected const ACTION = '削除';

    public function handle(DeletingEvent $event): void
    {
        $this->handleEvent($event->model);
    }
}
