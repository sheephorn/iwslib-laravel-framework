<?php

namespace IwslibLaravel\Models;


class EmailAttachment extends AppModel
{
    public function getModelName(): string
    {
        return "Email添付ファイル";
    }

    public function getHistory(): ?HistoryModel
    {
        return null;
    }
}
