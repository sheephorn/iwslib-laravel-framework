<?php

namespace IwslibLaravel\Listeners\Model;

use IwslibLaravel\Events\Model\DeletedEvent;

class DeletedListener extends ModelListener
{
    protected const ACTION = '削除';

    public function handle(DeletedEvent $event): void
    {
        $this->handleEvent($event->model);
    }
}
