<?php

namespace IwslibLaravel\Models;

use IwslibLaravel\Models\Feature\IModelFeature;
use Illuminate\Database\Eloquent\Collection;

abstract class HistoryModel extends BaseModel
{
    const COL_NAME_HISTORY_ID = ColumnName::HISTORY_ID;

    protected $primaryKey = ColumnName::HISTORY_ID;

    /**
     * @param string $id
     * @return Collection<static>
     */
    public static function findById(string $id)
    {
        return static::query()->where(ColumnName::ID, $id)
            ->orderBy(ColumnName::CREATED_AT)
            ->get();
    }

    public function fillFromOrigin(IModelFeature $originModel)
    {
        return $this->copy($originModel);
    }

    public function getHistory(): ?HistoryModel
    {
        return null;
    }

    public function getChangeLogMessage($before, $after): ?string
    {
        return null;
    }
}
