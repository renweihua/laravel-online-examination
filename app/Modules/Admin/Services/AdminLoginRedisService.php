<?php

namespace App\Modules\Admin\Services;

use App\Constants\AdminCacheKeys;
use App\Services\UserAuthLoginRedisService;

/**
 * 管理员登录Token管理与数量限制
 *
 * Class AdminLoginRedisService
 *
 * @package App\Modules\Bbs\Services
 */
class AdminLoginRedisService extends UserAuthLoginRedisService
{
    // 数据库连接名
    protected $redis_connection = 'admin-token';

    // 主键key名称
    protected $unique_key = 'admin_id';

    // token key前缀
    protected $token_key = AdminCacheKeys::ADMIN_LOGIN_TOKEN;
    // Token组
    protected $token_list_key = AdminCacheKeys::ADMIN_LOGIN_TOKEN_LIST;
}
