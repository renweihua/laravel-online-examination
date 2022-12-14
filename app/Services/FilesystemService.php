<?php

namespace App\Services;

use App\Exceptions\InvalidRequestException;
use App\Models\UploadFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Overtrue\CosClient\ObjectClient;

class FilesystemService extends Service
{
    protected $config;

    public function __construct()
    {

    }

    /**
     * 文件上传
     *
     * @param          $file
     * @param  string  $file_name
     * @param  string  $folder
     *
     * @return string
     * @throws \App\Exceptions\InvalidRequestException
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function put($file, $file_name = '', $folder = '', int $file_size = 0, $file_type = 'image/jepg', $extension = 'jpg') : string
    {
        if (empty($file)){
            throw new InvalidRequestException('请上传文件！');
        }
        if ( is_string($file) ) {
            if (empty($file_name)){
                $file_name = Str::random(50) . '.' . $extension;
            }
            $key = (empty($folder) ? date('Ym') : ($folder)) . '/' . $file_name;
            $content = $file;
        } else {
            if (empty($file_name)){
                // 移除特殊符标识，否则需要使用 urlencode 格式化
                // $file_name = remove_special_symbols($file->getClientOriginalName());
                // $file_name = urlencode($file->getClientOriginalName());
                // 一律重命名（存在特殊表情符号，太多各种问题了！）
                $file_name = Str::random(50) . '.' . pathinfo($file->getClientOriginalName())['extension'];
            }
            // if (strlen($file_name) > 200){
            //     $file_name = Str::random(50) . '.' . pathinfo($file->getClientOriginalName())['extension'];
            // }
            $key = (empty($folder) ? '' : ($folder . '/')) . date('Ym') . '/' . $file_name;
            $content = file_get_contents($file->getRealPath());
        }

        Storage::put($key, $content);

        // 添加文件库记录
        $uploadFile = UploadFile::addRecord(
            $key,
            $file,
            'local',
            trim(Storage::url(''), '/'),
            $file_size,
            $file_type,
            $extension
        );

        return $uploadFile->file_url;
    }
}
