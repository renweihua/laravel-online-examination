<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        Log::info('schedule:run');

        // $schedule->command('inspire')->hourly();
        // 每月1号调用：按月分表自动生成
        $schedule->command('command:autotablebuild')->monthlyOn();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        /**
         * 自动加载多模块的自定义命令行
         */
        $modules_path = config('modules.paths.modules');
        if ($dirs = get_dir_files($modules_path)){
            foreach ($dirs as $dir){
                if (is_dir($console_path = $modules_path . '/' . $dir . '/Console'))
                    $this->load($console_path = $modules_path . '/' . $dir . '/Console');
            }
        }

        require base_path('routes/console.php');
    }
}
