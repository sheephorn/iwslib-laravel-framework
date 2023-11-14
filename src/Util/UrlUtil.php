<?php

namespace IwslibLaravel\Util;

class UrlUtil
{

    /**
     * 画面のＵＲＬを生成する
     *
     * @param array|string $path
     * @return string
     */
    public static function getAppUrl(array|string $path, array $query = []): string
    {
        $elements = [config("app.url")];
        if (is_array($path)) {
            $elements =  array_merge($elements, $path);
        } else {
            $elements[] = $path;
        }

        $url = implode("/", $elements);

        if (!!$query) {
            $url .= "?";
            $queryStrList = [];
            foreach ($query as $key => $value) {
                $queryStrList[] = sprintf("%s=%s", $key, $value);
            }
            $url .= implode("&", $queryStrList);
        }

        return $url;
    }
}
