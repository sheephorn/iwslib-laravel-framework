<?php

namespace IwslibLaravel\Util;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteHelper
{

    const ENTRY = 'entry';

    static public function get(string $url, string $class)
    {
        return Route::get($url, [$class, self::ENTRY])->name(self::routeName($class));
    }

    static public function post(string $url, string $class)
    {
        return Route::post($url, [$class, self::ENTRY])->name(self::routeName($class));
    }

    static public function server(string $url, string $class)
    {
        return Route::post($url, [$class, self::ENTRY])->name(self::routeName($class));
    }

    static public function routeName(string $class)
    {
        $ele = explode('\\', $class);
        $controllerName = array_pop($ele);
        $groupName = array_pop($ele);
        $routeName =  Str::replaceLast('Controller', '', $groupName .  $controllerName);
        return $routeName;
    }

    static public function getPath(string $controllerClassName, array $param = [])
    {
        return route(self::routeName($controllerClassName), $param);
    }

    static public function webRoute(string $route)
    {
        return Str::replaceFirst('/api', '', $route);
    }
}
