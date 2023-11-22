<?php

namespace IwslibLaravel\Listeners\Model;

use IwslibLaravel\Events\Model\CreatingEvent;
use IwslibLaravel\Models\User;
use Illuminate\Support\Facades\Hash;

class CreatingListener extends ModelListener
{
    protected const ACTION = '作成中';

    public function handle(CreatingEvent $event): void
    {
        // ログインパスワードのハッシュ化
        if ($event->model instanceof User) {
            $event->model->password = Hash::make($event->model->password);
        }
    }
}
