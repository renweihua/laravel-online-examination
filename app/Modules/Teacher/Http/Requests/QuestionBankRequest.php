<?php

namespace App\Modules\Teacher\Http\Requests;

use App\Modules\Admin\Http\Requests\BaseRequest;

class QuestionBankRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'course_id' => 'required',
            'question_type' => 'required',
            'question_content'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'course_id.required' => '试卷科目为必选项！',
            'question_type.required' => '请选择题库种类！',
            'question_content.required'   => '请输入题目！',
        ];
    }
}
