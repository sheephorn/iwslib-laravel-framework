<?php

namespace IwslibLaravel\Models;

use IwslibLaravel\Events\Model\CreatedEvent;
use IwslibLaravel\Events\Model\CreatingEvent;
use IwslibLaravel\Events\Model\DeletedEvent;
use IwslibLaravel\Events\Model\UpdatingEvent;
use IwslibLaravel\Models\Feature\IModelFeature;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property ?string id ユーザーID
 * @property  ?string email Email
 * @property  ?string password ログインパスワード
 */
class User extends Authenticatable implements IModelFeature
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes;

    const COL_NAME_ID = 'id';
    const COL_NAME_EMAIL = 'email';
    const COL_NAME_PASSWORD = 'password';

    const COL_NAME_CREATED_BY = ColumnName::CREATED_BY;
    const COL_NAME_UPDATED_BY =  ColumnName::UPDATED_BY;
    const COL_NAME_CREATED_AT = ColumnName::CREATED_AT;
    const COL_NAME_UPDATED_AT = ColumnName::UPDATED_AT;
    const COL_NAME_DELETED_AT = ColumnName::DELETED_AT;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        self::COL_NAME_PASSWORD,
    ];

    protected $guarded = [
        self::COL_NAME_ID,
        self::COL_NAME_CREATED_BY,
        self::COL_NAME_UPDATED_BY,
        self::COL_NAME_CREATED_AT,
        self::COL_NAME_UPDATED_AT,
        self::COL_NAME_DELETED_AT,
    ];


    protected $dispatchesEvents = [
        'creating' => CreatingEvent::class,
        'created' => CreatedEvent::class,
        'updating' => UpdatingEvent::class,
        'deleted' => DeletedEvent::class,
    ];

    public static function getBuilder(string $name = 'main'): Builder
    {
        return DB::table(static::getTableName(), $name);
    }

    public static function getTableName(): string
    {
        return (new static)->getTable();
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

    public function getHistory(): ?HistoryModel
    {
        return new UserHistory();
    }

    public function getModelName(): string
    {
        return "ログインユーザー情報";
    }

    public function getChangeLogMessage($before, $after): ?string
    {
        return null;
    }
}
