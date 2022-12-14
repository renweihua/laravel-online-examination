<?php

namespace App\Modules\Admin\Http\Controllers\System;

use App\Models\System\Banner;
use App\Modules\Admin\Http\Controllers\BaseController;
use App\Modules\Admin\Http\Requests\System\BannerRequest;

class BannerController extends BaseController
{
    protected $model = Banner::class;

    protected $validator = BannerRequest::class;

    public function setFiltersWhere($query)
    {
        $request = request();
        // 按照名称进行搜索
        if (!empty($search = $request->input('search', ''))){
            $query->where('banner_title', 'LIKE', '%' . trim($search) . '%');
        }
        // 状态
        $is_check = $request->input('is_check', -1);
        if ($is_check > -1){
            $query->where('is_check', '=', $is_check);
        }
    }

    public function setOrderBy($query)
    {
        return $query->orderBy('banner_sort', 'ASC');
    }
}
