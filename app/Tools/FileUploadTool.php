<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/17
 * Time: 上午10:54
 */

namespace App\Tools;


use Illuminate\Support\Facades\Storage;

class FileUploadTool
{
    protected $allowed_ext = ["zip", "rar", "7z", "txt", "pdf", "doc", "docx", "xls", "xlsx", "jpg", "jpeg", "png"];

    public function save($file, $folder)
    {
        // 构建存储的文件夹规则，值如：uploads/files/zz
        // 文件夹切割能让查找效率更高。
        $folder_name = "uploads/files/$folder";
        // 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
        // 值如：/home/vagrant/Code/larabbs/public/uploads/images/avatars/201709/21/
        $upload_path = public_path() . '/' . $folder_name;
        // 获取文件的后缀名，因文件从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $name = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        if ( ! in_array($extension, $this->allowed_ext))
            return false;
        // 拼接文件名，加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
        // 值如：1493521050_7BVc9v9ujP.png
        $filename = time() . '_' . str_random(10) . '.' . $extension;

        // 将文件移动到我们的目标存储路径中
        $file->move($upload_path, $filename);
        return [
            'path' => "$folder_name/$filename",
            'name' => $name
        ];
    }

    public function saveToQiniu($file, $folder)
    {

        $name = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        if ( ! in_array($extension, $this->allowed_ext))
            return false;


        $folder_name = $folder;
        $upload_path = $folder_name. '/' .time() . '_' . str_random(10) .$name;


        $disk = Storage::disk('qiniu');
        $res = $disk->put($upload_path, file_get_contents($file));

        if(!$res)
            return false;


        // 上传至七牛
        return [
            'path' => $upload_path,
            'name' => $name,
            'url' =>  $disk->getDriver()->downloadUrl($upload_path)
                ->setDownload($name)
        ];
    }

    public function getDownloadPath($path)
    {
        return config('app.url') . "/" . $path;
    }
}