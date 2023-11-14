<?php

namespace IwslibLaravel\Util;

use IwslibLaravel\Features\InstanceAble;
use Illuminate\Support\Facades\DB;
use LogicException;

class DBUtil
{
    use InstanceAble;

    private bool $isBeginning = false;

    public function __destruct()
    {
        if ($this->isBeginning) {
            $this->rollBack();
        }
    }

    public  function beginTransaction(): void
    {
        if ($this->isBeginning) {
            throw new LogicException("２重トランザクション開始検知");
        }

        DB::beginTransaction();
        $this->isBeginning = true;
    }

    public  function commit(): void
    {
        if (!$this->isBeginning) {
            throw new LogicException("無効なコミット検知");
        }
        DB::commit();
        $this->isBeginning = false;
    }

    public function rollBack(): void
    {
        if (!$this->isBeginning) {
            throw new LogicException("無効なロールバック検知");
        }

        DB::rollBack();
        logs()->warning("ロールバック検知");
        $this->isBeginning = false;
    }

    public function isBeginning(): bool
    {
        return $this->isBeginning;
    }
}
