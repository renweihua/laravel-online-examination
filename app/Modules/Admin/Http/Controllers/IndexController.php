<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Models\MonthModel;
use App\Models\Rabc\Admin;
use App\Modules\Admin\Http\Middleware\CheckRabc;
use App\Modules\Admin\Services\IndexService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function __construct(IndexService $indexService)
    {
        $this->service = $indexService;
    }

    public function index(): JsonResponse
    {
        return $this->successJson($this->service->index());
    }

    /**
     * 按照日志类型的统计图数据
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logsStatistics(Request $request): JsonResponse
    {
        return $this->successJson($this->service->logsStatistics());
    }

    /**
     * 月份表列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMonthList(): JsonResponse
    {
        return $this->successJson(MonthModel::getInstance()->getAllMonthes());
    }

    /**
     * 编辑登录管理员信息
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(): JsonResponse
    {
        $this->service->updateAdmin(request());

        return $this->successJson([], $this->service->getError());
    }

    /**
     * 版本历史记录
     *
     * @return mixed
     */
    public function versionLogs(): JsonResponse
    {
        return $this->successJson($this->service->versionLogs());
    }

    /**
     * 获取服务器状态
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServerStatus(): JsonResponse
    {
        return $this->successJson($this->service->getServerStatus());
    }

    /**
     * 检测接口是否有权限
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function checkApiPermission(Request $request): JsonResponse
    {
        $apis = $request->input('apis', []);
        $menus = Admin::getRabcByAdmin($this->getLoginUserId());
        $result = [];

        $admin_prefix = cnpscy_config('admin_prefix');
        // 验证权限
        foreach ($apis as $api){
            $old_url = $api;
            $api = trim($api, '/');
            $full_url = $admin_prefix . '/' . $api;
            $result[$api] = [
                'url' => $api,
                'old_url' => $old_url,
                'full_url' => $full_url,
                'status' => empty($menus) ? false : (isset($menus[$full_url]) ? true : false),
            ];
        }
        // 验证一次白名单
        $white_lists = CheckRabc::$white_lists;
        foreach ($result as &$item){
            if (!$item['status']){
                if (in_array($item['url'], $white_lists)){
                    $item['status'] = true;
                }
                if (in_array($item['full_url'], $white_lists)){
                    $item['status'] = true;
                }
            }
        }

        return $this->successJson($result);
    }
}
