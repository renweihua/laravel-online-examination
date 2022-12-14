<?php

namespace App\Http\Middleware;

use App\Exception\HttpStatus\ForbiddenException;
use App\Helper\Logger;
use App\Models\System\IpBlacklist;
use App\Traits\Json;
use Closure;
use Illuminate\Http\Request;

class CheckIpBlacklist
{
    use Json;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $client_ip = $request->getClientIp();
        if (!$client_ip){
            $client_ip = get_ip();
        }

        $ips = IpBlacklist::getAllIPs();
        $ips_by_ip = $ips->keyBy('ip');
        foreach ($ips_by_ip as $ip){
            if (!empty($ip->ip_end)){ // IP段
                if (check_ip_range($client_ip, $ip->ip, $ip->ip_end)){
                    $this->log($client_ip, $request);
                    $msg = '您的IP段已被禁止访问！';
                    if ($request->expectsJson()) {
                        return $this->errorJson($msg);
                    }else{
                        abort(403, $msg);
                    }
                }
            }else{
                if (isset($ips_by_ip[$client_ip])){
                    $this->log($client_ip, $request);
                    $msg = '您的IP在系统黑名单中，禁止访问！';
                    if ($request->expectsJson()) {
                        return $this->errorJson($msg);
                    }else{
                        abort(403, $msg);
                    }
                }
            }
        }

        return $next($request);

        // 获取黑名单组
        $ip_blacklists = cnpscy_config('ip_blacklists');

        // var_dump($client_ip);

        // 黑名单的IP（上海腾讯云服务器的IP）
        $ip_blacklists = '49.234.35.33,49.234.27.58,49.234.25.245,49.234.34.31,81.71.60.226,81.71.60.110,81.71.60.207,81.71.60.213,1.13.138.226,1.13.140.118,112.53.2.68';

        if ($ip_blacklists){
            $ip_blacklists_array = explode(',', $ip_blacklists);
            // 键值翻转，检测是否存在数组key
            $ip_blacklists_array = array_flip($ip_blacklists_array);
            if (isset($ip_blacklists_array[$client_ip])){
                $this->log($client_ip, $request);

                $msg = '您的IP在系统黑名单中，禁止访问！';
                if ($request->expectsJson()) {
                    return $this->errorJson($msg);
                }else{
                    abort(403, $msg);
                }
            }
        }


        // IP段访问限制（上海腾讯云服务器的IP）
        $ip_ranges = [
            ['175.24.211.1', '175.24.214.255'],
            ['81.71.60.1', '81.71.60.255'],
        ];
        if ($ip_ranges) {
            foreach ($ip_ranges as $key => $value) {
                if (check_ip_range($client_ip, current($value), end($value))){
                    $this->log($client_ip, $request);

                    $msg = '您的IP段已被禁止访问！';
                    if ($request->expectsJson()) {
                        return $this->errorJson($msg);
                    }else{
                        abort(403, $msg);
                    }
                }
            }
        }


        return $next($request);
    }

    private function log($client_ip, $request)
    {
        $logger = Logger::get('illegal-access');
        $logger->info('请求来源IP：' . $client_ip);
        $logger->info('URL：' . $request->getUri());
        $logger->info('Header：' . my_json_encode($request->header()));
    }
}
