<?php

namespace IwslibLaravel\Models\Feature;

use IwslibLaravel\Models\User;

trait UserId
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
