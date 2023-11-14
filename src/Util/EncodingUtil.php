<?php

namespace IwslibLaravel\Util;

class EncodingUtil
{

    public static function toUtf8FromSjis(string $source)
    {
        return mb_convert_encoding($source, "UTF8", 'SJIS');
    }
    public static function toSjisFromUtf8(string $source)
    {
        return mb_convert_encoding($source, "SJIS", 'UTF8');
    }
}
