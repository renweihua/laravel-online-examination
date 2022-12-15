<?php

namespace App\Modules\Teacher\Services;

use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\OnlineExamination\QuestionBank;
use App\Services\Service;
use Illuminate\Http\Request;

class QuestionBankService extends Service
{
    protected function getQuestionById($question_id, $check_auth = true)
    {
        $question_bank = QuestionBank::find($question_id);
        if (empty($note)){
            throw new BadRequestException('题库不存在或已删除！');
        }
        if ($check_auth && $note->user_id != getLoginUserId()){
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
        $question_bank->question_content = $request->input('question_content');
        $question_bank->question_options = $request->input('question_options', []);
        $question_bank->question_answer = $request->input('question_answer');
        $question_bank->answer_explain = $request->input('answer_explain');
        $question_bank->save();

        $this->setError('题库保存成功！');
        return $question_bank;
    }
}
