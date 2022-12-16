<?php

namespace App\Modules\Student\Http\Requests;

use App\Modules\Admin\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'student_no' => 'required',
            'password'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'student_no.required' => '学生号为必填项！',
            'password.required'   => '登录密码为必填项！',
        ];
    }
}
