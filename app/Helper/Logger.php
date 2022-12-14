<?php

namespace App\Helper;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

class Logger
{
    protected static $log;

    public static function get($name = 'laravel')
    {
        self::$log = new MonologLogger($name);

        // 创建mysql文件夹
        $dir_path = dirname(dirname(__DIR__)) . '/storage/logs/' . self::$log->getName();
        if (!is_dir($dir_path)) mkdir($dir_path, 0755);

        // 年月的日志目录
        $year_path = $dir_path . '/' . date('Y');
        if (!is_dir($year_path)) mkdir($year_path, 0755);

        $month_path = $year_path . '/' . date('m');
        if (!is_dir($month_path)) mkdir($month_path, 0755);

        $log_path  = storage_path('logs/' . self::$log->getName() . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '.log');
        if (!file_exists($log_path)) {
            fopen($log_path, "w");
        }

        self::$log->pushHandler(new StreamHandler($log_path, MonologLogger::INFO));
        return self::$log;
    }
}
