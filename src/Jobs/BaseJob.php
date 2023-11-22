<?php

namespace IwslibLaravel\Jobs;

use IwslibLaravel\Util\DBUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

abstract class BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected DBUtil $db;

    public function handle()
    {


        $this->db = DBUtil::instance();

        $needTransaction = !$this->db->isBeginning();

        try {
            if ($needTransaction) {
                $this->db->beginTransaction();
            }

            $this->logConfig();
            $this->handleJob();

            if ($needTransaction) {
                $this->db->commit();
            }
        } catch (Exception $e) {
            if ($needTransaction) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * ジョブを再試行する前に待機する秒数を計算
     */
    public function backoff(): int
    {
        return 60;
    }

    abstract protected function handleJob();

    private  function logConfig()
    {
        Log::withContext([
            '__job_class__' => static::class,
            '__job_uuid__' => strval(Str::uuid()),
        ]);
    }
}
