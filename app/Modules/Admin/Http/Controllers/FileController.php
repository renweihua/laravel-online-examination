<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Exceptions\HttpStatus\BadRequestException;
use App\Models\UploadFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileController extends BaseController
{
    protected $model = UploadFile::class;

    public function setFiltersWhere($query)
    {
        $request = request();
        // 按照名称进行搜索
        if (!empty($search = $request->input('search', ''))){
            $query->where('file_name', 'LIKE', '%' . trim($search) . '%');
        }
        // 分组筛选
        $group_id = (int)$request->input('group_id', -1);
        if ($group_id > 0){
            $query->where('group_id', $group_id);
        }
    }

    /**
     * 移动文件至指定分组
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFileGroup(Request $request): JsonResponse
    {
        $params = $request->all();
        if ($this->getModel()->whereIn('file_id', $params['file_ids'])->update(['group_id' => $params['group_id']])){
            return $this->successJson([], '移动成功！');
        }else{
            throw new BadRequestException('移动失败！');
        }
    }
}
