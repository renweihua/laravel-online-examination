<?php

namespace App\Models\OnlineExamination;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Course extends Model
{
    protected $primaryKey = 'course_id';
    protected $is_delete  = 0;
    const CACHE_KEY = 'courses';

    public static function getCourses($force = false)
    {
        $cache_key = self::CACHE_KEY;
        $data = Cache::get($cache_key);
        if (empty($data) || $force){
            $data = self::get();
            Cache::put($cache_key, $data, Carbon::now()->addMonths(1));
        }
        return $data;
    }

    public static function formatCoursesByVue()
    {
        $courses = self::getCourses();
        $data = [];
        foreach ($courses as $item){
            $data[] = [
                'key' => $item->course_id,
                'label' => $item->course_name,
            ];
        }
        return $data;
    }
}
