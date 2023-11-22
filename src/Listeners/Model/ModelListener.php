<?php

namespace IwslibLaravel\Listeners\Model;

use IwslibLaravel\Models\ColumnName;
use IwslibLaravel\Models\Feature\IModelFeature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

abstract class ModelListener
{

    protected const ACTION = '-';

    protected function handleEvent(IModelFeature $model): void
    {
        // 履歴作成処理
        $this->createHistory($model);
    }

    protected function createHistory(IModelFeature $model)
    {
        $history =  $model->getHistory();
        $changeMessage = "";
        if ($history !== null) {
            $history->fillFromOrigin($model);
            $history->save();

            if ($model instanceof Model) {
                $before = $model->getOriginal();
                $after = $model;
                $message = $model->getChangeLogMessage($before, $after);
                if ($message !== null) {
                    $changeMessage = sprintf("[%s]", $message);
                }
            }
        }
        Log::info(sprintf(
            "モデル変更検知[%s][%s][ID:%s]%s",
            static::ACTION,
            $model->getModelName(),
            data_get($model, ColumnName::ID),
            $changeMessage
        ));
    }
}
