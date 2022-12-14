<?php

namespace App\Modules\Admin\Http\Controllers\System;

use App\Models\System\Friendlink;
use App\Modules\Admin\Http\Controllers\BaseController;
use App\Modules\Admin\Http\Requests\System\FriendlinkRequest;

class FriendlinkController extends BaseController
{
    protected $model = Friendlink::class;

    protected $validator = FriendlinkRequest::class;

    public function setFiltersWhere($query)
    {
        $request = request();
        // 按照名称进行搜索
        if (!empty($search = $request->input('search', ''))){
            $query->where('link_name', 'LIKE', '%' . trim($search) . '%');
        }
        // 状态
        $is_check = $request->input('is_check', -1);
        if ($is_check > -1){
            $query->where('is_check', '=', $is_check);
        }
    }

    public function setOrderBy($query)
    {
        return $query->orderBy('link_sort', 'ASC');
    }
}
