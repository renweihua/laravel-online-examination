<?php

namespace App\Modules\Admin\Services;

use App\Exceptions\Admin\AuthException;
use App\Exceptions\Admin\AuthTokenException;
use App\Exceptions\InvalidRequestException;
use App\Models\Log\AdminLoginLog;
use App\Models\Rabc\Admin;
use App\Models\Rabc\AdminMenu;
use App\Modules\Bbs\Services\UserLoginRedisService;
use App\Services\Service;

class AuthService extends Service
{
    /**
     * 登录流程
     *
     * @param $data
     * @return array
     * @throws AuthException
     * @throws InvalidRequestException
     */
    public function login($data)
    {
        $admin = Admin::getInstance()->getAdminByName($data['admin_name']);
        if (!$admin){
            throw new AuthException('管理员账户不存在！');
        }
        if (!hash_verify($data['password'], $admin->password)) throw new AuthException('认证失败！');
        switch ($admin->is_check) {
            case 0:
                throw new AuthException('该管理员尚未启用！', 0, $admin->admin_id);
                break;
            case 2:
                throw new AuthException('该管理员已禁用！', 0, $admin->admin_id);
                break;
        }

        AdminLoginLog::getInstance()->add($admin->admin_id);

        $result = $this->respondWithToken($admin->admin_id);

        // Token存入Redis
        AdminLoginRedisService::getInstance()->saveUserToken($result);

        $this->setError('登录成功！');
        return $result;
    }

    /**
     * 登录管理员信息获取
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @throws \App\Exceptions\Admin\AuthTokenException
     */
    public function me($request)
    {
        if (!$admin = $request->attributes->get('login_user')){
            throw new AuthTokenException('认证失败！');
        }
        $admin->admin_head = asset($admin->admin_head);
        $admin['roles'] = ['admin'];
        return $admin;
    }

    /**
     * 获取拥有的权限
     *
     * @throws \App\Exceptions\Admin\AuthTokenException
     */
    public function getRabcList($request)
    {
        if (!$admin = $request->attributes->get('login_user')){
            throw new AuthTokenException('认证失败！');
        }
        // 如果是admin_id = 1，那么默认返回全部权限
        if($admin->admin_id == 1){
            return list_to_tree(AdminMenu::getInstance()->getAllMenus()->toArray());
        }
        $admin = Admin::with(['roles.menus'])->find($admin->admin_id)->toArray();

        $menus = [];
        foreach (array_column($admin['roles'], 'menus') as $item){
            $menus = array_merge($menus, $item);
        }

        return list_to_tree($menus);
    }

    /**
     * 退出登录
     *
     * @return bool
     */
    public function logout($token)
    {
        UserLoginRedisService::getInstance()->deleteUserToken($token);
        return true;
    }

    /**
     * Get the token array structure.
     *
     * @param $token
     * @return array
     */
    protected function respondWithToken($admin_id): array
    {
        $token = AdminLoginRedisService::getInstance()->getUserToken($admin_id, $expires_time);
        return [
            'admin_id' => $admin_id,
            'auth_type' => 'admin',
            'access_token' => $token,
            'expires_time'   => $expires_time,
            'login_time' => time(),
        ];
    }
}
