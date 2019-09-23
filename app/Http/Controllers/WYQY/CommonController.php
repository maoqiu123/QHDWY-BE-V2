<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/16
 * Time: 下午4:04
 */

namespace App\Http\Controllers\WYQY;


use App\Http\Controllers\Controller;
use App\Service\FileService;
use App\Service\WYQY\QYJBXXWHService;
use App\Service\XZQHService;
use App\Tools\FileUploadTool;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    private $qyjbxxService;
    private $xzqhService;
    private $fileService;

    public function __construct(QYJBXXWHService $QYJBXXWHService, XZQHService $XZQHService, FileService $fileService)
    {
        $this->qyjbxxService = $QYJBXXWHService;
        $this->xzqhService = $XZQHService;
        $this->fileService = $fileService;
    }

    /**
     * 检查企业编号是否存在
     * @param string $enterpriseCode 企业编号
     * @return string
     */
    public function checkEnterpriseExist(string $enterpriseCode)
    {
        if ($this->qyjbxxService->isColumnExist('sqybm', $enterpriseCode))
            return RequestTool::response(true, 1000, '该企业不存在');
        else
            return RequestTool::response(false, 1000, '不存在该企业');
    }

    /**
     * 企业名称提示
     * @param string $hint 提示
     * @return array 企业名称提示
     */
    public function hintEnterpriseName(string $hint)
    {
        $data = $this->qyjbxxService->show([['sqymc', 'like', '%' . $hint . '%']], ['id', 'sqymc']);
        return RequestTool::response($data, 1000, '模糊匹配成功');
    }

    /**
     * 获取省份列表
     *
     * @return array 省份
     */
    public function getProvinces()
    {
        $provinces = $this->xzqhService->getProvinces();
        return RequestTool::response($provinces, 1000, '获取省份列表成功');
    }

    /**
     * 获取市列表
     * @param int $provinceId 省份id
     * @return array 城市
     */
    public function getCities(int $provinceId)
    {
        $cities = $this->xzqhService->getCities($provinceId);
        return RequestTool::response($cities, 1000, '获取城市列表成功');
    }

    /**
     * 获取区委会列表
     * @param int $cityId 城市id
     * @return array 区县
     */
    public function getDistrict(int $cityId)
    {
        $district = $this->xzqhService->getDistrict($cityId);
        return RequestTool::response($district, 1000, '获取县列表成功');
    }

    /**
     * 获取街道
     * @param int $districtId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBanShiChu(int $districtId)
    {
        $banshichu = $this->xzqhService->getBanShiChu($districtId);
        return RequestTool::response($banshichu, 1000, '获取街道列表成功');
    }

    /**
     * 获取居委会
     * @param int $banshichu
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJuWeiHui(int $banshichu)
    {
        $juweihui = $this->xzqhService->getJuWeiHui($banshichu);
        return RequestTool::response($juweihui, 1000, '获取居委会列表成功');
    }

    /**
     * @param $id 表id 指明哪条记录附件
     * @param $tbName string 表名字
     * @param $folder string 上传目录前缀 例如 如资质信息 则传入 'zzxx'
     * @param $fileContent 文件内容
     * @return array
     */
    public function uploadFile($id, $tbName, $folder, $fileContent)
    {
        $uploader = new FileUploadTool();
        // 初始化返回数据，默认是失败的
        $data = [
            'success' => 0,
            'id' => '',
            'file_path' => '',
            'file_name' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $fileContent) {
            if(config('app.file_location') == 'local')
            {
                // 保存文件到本地
                $result = $uploader->save($fileContent, $folder);
            }
            else
            {
                // 保存文件到七牛
                $result = $uploader->saveToQiniu($fileContent, $folder);
            }

            // 文件保存成功的话
            if ($result) {
                $data['success'] = 1;
                $data['id'] = $this->fileService->saveFiles($result['path'], $result['name']);
                $data['file_path'] = config('app.file_location') == 'local' ? config('app.url') . '/file/' . $data['id'] : $result['url'] ;
                $data['file_name'] = $result['name'];
                $res = $this->fileService->updateTableRecord($id, $data, $tbName);
                if (!$res)
                    $data['success'] = 0;
            }
        }
        return $data;
    }

    /**
     * @param $fileid 文件jsonId
     * @param $rowid 文件在表中记录id
     * @param $tbName string 表名
     * @return array code 1000成功
     *
     */
    public function deleteFile($fileid, $rowid, $tbName)
    {
        $data = [
            'code' => 1000,
            'message' => '删除成功'
        ];

        $r = $this->fileService->deleteTableRecord($fileid, $rowid, $tbName);
        if (!$r) {
            $data['code'] = 1102;
            $data['message'] = '删除记录失败';
            return $data;
        }

//        $tbName = 't_qyjbxx_zzxx';
//        $rowid = 10000000001634;
        $res = $this->fileService->getDownloadFile($fileid);
        if ($res == null) {
            $data['code'] = 1100;
            $data['message'] = '文件不存在';
            return $data;
        }
        if(config('app.file_location') == 'local')
            $re = unlink(public_path() . '/' . $res->fjlj);
        else{
            $disk = \Storage::disk('qiniu');
            $re = $disk->delete($res->fjlj);
        }
        if (!$re) {
            $data['code'] = 1101;
            $data['message'] = '删除文件失败';
            return $data;
        }
        $this->fileService->deleteDownloadFile($fileid);

        return $data;
    }

    public function downloadFile($id)
    {
        $file = $this->fileService->getDownloadFile($id);
        if ($file == null)
            return RequestTool::response(null, 1102, "文件不存在!");

        $filePath = $file->fjlj;
        $fileName = $file->fjmc;

        return response()->download($filePath, $fileName);
    }

    public function codeList(Request $request)
    {
        $zh = $request->zh;
        $dmb = SqlTool::getCodeWithMc($zh);
        if (!$dmb) {
            return response()->json([
                'code' => 1001,
                'message' => '没有该代码表，请联系管理员加数据',
                'data' => [
                    [
                        'value' => 'error',
                        'lable' => '出错！请联系管理员'
                    ]
                ]
            ]);
        }
        return response()->json([
            'code' => 1000,
            'data' => $dmb,
            'message' => '获取' . $zh . '列表成功'
        ]);
    }

}