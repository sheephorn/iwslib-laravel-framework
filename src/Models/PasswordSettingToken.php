<?php

namespace App\Models;

use IwslibLaravel\Models\Feature\UserId;
use IwslibLaravel\Util\DateUtil;
use Illuminate\Database\Eloquent\Builder;

class PasswordSettingToken extends AppModel
{
    use UserId;

    const COL_NAME_USER_ID = ColumnName::USER_ID;
    const COL_NAME_TOKEN = 'token';
    const COL_NAME_EXPIRES_AT = 'expires_at';

    protected $casts = [
        self::COL_NAME_EXPIRES_AT => 'datetime',
    ];

    public function getHistory(): ?HistoryModel
    {
        return null;
    }

    public function getModelName(): string
    {
        return "ログインパスワード設定トークン";
    }

    public function scopeExpiresIn(Builder $query)
    {
        return $query->where(self::COL_NAME_EXPIRES_AT, '>', DateUtil::now());
    }
}
