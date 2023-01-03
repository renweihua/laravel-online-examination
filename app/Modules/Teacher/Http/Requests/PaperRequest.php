<?php

namespace App\Modules\Teacher\Http\Requests;

use App\Modules\Admin\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class PaperRequest extends BaseRequest
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
            'paper_name' => 'required',
            'paper_duration'   => 'required',
            'paper_difficulty'   => 'required',
            'paper_config'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'course_id.required' => '试卷科目为必选项！',
            'paper_name.required' => '试卷名称为必填项！',
            'paper_duration.required'   => '请设置考试时长(s)！',
            'paper_difficulty.required'   => '请设置试卷难度级别！',
            'paper_config.required'   => '请设置试卷配置！',
        ];
    }
}
