<?php

namespace App\Models\OnlineExamination;

use App\Models\Model;

class QuestionBank extends Model
{
    protected $primaryKey = 'question_id';
    protected $is_delete  = 0;

    // 选择题才会设置选项
    public function setQuestionOptionsAttribute($value)
    {
        $this->attributes['question_options'] = my_json_encode($value);
    }

    public function getQuestionOptionsAttribute($value)
    {
        return my_json_decode($value);
    }
}
