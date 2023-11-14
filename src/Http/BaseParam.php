<?php

namespace IwslibLaravel\Http;

use IwslibLaravel\Util\DateUtil;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use ReflectionClass;

abstract class BaseParam implements IParam
{
    const REQUIRED = 'required';
    const NULLABLE = 'nullable';
    const STR = 'string';
    const NUMERIC = 'numeric';
    const DATE = 'date';
    const BOOLEAN_ = 'boolean';
    const IMAGE = 'image';
    const FILE = 'file';
    const ARRAY = 'array';

    const PARAM_NAME_TIMESTAMP = 'timestamp';

    private array $param = [];

    abstract public function rules(): array;

    public function __set($name, $value)
    {
        $name  = Str::snake($name);
        $rule = data_get($this->rules(), $name, null);
        if (!$rule) {
            throw new Exception('存在しないパラメータ ' . $name);
        }

        $this->param[$name] = $value;
    }

    public function __get($name)
    {
        return data_get($this->param, Str::snake($name), null);
    }

    public function toArray(bool $toCamelCase = false): array
    {
        if ($toCamelCase === false) {
            return $this->param;
        }

        $ret = [];
        foreach ($this->param as $key => $val) {
            $camelKey = Str::camel($key);
            $ret[$camelKey] = $val;
        }
        return $ret;
    }

    public function setData(array $data)
    {

        $dots = Arr::dot($data);

        $ruleRegExs = RuleAnalyzer::convertToRegEx($this->rules());

        // logger($ruleRegExs);

        foreach ($dots as $name => $value) {

            if ($value === null) {
                data_set($this->param, $name, null);
                continue;
            }

            $analyzer = new RuleAnalyzer($name, $ruleRegExs);

            if ($analyzer->isMathed()) {
                $content = $this->getSettableData($analyzer->getType(), $value);
                data_set($this->param, $name, $content);
            }
        }

        // logger($this->param);
    }

    private function getSettableData($rule, $value)
    {
        if (is_string($rule)) {
            if ($rule === self::STR) {
                return strval($value);
            }
            if ($rule === self::NUMERIC) {
                return intval($value);
            }
            if ($rule === self::BOOLEAN_) {
                return boolval($value);
            }
            if ($rule === self::DATE) {
                if (is_string($value)) {
                    return DateUtil::parse($value);
                } else {
                    return null;
                }
            }
            if ($rule === self::IMAGE || $rule === self::FILE) {
                return $value;
            }
            if ($rule === self::ARRAY) {
                return $value;
            }
        } elseif ($rule instanceof Enum) {
            // リフレクションを使ってEnumの型を取得する
            $ref = new ReflectionClass((get_class($rule)));
            $type = $ref->getProperty('type');
            $type->setAccessible(true);
            $enum = $type->getValue($rule);
            try {
                return $enum::tryFrom($value);
            } catch (Exception $e) {
                logs()->error('Enum パース失敗', ['rule' => $rule, 'value' => $value, 'exception' => $e->getMessage()]);
                throw $e;
            }
        }


        throw new Exception(sprintf("不正な変換 ",));
    }

    /**
     * 排他チェック
     *
     * @param Carbon|null $timestamp
     * @return boolean
     */
    public function checkTimestamp(Carbon|null $timestamp): bool
    {
        if ($timestamp === null) return true;

        $param = $this->__get(self::PARAM_NAME_TIMESTAMP);
        if ($param === null || !$param instanceof Carbon) {
            logger("無効なタイムスタンプ確認");
            logger($param);
            return false;
        }

        return $param->eq($timestamp);
    }

    private function isNullable(array|bool $condition, bool $nullable): bool
    {
        if (is_array($condition)) {
            return $nullable;
        } else {
            return $condition;
        }
    }

    protected function str(array|bool $condition = [], $nullable = false): array
    {
        $conditionEx = array_merge(is_array($condition) ? $condition : [], ['max:250']);

        return array_merge([
            $this->isNullable($condition, $nullable) ? self::NULLABLE : self::REQUIRED,
            self::STR
        ], $conditionEx);
    }
    protected function text(array|bool $condition = [], $nullable = false): array
    {
        return array_merge([
            $this->isNullable($condition, $nullable) ? self::NULLABLE : self::REQUIRED,
            self::STR
        ], is_array($condition) ? $condition : []);
    }
    protected function numeric(array|bool $condition = [], $nullable = false): array
    {
        return array_merge([
            $this->isNullable($condition, $nullable) ? self::NULLABLE : self::REQUIRED,
            self::NUMERIC
        ], is_array($condition) ? $condition : []);
    }
    protected function boolean(array|bool $condition = [], $nullable = false): array
    {
        return array_merge([
            $this->isNullable($condition, $nullable) ? self::NULLABLE : self::REQUIRED,
            self::BOOLEAN_
        ], is_array($condition) ? $condition : []);
    }
    protected function array(array|bool $condition = [], $nullable = false): array
    {
        return array_merge([
            $this->isNullable($condition, $nullable) ? self::NULLABLE : self::REQUIRED,
            self::ARRAY
        ], is_array($condition) ? $condition : []);
    }
    protected function date(array|bool $condition = [], $nullable = false): array
    {
        return array_merge([
            $this->isNullable($condition, $nullable) ? self::NULLABLE : self::REQUIRED,
            self::DATE
        ], is_array($condition) ? $condition : []);
    }
    protected function enum(array|bool $condition = [], $nullable = false): array
    {
        return array_merge([
            $this->isNullable($condition, $nullable) ? self::NULLABLE : self::REQUIRED,
        ], is_array($condition) ? $condition : []);
    }
    protected function image(array|bool $condition = [], $nullable = false): array
    {
        return array_merge([
            $this->isNullable($condition, $nullable) ? self::NULLABLE : self::REQUIRED,
            self::IMAGE
        ], is_array($condition) ? $condition : []);
    }
    protected function images(string $name, $nullable = false): array
    {
        $need = $nullable ? self::NULLABLE : self::REQUIRED;
        return [
            $name => [$need, self::ARRAY],
            sprintf("%s.*", $name) => [$need, self::IMAGE]
        ];
    }
    protected function file(array|bool $condition = [], $nullable = false): array
    {
        return array_merge([
            $this->isNullable($condition, $nullable) ? self::NULLABLE : self::REQUIRED,
            self::FILE
        ], is_array($condition) ? $condition : []);
    }

    protected function sortableRules()
    {
        return [
            'sort' => $this->str(true),
            'order' => $this->str(true),
            'limit' => $this->numeric(true),
        ];
    }

    protected function timestamp(bool $nullable = false)
    {
        return [self::PARAM_NAME_TIMESTAMP =>  $this->date($nullable)];
    }
}
