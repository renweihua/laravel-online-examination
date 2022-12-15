<?php

namespace App\Modules\Teacher\Services;

use App\Models\OnlineExamination\Paper;
use App\Models\OnlineExamination\QuestionBank;
use App\Services\Service;
use Illuminate\Http\Request;

class QuestionBankService extends Service
{
    public function create(Request $request)
    {
        $data = $request->all();
        $this->setError('题库创建成功！');
        return QuestionBank::create($data);
    }
}
