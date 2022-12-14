<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Models\Rabc\AdminMenu;
use App\Modules\Admin\Http\Requests\LoginRequest;
use App\Modules\Admin\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function __construct(AuthService $authService)
    {
        $this->service = $authService;
    }

    /**
     * 登录流程
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Admin\AuthException
     * @throws \App\Exceptions\InvalidRequestException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        // 登录流程
        $token = $this->service->login($data);

        return $this->successJson($token, $this->service->getError());
    }

    /**
     * 获取登录管理员信息
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Admin\AuthException
     */
    public function me(Request $request): JsonResponse
    {
        if (\request()->getMethod() == 'OPTIONS'){
            return $this->successJson();
        }

        return $this->successJson($this->service->me($request));
    }

    /**
     * 退出登录
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->successJson($this->service->logout($request));
    }

    public function getRabcList(Request $request): JsonResponse
    {
        return $this->successJson($this->service->getRabcList($request));

        // 临时测试数据
        return $this->successJson(list_to_tree(AdminMenu::getInstance()->getAllMenus()->toArray()));
    }
}
