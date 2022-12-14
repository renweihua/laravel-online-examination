<?php

namespace App\Modules\Admin\Http\Controllers\Rabc;

use App\Modules\Admin\Http\Controllers\BaseController;
use App\Modules\Admin\Http\Requests\Rabc\AdminRoleRequest;
use App\Modules\Admin\Services\AdminRoleService;

class AdminRoleController extends BaseController
{
    protected $validator = AdminRoleRequest::class;

    public function __construct(AdminRoleService $adminRoleService)
    {
        $this->service = $adminRoleService;
    }
}
