<?php

namespace App\Modules\Admin\Http\Controllers\System;

use App\Models\System\Config;
use App\Modules\Admin\Http\Controllers\BaseController;
use App\Modules\Admin\Http\Requests\System\ConfigRequest;
use Illuminate\Http\JsonResponse;

class ConfigController extends BaseController
{
    protected $model = Config::class;

    protected $validator = ConfigRequest::class;

    public function setFiltersWhere($query)
    {
        $request = request();
        // 按照名称进行搜索
        if (!empty($search = $request->input('search', ''))){
            $query->where(function($query)use ($search){
                $query->where('config_title', 'LIKE', '%' . trim($search) . '%')
                    ->orWhere('config_name', 'LIKE', '%' . trim($search) . '%');
            });
        }
        // 状态
        $is_check = $request->input('is_check', -1);
        if ($is_check > -1){
            $query->where('is_check', '=', $is_check);
        }
    }

    public function setOrderBy($query)
    {
        return $query->orderBy('config_sort', 'ASC');
    }

    public function getConfigGroupType(): JsonResponse
    {
        $config_type_list = $config_group_list = [];
        $config_group = cnpscy_config('config_group_list');
        foreach ($config_group as $key => $value){
            $config_group_list[] = [
                'value' =>  $key,
                'name' =>  $value,
            ];
        }
        $config_type = cnpscy_config('config_type_list');
        foreach ($config_type as $key => $value){
            $config_type_list[] = [
                'value' =>  $key,
                'name' =>  $value,
            ];
        }
        return $this->successJson([
            'config_group_list' => $config_group_list,
            'config_type_list' => $config_type_list,
        ]);
    }

    /**
     * 同步配置
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pushRefreshConfig(): JsonResponse
    {
        $this->getModel()->pushRefreshConfig();
        return $this->successJson([], '配置文件已同步成功！');
    }
}
