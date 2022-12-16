<?php

namespace App\Modules\Teacher\Services;

use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\OnlineExamination\QuestionBank;
use App\Services\Service;
use Illuminate\Http\Request;

class QuestionBankService extends Service
{
    public function paginate(): array
    {
        $login_user_id = getLoginUserId();
        $request = request();
        $search = $request->input('search', '');
        // 题库类型
        $question_type = $request->input('question_type', -1);
        // 课程筛选
        $course_id = $request->input('course_id', -1);
        $lists = QuestionBank::with('course')
            ->where('teacher_id', $login_user_id)
            ->where(function ($query) use ($search, $question_type, $course_id){
                if (!empty($search)){
                    $query->where('question_content', 'LIKE', '%' . trim($search) . '%');
                }
                if ($question_type > -1){
                    $query->where('question_type', '=', $question_type);
                }
                if ($course_id > -1){
                    $query->where('course_id', '=', $course_id);
                }
            })
            ->orderByDesc('question_id')
            ->paginate();

        return $this->getPaginateFormat($lists);
    }

    protected function getQuestionById($question_id, $check_auth = true)
    {
        $question_bank = QuestionBank::find($question_id);
        if (empty($question_bank)){
            throw new BadRequestException('题库不存在或已删除！');
        }
        if ($check_auth && $question_bank->teacher_id != getLoginUserId()){
            throw new ForbiddenException('您无权限查看题库`' . $question_bank->question_content . '`！');
        }
        return $question_bank;
    }

    public function createOrUpdate(Request $request)
    {
        $question_id = $request->input('question_id', 0);
        if (!$question_id){
            $question_bank = new QuestionBank;
            $question_bank->teacher_id = getLoginUserId();
        }else{
            $question_bank = $this->getQuestionById($question_id);
        }

        $question_bank->question_type = $request->input('question_type');
        $question_bank->course_id = $request->input('course_id');
        $question_bank->question_content = $request->input('question_content');
        $question_bank->question_options = $request->input('question_options', []);
        $question_bank->question_answer = $request->input('question_answer');
        $question_bank->answer_explain = $request->input('answer_explain', '');
        $question_bank->save();

        $this->setError('题库`' . $question_bank->question_content . '`保存成功！');
        return $question_bank;
    }
}
