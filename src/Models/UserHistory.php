<?php

namespace IwslibLaravel\Models;


class UserHistory extends HistoryModel
{
    public function getModelName(): string
    {
        return "ユーザー情報履歴";
    }
}
