<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/6/23
 * Time: 下午10:15
 */

namespace App\Http\Controllers\LJXQ;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WYQY\CommonController;
use App\Service\LJXQ\GZGZJDJZQKService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GZGZJDJZQKController
{
    private $gzgzjdjzqkService;
    private $commonController;
    public function __construct(GZGZJDJZQKService $GZGZJDJZQKService,CommonController $commonController)
    {
        $this->gzgzjdjzqkService = $GZGZJDJZQKService;
        $this->commonController = $commonController;
    }
    private $gzgzjdjzqkRules = [
        'sgzjhid'=>'required|max:50',
        'sjzqk'=>'required|max:1000',
        'sbz'=>''
    ];
    public function create(Request $request){
        $rules = $this->gzgzjdjzqkRules;
        //进行验证，成功则返回数据数组，失败则返回JsonResponse类
        $data = ValidationHelper::checkAndGet($request, $rules, 1001);
        //如果data类型为response则return，否则继续
        $flag = 1;
        $className = null;
        try {
            $className = class_basename($data);
        } catch (\Exception $exception) {
            $flag = 0;
        }
        if ($flag == 1 && $className == 'JsonResponse')
            return $data;
        //给数据添加唯一标识，所有数据的唯一标识均用次函数生成
        $data['id'] = SqlTool::makeUUID();
        //录入人
        $data['slrr'] = $request->user->id;
        //录入时间
        $data['dlrsj'] = Carbon::now();
        //上报日期
        $data['dsbrq'] = Carbon::now();
        //数据状态默认为暂存
        $data['ssjzt'] = "暂存";
        //入库
        $this->gzgzjdjzqkService->create($data);
        //返回结果
        return RequestTool::response(null, 1000, '添加成功');
    }
    public function update($id,Request $request){
        //获取当前操作用户
//        $user = $request->user;
        $data['ssjzt'] = "提交";
        //入库
        $this->gzgzjdjzqkService->update($id,$data);
        //返回结果
        return RequestTool::response(null, 1000, "更新成功");
    }
    public function zan($id,Request $request)
    {
        $status=$this->gzgzjdjzqkService->getstatus($id);
        if($status=='提交')
            return RequestTool::response(null,1002,'已提交状态不允许修改');
        $rules=$this->gzgzjdjzqkRules;
        unset($rules['sgzjhid']);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $this->gzgzjdjzqkService->update($id,$data);
        return RequestTool::response(null,1000,'暂存成功');
    }
    public function delete($id,Request $request){
        $this->gzgzjdjzqkService->delete($id);
        return RequestTool::response(null, 1000, '删除成功');
    }
    public function searchGzgzjdjzqk(Request $request)
    {
        if ($request->sgzjhid == null||$request->sgzjhid == ''){
            return RequestTool::response('', 1000, 'sgzjhid is required.');
        }
        $sgzjhid=$request->sgzjhid;
        $size = $request->size ?? 10;

        $ggftysydgs = $this->gzgzjdjzqkService->searchGzgzjdjzqk($sgzjhid,$size);

        return RequestTool::response($ggftysydgs, 1000, '查询改造工作计划情况成功');
    }
    public function uploadFile($id,Request $request)
    {
        // dd($request->file);
        $res = $this->commonController->uploadFile($id,'t_ljxq_gzgzjd_jzqk','ljxqjzqk',$request->file);
        if(!$res['success'])
            return RequestTool::response($res,1101,"上传失败!");


        return RequestTool::response($res,1000,"上传成功!");
    }

    public function deleteFile(Request $request)
    {
        if(!isset($request->fileid)||!isset($request->rowid))
            return RequestTool::response(null,1001,"未填写记录id或文件id!");
        $fileId = $request->fileid;
        $rowId= $request->rowid;
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_ljxq_gzgzjd_jzqk');
        return response()->json($res);
    }


}