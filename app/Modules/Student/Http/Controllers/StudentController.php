<?php

namespace App\Modules\Student\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OnlineExamination\Course;

class StudentController extends Controller
{
    protected $service;

    // 课程列表
    public function getCourses()
    {
        $course = Course::getCourses();
        return $this->successJson($course);
    }
}
