<?php

namespace App\Modules\Teacher\Http\Controllers;

use App\Exceptions\HttpStatus\UnauthorizedException;
use App\Models\OnlineExamination\Teacher;
use App\Modules\Teacher\Http\Requests\LoginRequest;

class AuthController extends TeacherController
{
    public function login(LoginRequest $request)
    {
        $teacher = Teacher::where('teacher_no', $request->teacher_no)->first();
        if (!$teacher){
            throw new UnauthorizedException('教工号不存在或已删除！');
        }
        if (!hash_verify($request->password, $teacher->password)) throw new UnauthorizedException('认证失败！');
        return $this->successJson($teacher, '登录成功！');
    }
}
