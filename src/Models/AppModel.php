<?php

namespace IwslibLaravel\Models;

use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

abstract class AppModel extends BaseModel
{
    use SoftDeletes, HasUuids;

    public function getHistory(): ?HistoryModel
    {
        $historyName = static::class . 'History';


        $history = new $historyName;

        if ($history instanceof HistoryModel) {
            if (Auth::check()) {
                $id = Auth::id();
                $history->updated_by = $id;
                $history->created_by = $id;
            }
            return $history;
        } else {
            throw new Exception("履歴モデル不正");
        }
    }

    public function getChangeLogMessage($before, $after): ?string
    {
        return null;
    }

    public function setId(?string $uuid = null)
    {
        if ($this->id !== null) return;

        if ($uuid) {
            $this->id = $uuid;
        } else {
            $this->id = Str::uuid();
        }
    }
}
