<?php

namespace App\Modules\Teacher\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OnlineExamination\Course;

class TeacherController extends Controller
{
    protected $service;

    // Vue格式的课程列表
    public function getFormatCoursesByVue()
    {
        $course = Course::formatCoursesByVue();
        return $this->successJson($course);
    }
}
