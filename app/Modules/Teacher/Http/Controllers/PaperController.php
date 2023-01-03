<?php

namespace App\Modules\Teacher\Http\Controllers;

use App\Modules\Teacher\Http\Requests\PaperRequest;
use App\Modules\Teacher\Services\PaperService;

class PaperController extends TeacherController
{
    public function __construct()
    {
        $this->service = PaperService::getInstance();
    }

    public function create(PaperRequest $request)
    {
        $data = $this->service->create($request);

        return $this->successJson($data, $this->service->getError());
    }
}
