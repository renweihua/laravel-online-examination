<?php

namespace App\Modules\Teacher\Http\Controllers;

use App\Modules\Teacher\Http\Requests\QuestionBankRequest;
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
}
