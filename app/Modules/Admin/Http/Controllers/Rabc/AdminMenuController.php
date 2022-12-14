<?php

namespace App\Modules\Admin\Http\Controllers\Rabc;

use App\Modules\Admin\Http\Controllers\BaseController;
use App\Modules\Admin\Http\Requests\Rabc\AdminMenuRequest;
use App\Modules\Admin\Services\AdminMenuService;

class AdminMenuController extends BaseController
{
    protected $validator = AdminMenuRequest::class;

    public function __construct(AdminMenuService $adminMenuService)
    {
        $this->service = $adminMenuService;
    }
}
