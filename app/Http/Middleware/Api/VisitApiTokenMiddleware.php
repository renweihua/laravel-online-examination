<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Exceptions\HttpStatus\BadRequestException;
use App\Library\Encrypt\Aes;
use Closure;
use Illuminate\Http\Request;

/**
 * Class VisitApiTokenMiddleware
 *
 * 访问API的Token验证中间件
 *
 * @package App\Middleware\Api
 */
class VisitApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 根据具体业务判断逻辑走向，这里假设用户携带的token有效
        $token = $request->getHeader('api-token')[0] ?? '';
        if ( strlen($token) <= 0 ) {
            throw new BadRequestException('无权访问[01]！');
        }

        $aes = new Aes;
        $data = $aes->decrypt($token);
        if (!$data){
            throw new BadRequestException('无权访问[02]！');
        }
        // 检测Token的过期时间
        if (!isset($data['expire_time']) || $data['expire_time'] < time()){
            throw new BadRequestException('访问无权已失效[02]！');
        }

        // 获取形参
        $params = $request->getQueryParams();
        if (!isset($params['app_key']) || empty($params['app_key'])){
            throw new BadRequestException('请设置AppKey！');
        }
        // 验证app_key是否匹配
        if (!isset($data['app_key']) || $params['app_key'] != $data['app_key']){
            throw new BadRequestException('无效AppKey！');
        }

        return $next($request);
    }
}
