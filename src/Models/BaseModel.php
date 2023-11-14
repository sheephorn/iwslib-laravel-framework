<?php

namespace IwslibLaravel\Models;

use IwslibLaravel\Events\Model\CreatedEvent;
use IwslibLaravel\Events\Model\CreatingEvent;
use IwslibLaravel\Events\Model\DeletedEvent;
use IwslibLaravel\Events\Model\UpdatingEvent;
use IwslibLaravel\Models\Feature\IModelFeature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class BaseModel extends Model implements IModelFeature
{
    use HasFactory;

    const COL_NAME_ID = ColumnName::ID;
    const COL_NAME_CREATED_BY = ColumnName::CREATED_BY;
    const COL_NAME_UPDATED_BY =  ColumnName::UPDATED_BY;
    const COL_NAME_CREATED_AT = ColumnName::CREATED_AT;
    const COL_NAME_UPDATED_AT = ColumnName::UPDATED_AT;
    const COL_NAME_DELETED_AT = ColumnName::DELETED_AT;

    protected $guarded = [
        self::COL_NAME_ID,
        self::COL_NAME_CREATED_BY,
        self::COL_NAME_UPDATED_BY,
        self::COL_NAME_CREATED_AT,
        self::COL_NAME_UPDATED_AT,
        self::COL_NAME_DELETED_AT,
    ];

    public static function getBuilder(string $name = 'main'): Builder
    {
        return DB::table(static::getTableName(), $name)
            ->whereNull($name . "." . static::COL_NAME_DELETED_AT);
    }

    public static function getTableName(): string
    {
        return (new static)->getTable();
    }

    public static function hasColumn(string $columnName): bool
    {
        $target = sprintf("%s::COL_NAME_%s", static::class, Str::upper($columnName));
        $ret = defined($target);
        return $ret;
    }


    public function copy(IModelFeature $from): static
    {
        $data = $from->getAttributeKeys();

        foreach ($data as $key) {
            $this->$key = $from->$key;
        }
        return $this;
    }

    public function getAttributeKeys(): array
    {
        return array_values(array_unique(array_merge(array_keys($this->attributesToArray()), $this->hidden)));
    }

    public function isNotSavedModel(): bool
    {
        return data_get($this, ColumnName::ID) === null;
    }

    protected $dispatchesEvents = [
        'creating' => CreatingEvent::class,
        'created' => CreatedEvent::class,
        'updating' => UpdatingEvent::class,
        'deleted' => DeletedEvent::class,
    ];


    // カラムが存在する項目のみfillするようオーバーライド
    public function fill(array $atr)
    {
        $filterd = array_filter($atr, function ($value, $key) {
            return static::hasColumn($key);
        }, ARRAY_FILTER_USE_BOTH);

        return parent::fill($filterd);
    }
}
