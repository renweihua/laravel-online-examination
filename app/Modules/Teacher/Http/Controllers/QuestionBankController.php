<?php

namespace App\Modules\Teacher\Http\Controllers;

use App\Modules\Teacher\Http\Requests\QuestionBankRequest;
use App\Modules\Teacher\Services\QuestionBankService;

class QuestionBankController extends TeacherController
{
    public function __construct()
    {
        $this->service = QuestionBankService::getInstance();
    }

    public function create(QuestionBankRequest $request)
    {
        $data = $this->service->create($request);

        return $this->successJson($data, $this->service->getError());
    }
}
