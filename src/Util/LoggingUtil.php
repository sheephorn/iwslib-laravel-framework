<?php

namespace IwslibLaravel\Util;

use Exception;
use Illuminate\Support\Facades\Log;

class LoggingUtil
{

    public static function debugException(Exception $e, string|array $messages = [])
    {
        Log::debug(self::getExceptionContents($e, $messages));
    }
    public static function infoException(Exception $e, string|array $messages = [])
    {
        Log::error(self::getExceptionContents($e, $messages));
    }
    public static function warnException(Exception $e, string|array $messages = [])
    {
        Log::error(self::getExceptionContents($e, $messages));
    }
    public static function errorException(Exception $e, string|array $messages = [])
    {
        Log::error(self::getExceptionContents($e, $messages));
    }

    private static function getExceptionContents(Exception $e, string|array $messages)
    {

        if (is_string($messages)) {
            $message = $messages;
            $messages = [];
            $messages[] = $message;
        }

        return [
            ...$messages,
            '_message' => $e->getMessage(),
            '_file' => $e->getFile(),
            '_line' => $e->getLine(),
            '_exceptionType' => $e::class,
        ];
    }
}
