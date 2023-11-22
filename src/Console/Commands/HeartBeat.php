<?php

namespace IwslibLaravel\Console\Commands;

use Illuminate\Console\Scheduling\Schedule;

class HeartBeat extends BaseCommand
{
    static public function schedule(Schedule $schedule)
    {
        $schedule->command(self::class)
            ->everyFiveMinutes()
            ->evenInMaintenanceMode()
            ->description("ハートビート");
        $schedule->command(self::class, ['--maintenance'])
            ->everyMinute()
            ->evenInMaintenanceMode()
            ->description("メンテナンスモード確認");
    }


    protected $signature = "iwslib:heatbeat {--maintenance}";

    protected bool $outputInfoForBase = false;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ハートビート';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function service(): int
    {
        if ($this->option('maintenance')) {
            $isMaintenanceMode = app()->isDownForMaintenance();
            if ($isMaintenanceMode) {
                $this->outputWarn("down for maintenance");
            }
        } else {
            $this->outputInfo("heart beat");
        }
        return self::RESULTCODE_SUCCESS;
    }
}
