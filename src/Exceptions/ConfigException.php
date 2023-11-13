<?php

namespace IwslibLaravel\Exceptions;

use Exception;

class ConfigException extends Exception
{
    public function __construct(string $key, $value)
    {
        parent::__construct("設定エラー: key:" . $key . " value:" . $value);
    }
}
