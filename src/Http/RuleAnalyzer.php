<?php

namespace IwslibLaravel\Http;

use Illuminate\Support\Str;

class RuleAnalyzer
{

    static public  function convertToRegEx(array $rules)
    {
        $ret = [];
        foreach ($rules as  $name => $ruleList) {
            $pattern = '/^' . Str::replace('*', '\d+', Str::replace('.', '\.', $name)) . '$/';
            $ret[$pattern] = $ruleList;
        }
        return $ret;
    }

    private string $path;
    private string $pattern;

    private int|null $arrayIndex = null;

    private bool $mathed = false;

    private array $rules;

    public function __construct(string $path, array &$rules)
    {
        $this->path = $path;

        // パターンマッチング
        foreach ($rules as $pattern => $ruleList) {
            if (preg_match($pattern, $path, $matcheds)) {
                $this->pattern = $pattern;
                $this->rules = $ruleList;
                $this->mathed = true;
                break;
            }
        }

        if (!$this->mathed) {
            return;
        }

        // 配列インデックスの取得
        $this->arrayIndex = $this->getArrayIndexFromPath($path);
    }

    public function isMathed()
    {
        return $this->mathed;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function getType()
    {
        return $this->rules[1];
    }

    public function isArrayMember()
    {
        return $this->arrayIndex !== null;
    }

    public function getArrayIndex()
    {
        return $this->arrayIndex;
    }

    private function getArrayIndexFromPath(string $path)
    {

        preg_match('/^.+\.(\d+)\.[0-9A-Za-z_]+$/', $path, $matches);
        if (count($matches) === 0) {
            return null;
        }

        return intval($matches[1]);
    }

    public function getArrayName()
    {
        $list = explode('.*.', $this->pattern);
        array_pop($list);
        return implode('.*.');
    }
}
