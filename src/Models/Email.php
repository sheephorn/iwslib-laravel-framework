<?php

namespace IwslibLaravel\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Email extends AppModel
{
    const COL_NAME_SEND_DATETIME = "send_datetime";

    public function getModelName(): string
    {
        return "Email";
    }

    public function emailAttachments(): HasMany
    {
        return $this->hasMany(EmailAttachment::class);
    }

    protected $casts = [
        self::COL_NAME_SEND_DATETIME => 'datetime',
    ];
}
