<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Exceptions\HttpStatus\BadRequestException;
use App\Traits\Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait TraitController
{
    use Error;

    protected $model;
    protected $modelInstance;

    protected $with = [];
    protected $detailWith = [];

    protected function getModel()
    {
        if (empty($this->model)) {
            throw new BadRequestException('请注入Model模型');
        }
        if (empty($this->modelInstance)) $this->modelInstance = new $this->model;
        return $this->modelInstance;
    }

    public function lists($query = [], array $params = [])
    {
        if (!$this->model){
            throw new BadRequestException('请注入Model模型');
        }
        $build = $this->getModel();
        if ($this->with){
            $build = $build->with($this->with);
        }

        if (method_exists($this, 'setFiltersWhere')){
            $this->setFiltersWhere($build);
        }

        // if (empty($query)){
        //     $query = \request()->all();
        // }

        if ($query){
            if (is_callable($query)){
                $query($build);
            }else{
                foreach ($query as $key => $value){
                    if (is_numeric($key)){
                        if (count($value) == 3){
                            [$filed, $like, $search] = $value;
                            $build = $build->where($filed, $like, "%{$search}%");
                        }
                    }else{
                        $build = $build->where($key, $value);
                    }
                }
            }
        }

        if (method_exists($this, 'setOrderBy')){
            $this->setOrderBy($build);
            // if (empty($build)){
            //     throw new BadRequestException('`setOrderBy`必须返回对应的`Query`');
            // }
        }else{
            $build = $build->orderBy(empty($params['order']) ? $this->getModel()->getKeyName() : $params['order'], empty($params['order_sort']) ? 'DESC' : $params['order_sort']);
        }

        // 如果是下载，那么数据将不分页。
        if (isset($params['is_download']) && $params['is_download'] == 1){
            $lists = $build->get()->toArray() ?? [];
            return $lists;
        }else{
            $lists = $build->paginate($this->getLimit((int)request()->input('limit')));
            return $this->getPaginateFormat($lists);
        }
    }

    /**
     * 详情
     */
    public function detail(Request $request): JsonResponse
    {
        $id = $request->input($this->model->getKeyName(), 0);
        $detail = $this->model->detail($id, '*', false, $this->detailWith ?? $this->with);
        return $this->successJson($detail);
    }

    /**
     * @param  array  $params
     * @param  int    $start_filter 是否开启自动过滤字段（只会录入当前表的字段）
     *
     */
    public function add(array $params, int $start_filter = 1)
    {
        $result = $this->getModel()->create($start_filter === 1 ? $this->getModel()->setFilterFields($params) : $params);
        $this->setError($result ? '新增成功！' : '新增失败！');
        return $result;
    }

    /**
     * @param  array  $params
     * @param  int    $start_filter 是否开启自动过滤字段（只会录入当前表的字段）
     *
     */
    public function save(array $params, int $start_filter = 1)
    {
        $primary_key = $this->getModel()->getKeyName();
        if (!isset($params[$primary_key])){
            throw new BadRequestException('update主键不存在！');
        }
        $id = $params[$primary_key];
        $result = $this->getModel()->where($primary_key, $id)->update($start_filter === 1 ? $this->getModel()->setFilterFields($params) : $params);
        $this->setError('{ ' . $primary_key . ' => ' . $id . ' }，' . ($result ? '编辑成功！' : '编辑失败！'));
        return $result;
    }

    /**
     * 删除与批量删除 流程
     *
     * @param $id
     *
     * @return mixed
     */
    public function batchDelete(array $params)
    {
        $primary_key = $this->getModel()->getKeyName();
        if (!isset($params[$primary_key]) && !isset($params['id'])){
            throw new BadRequestException('删除时，设置主键标识！');
        }
        $id = $params[$primary_key] ?? $params['id'];
        if (empty($id)){
            throw new BadRequestException('删除时，设置主键标识！');
        }
        if(is_string($id) || is_numeric($id)){
            $ids = [$id];
        }else{
            $ids = $id;
        }
        if ($this->getModel()->getIsDelete()){
            $result = $this->getModel()->whereIn($primary_key, $ids)->delete();
        }else {
            $result = $this->getModel()->whereIn($primary_key, $ids)->update([$this->getModel()->getDeleteField() => 1]);
        }
        $this->setError('{ ' . $primary_key . ' => ' . implode(',', $ids) . '}，' . ($result ? '删除成功！' : '删除失败！'));
        return $result;
    }

    /**
     * 指定字段变动
     *
     * @param  array  $params
     *
     * @return mixed
     */
    public function patchFiled(array $params)
    {
        $primary_key = $this->getModel()->getKeyName();
        if (!isset($params[$primary_key]) && !isset($params['id'])){
            throw new BadRequestException('请指定唯一标识！');
        }
        $id = $params[$primary_key] ?? $params['id'];
        $result = $this->getModel()->where($primary_key, $id)->update([$params['change_field'] => $params['change_value']]);
        $this->setError('{ ' . $primary_key . ' => ' . $id . '}，' . ($result ? '设置成功！' : '设置失败！'));
        return $result;
    }

    /**
     * 下拉列表
     *
     * @param  array  $callback
     * @param  array  ...$parameter
     *
     * @return mixed
     */
    public function selectLists(array $callback = [])
    {
        $lists = $this->getModel()->where($callback)
            ->with($this->with)
            ->orderBy($this->getModel()->getKeyName(), 'ASC')
            ->limit(100)
            ->get();
        return $lists;
    }

    public function getLimit(int $limit = 10):int
    {
        // 不可为0
        $limit = $limit <= 0 ? 10 : $limit;
        // 每页最多100条数据
        $limit = $limit > 100 ? 100 : $limit;
        return $limit;
    }

    public function getPaginateFormat($paginate = []): array
    {
        return [
            'current_page' => $paginate->currentPage(),
            'per_page' => $paginate->perPage(),
            'count_page' => $paginate->lastPage(),
            'total' => $paginate->total(),
            'data' => $paginate->items(),
        ];
    }
}
