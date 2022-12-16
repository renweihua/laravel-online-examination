<?php

namespace App\Modules\Teacher\Http\Requests;

use App\Models\OnlineExamination\QuestionBank;
use App\Modules\Admin\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class QuestionIdRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $questionBankModel = QuestionBank::getInstance();
        return [
            'question_id' => [
                'required',
                Rule::exists($questionBankModel->getTable(), $questionBankModel->getKeyName()),
            ],
        ];
    }

    public function messages()
    {
        return [
            'question_id.required' => '请指定题库！',
            'question_id.exists'   => '请指定有效的题库！',
        ];
    }
}
