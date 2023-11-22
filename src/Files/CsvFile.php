<?php

namespace IwslibLaravel\Files;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use IwslibLaravel\Util\EncodingUtil;
use LogicException;

class CsvFile extends TmpFile
{

    const ENCODE_UTF8 = "UTF8";
    const ENCODE_SJIS = "SJIS";


    public function __construct(
        protected array $headers = [],
        protected string $encode = self::ENCODE_UTF8
    ) {
        parent::__construct();

        if (!in_array($encode, [static::ENCODE_UTF8, static::ENCODE_SJIS])) {
            throw new LogicException("エンコード指定不正:" . $encode);
        }

        if (count($headers) !== 0) {
            $this->addLine($headers);
        }
    }

    public function addLine(array|Collection $row, array|null $sortDef = null)
    {
        if ($sortDef !== null) {
            $row = $this->sortColumn($sortDef, $row);
        }

        $str = "";
        foreach ($row as $col => $val) {
            if ($str !== "") {
                $str .= ",";
            }

            $str .= $val;
        }

        if ($this->encode === static::ENCODE_SJIS) {
            $str = EncodingUtil::toSjisFromUtf8($str);
        }

        $this->append($str);
    }

    private function sortColumn(array $sortDef, $data): array
    {
        $ele = [];
        $notFound = Str::uuid();
        foreach ($sortDef as $def) {
            $ret = data_get($data, $def, $notFound);
            if ($ret === $notFound) {
                throw new LogicException("存在しない項目:" . $def);
            }
            $ele[] = $ret;
        }
        return $ele;
    }
}
