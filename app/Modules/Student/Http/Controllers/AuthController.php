<?php

namespace App\Modules\Student\Http\Controllers;

use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\UnauthorizedException;
use App\Models\OnlineExamination\Student;
use App\Modules\Student\Http\Requests\LoginRequest;

class AuthController extends StudentController
{
    public function login(LoginRequest $request)
    {
        $teacher = Student::where('student_no', $request->student_no)->first();
        if (!$teacher){
            throw new BadRequestException('学生号不存在或已删除！');
        }
        if (!hash_verify($request->password, $teacher->password)) throw new UnauthorizedException('认证失败！');
        return $this->successJson($teacher, '登录成功！');
    }
}
