<?php

namespace App\Modules\Admin\Http\Controllers\System;

use App\Models\System\Protocol;
use App\Modules\Admin\Http\Controllers\BaseController;
use App\Modules\Admin\Http\Requests\System\ProtocolRequest;

class ProtocolController extends BaseController
{
    protected $model = Protocol::class;

    protected $validator = ProtocolRequest::class;

    public function setFiltersWhere($query)
    {
        $request = request();
        // 按照名称进行搜索
        if (!empty($search = $request->input('search', ''))){
            $query->where('protocol_title', 'LIKE', '%' . trim($search) . '%');
        }
        // 状态
        $is_check = $request->input('is_check', -1);
        if ($is_check > -1){
            $query->where('is_check', '=', $is_check);
        }
    }
}
