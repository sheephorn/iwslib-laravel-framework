<?php

namespace IwslibLaravel\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class BaseCommand extends Command
{

    const RESULTCODE_SUCCESS = 0;
    const RESULTCODE_WARN = 1;
    const RESULTCODE_FAILED = 2;
    const COMMAND = "no-define";

    /**
     * ベースのログを出力するか
     *
     * @var boolean
     */
    protected bool $outputInfoForBase = true;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    abstract protected function service(): int;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ret = 0;

        $this->boot();
        try {
            $ret = $this->service();
        } catch (Exception $e) {
            $message = sprintf("例外発生:%s:%s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
            $this->outputError($message, $e->getTrace());
            $ret = self::RESULTCODE_FAILED;
        }


        if ($ret === self::RESULTCODE_SUCCESS) {
            $this->outputInfoForBase("成功しました。");
        } else  if ($ret === self::RESULTCODE_WARN) {
            $this->outputWarn("一部失敗があります。");
        } else  if ($ret === self::RESULTCODE_FAILED) {
            $this->outputError("失敗しました");
        } else {
            $this->outputError(sprintf("未定義のエラーコード:%d", $ret));
        }

        return $ret;
    }

    private function boot()
    {
        Log::setDefaultDriver("batch");
        Log::withContext([
            '__scheduleId' => strval(Str::uuid()),
            '__user__' => exec('whoami'),
            ...$this->arguments(),
        ]);
        $this->outputInfoForBase(sprintf("バッチ起動 %s", $this->getCommandName()));
    }

    protected function outputInfo(string $message, array $context = [])
    {
        Log::info($message, $this->getContext($context));
        $this->info($message);
    }
    private function outputInfoForBase(string $message, array $context = [])
    {
        if ($this->outputInfoForBase) {
            Log::info($message, $this->getContext($context));
        }
        $this->info($message);
    }
    protected function outputWarn(string $message, array $context = [])
    {
        Log::warning($message, $this->getContext($context));
        $this->warn($message);
    }
    protected function outputError(string $message, array $context = [])
    {
        Log::error($message, $this->getContext($context));
        $this->error($message);
    }
    private function getContext(array $context = [])
    {
        return array_merge($context, ["context" => $this->arguments()]);
    }

    protected function getCommandName(): string
    {
        return "";
    }
}
