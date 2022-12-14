<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Exceptions\HttpStatus\BadRequestException;
use App\Models\Rabc\Admin;
use App\Traits\Json;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    use Json;
    use TraitController;

    protected $service;
    protected $request;

    /**
     * 验证器
     */
    protected $validator;

    public function getLoginUser(): Admin
    {
        return request()->attributes->get('login_user');
    }

    public function getLoginUserId(): int
    {
        return $this->getLoginUser()->admin_id ?? 0;
    }

    public function index(): JsonResponse
    {
        // 未设置默认，设置了Service层，那么就调用service层的list
        if (!$this->model && !empty($this->service)){
            return $this->successJson($this->service->lists(request()->all()));
        }
        return $this->successJson($this->lists());
    }

    private function checkValidatorRule($scene = '')
    {
        $validatorRequest = new $this->validator;
        $rules = $validatorRequest->getRules($scene);
        if ($rules){
            $validator = Validator::make(request()->all(), $rules, $validatorRequest->getMessages());
            if ( $validator->fails() ) {
                throw new BadRequestException($validator->errors()->first());
            }
        }
    }

    public function create(): JsonResponse
    {
        $params = request()->all();
        // 开启验证器
        if ( $this->validator ) {
            $this->checkValidatorRule(__FUNCTION__);
        }

        if ( $this->add($params) ) {
            return $this->successJson([], $this->getError());
        } else {
            throw new BadRequestException($this->getError());
        }
    }

    public function update(): JsonResponse
    {
        $params = request()->all();
        // 开启验证器
        if ( $this->validator ) {
            $this->checkValidatorRule(__FUNCTION__);
        }

        if ( $this->save($params) ) {
            return $this->successJson([], $this->getError());
        } else {
            throw new BadRequestException($this->getError());
        }
    }

    /**
     * 删除与批量删除
     *
     * @return JsonResponse
     */
    public function delete(): JsonResponse
    {
        $params = request()->all();
        // 开启验证器
        if ( $this->validator ) {
            $this->checkValidatorRule(__FUNCTION__);
        }

        if ( $this->batchDelete($params) ) {
            return $this->successJson([], $this->getError());
        } else {
            throw new BadRequestException($this->getError());
        }
    }

    /**
     * 设置指定字段【常用于状态的变动】
     *
     * @return JsonResponse
     */
    public function changeFiledStatus(): JsonResponse
    {
        $params = request()->all();
        // 开启验证器
        if ( $this->validator ) {
            $this->checkValidatorRule(__FUNCTION__);
        }

        if ( $this->patchFiled($params) ) {
            return $this->successJson([], $this->getError());
        } else {
            throw new BadRequestException($this->getError());
        }
    }

    /**
     * 下拉筛选列表（可搜索）
     *
     * @return JsonResponse
     */
    public function getSelectLists($callback = []): JsonResponse
    {
        // 未设置默认，设置了Service层，那么就调用service层的list
        if (!$this->model && !empty($this->service)){
            return $this->successJson($this->service->getSelectLists(request()));
        }

        $lists = $this->selectLists($callback);
        return $this->successJson($lists);
    }
}
