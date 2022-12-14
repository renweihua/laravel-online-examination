<?php

namespace App\Modules\Admin\Http\Controllers\System;

use App\Models\System\Version;
use App\Modules\Admin\Http\Controllers\BaseController;
use App\Modules\Admin\Http\Requests\System\VersionRequest;
use Illuminate\Http\JsonResponse;

class VersionController extends BaseController
{
    protected $model = Version::class;

    protected $validator = VersionRequest::class;

    public function setFiltersWhere($query)
    {
        $request = request();
        // 按照名称进行搜索
        if (!empty($search = $request->input('search', ''))){
            $query->where(function($query)use ($search){
                $query->where('version_name', 'LIKE', '%' . trim($search) . '%')
                    ->orWhere('version_number', 'LIKE', '%' . trim($search) . '%');
            });
        }
    }

    public function setOrderBy($query)
    {
        return $query->orderBy('version_sort', 'ASC');
    }
}
