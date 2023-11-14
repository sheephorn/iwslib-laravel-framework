<?php

namespace IwslibLaravel\Util;

use Illuminate\Support\Carbon;

class DateUtil
{
    public static function now()
    {
        if (!app()->environment('local')) {
            return new Carbon();
        }

        $nowStr = self::getConfig();

        if ($nowStr !== null && $nowStr !== '') {
            $date = new Carbon($nowStr);
            if ($date->isValid()) {
                return new Carbon();
                return $date;
            }
        }
        return new Carbon();
    }

    public static function parse(?string $source): Carbon|null
    {
        if ($source === null) {
            return null;
        }
        $date = Carbon::parse($source);
        if ($date->isValid()) {
            return $date->timezone(config('app.timezone'));
        }
        return null;
    }


    private static function getConfig()
    {
        return config('date.now', null);
    }
}
