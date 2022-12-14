<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Models\UploadGroup;
use App\Modules\Admin\Http\Requests\FileGroupRequest;

class FileGroupController extends BaseController
{
    protected $model = UploadGroup::class;

    protected $validator = FileGroupRequest::class;

    public function setFiltersWhere($query)
    {
        $request = request();
        // 按照名称进行搜索
        if (!empty($search = $request->input('search', ''))){
            $query->where('group_name   ', 'LIKE', '%' . trim($search) . '%');
        }
    }
}
