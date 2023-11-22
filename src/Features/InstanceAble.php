<?php

namespace IwslibLaravel\Features;

trait InstanceAble
{
    /**
     * @return static
     */
    public static function instance()
    {
        return app()->make(self::class);
    }
}
