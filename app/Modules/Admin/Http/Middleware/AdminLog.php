<?php

namespace App\Modules\Admin\Http\Middleware;

use App\Traits\Json;
use Closure;
use Illuminate\Http\Request;

class AdminLog
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
        $method = strtoupper($request->getMethod());

        if ($method != 'GET'){
            $admin_log = \App\Models\Log\AdminLog::addRecord(request()->attributes->get('login_user')->admin_id ?? 0);

            $log_status = 0;
            try{
                $response = $next($request);

                // 获取返回data内容
                $response_body_content = $response->getData();

                // 根据接口响应，存储返回状态与文本提示语
                $log_status = $response_body_content->status;
                $log_description = empty($adminlog->log_description) ? $response_body_content->msg : $adminlog->log_description;
            }catch(\Exception $e){
                $log_description = $e->getMessage();
                $response = $this->errorJson($log_description);
            }

            // 同步更新响应状态与文本，在`handler`层可能会被异常终止
            $admin_log->update(
                [
                    'log_duration' => microtime(true) - LARAVEL_START,
                    // 根据接口响应，存储返回状态与文本提示语
                    'log_status'   => $log_status,
                    'description'   => $log_description,
                ]
            );

            return $response;
        }

        return $next($request);
    }
}
