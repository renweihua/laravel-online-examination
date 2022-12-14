<?php

namespace App\Models\System;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class IpBlacklist extends Model
{
    protected $is_delete = 0;

    const CACHE_KEY = 'cache:ip_blacklists';

    public static function getAllIPs(bool $force = false)
    {
        $key = self::CACHE_KEY;
        if ($force){
            Cache::delete($key);
        }
        return Cache::remember($key, Carbon::now()->addHours(1), function() {
            return self::get();
        });
    }
}
