<?php

namespace IwslibLaravel\Listeners\Model;

use IwslibLaravel\Events\Model\UpdatingEvent;
use IwslibLaravel\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatingListener extends ModelListener
{
    protected const ACTION = '更新';

    public function handle(UpdatingEvent $event): void
    {
        // 更新者作成者の設定
        if (Auth::check()) {
            $id = Auth::id();
            $event->model->updated_by = $id;
            if ($event->model->created_by === null) {
                $event->model->created_by = $id;
            }
        }

        // ログインパスワードのハッシュ化
        if ($event->model instanceof User) {
            if ($event->model->isDirty(User::COL_NAME_PASSWORD)) {
                if ($event->model->password !== null) {
                    $event->model->password = Hash::make($event->model->password);
                }
            }
        }


        $this->handleEvent($event->model);
    }
}
