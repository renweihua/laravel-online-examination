<?php

namespace App\Modules\Teacher\Http\Controllers;

use App\Modules\Teacher\Http\Requests\QuestionBankRequest;
use App\Modules\Teacher\Http\Requests\QuestionIdRequest;
use App\Modules\Teacher\Services\QuestionBankService;
use Illuminate\Http\Request;

class QuestionBankController extends TeacherController
{
    public function __construct()
    {
        $this->service = QuestionBankService::getInstance();
    }

    public function index()
    {
        $lists = $this->service->paginate();

        return $this->successJson($lists);
    }

    public function createOrUpdate(QuestionBankRequest $request)
    {
        $data = $this->service->createOrUpdate($request);

        return $this->successJson($data, $this->service->getError());
    }

    public function delete(QuestionIdRequest $request)
    {
        $this->service->delete($request->input('question_id'));

        return $this->successJson([], $this->service->getError());
    }
}
