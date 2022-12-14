<?php

declare(strict_types = 1);

namespace App\Traits;

trait Json
{
    protected $http_code = 200;

    public function setHttpCode($http_code){
        $this->http_code = $http_code;
    }

    public function successJson($data = [], $msg = 'success', $other = [], array $header = [])
    {
        return $this->myAjaxReturn(array_merge(['data' => $data, 'msg' => $msg], $other), $header);
    }

    public function errorJson($msg = 'error', $http_code = 400, $data = [], $other = [], array $header = [])
    {
        return $this->myAjaxReturn(array_merge(['msg' => $msg, 'http_code' => $http_code, 'data' => $data], $other), $header);
    }

    /**
     * [myAjaxReturn]
     * @author:cnpscy <[2278757482@qq.com]>
     * @chineseAnnotation:API接口返回格式统一
     * @englishAnnotation:
     * @version:1.0
     * @param              [type] $data [description]
     */
    public function myAjaxReturn($data, array $header = [])
    {
        $data['data'] = $data['data'] ?? [];
        if(!isset($data['http_code'])) $data['http_code'] = $this->http_code;
        switch ($data['http_code']){
            case 200:
                $data['status'] = 1;
                break;
            case 400:
                $data['status'] = 0;
                break;
            case 401:
                $data['status'] = -1;
                break;
            case 403:
                $data['status'] = -2;
                break;
        }
        $data['msg'] = $data['msg'] ?? (empty($data['status']) ? '数据不存在！' : 'success');
        $data['execution_time'] = microtime(true) - LARAVEL_START;
        $data['http_status'] = $data['http_code'];

        // 如果设置的header的Token返回，那么追加参数
        if ($authorization = request()->header('new_authorization')) $header['Authorization'] = $authorization;
        // JSON_UNESCAPED_UNICODE 256：Json不要编码Unicode
        return response()->json($data, $data['http_status'], $header, 256);
    }
}
