<?php

namespace IwslibLaravel\Http;

abstract class Rule
{
    public static function email(): array
    {
        $ret = [];
        $ret[] = "email:strict,filter,dns";
        $ret[] = "max:255";
        return $ret;
    }
}
