<?php

namespace IwslibLaravel\Models;

abstract class ColumnName
{
    // 共通
    const ID = 'id';
    const UPDATED_AT = 'updated_at';
    const UPDATED_BY = 'updated_by';
    const CREATED_AT = 'created_at';
    const CREATED_BY = 'created_by';
    const DELETED_AT = 'deleted_at';
    const HISTORY_ID = 'history_id';

    // 業務
    const USER_ID = 'user_id';
    const EMAIL_ID = "email_id";
}
