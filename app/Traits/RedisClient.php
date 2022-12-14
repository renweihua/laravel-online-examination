<?php

declare(strict_types = 1);

namespace App\Traits;

use Illuminate\Support\Facades\Redis;

trait RedisClient
{
    protected static $redis;

    protected static function getRedis($name = 'redis-lock')
    {
        if (!self::$redis){
            self::$redis = Redis::connection($name)->client();
        }
        return self::$redis;
    }
}
