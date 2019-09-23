<?php

namespace App\Http\Controllers\SJD\BMFW;

use App\Http\Controllers\WYQY\CommonController;
use App\Service\SJD\BMFW\YZBXXXService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class YZBXXXController extends Controller
{
    private $YZBXXXService;
    private $commonController;
    public function __construct(YZBXXXService $YZBXXXService, CommonController $commonController)
    {
        $this->YZBXXXService = $YZBXXXService;
        $this->commonController = $commonController;
    }

    public function createBxxx(Request $request)
    {
        $user = $request->user;
        $rules = [
            'sbxsx' => 'required',
            'dbxrq' => 'required',
            'slxdh' => 'required',
            'sbxnr' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data = ValidationHelper::getInputData($request, $rules);

        $this->YZBXXXService->createBxxx($user->id, $data);

        return RequestTool::response(null, 1000, '提交成功');
    }

    public function uploadFile($id,Request $request)
    {
        $res = $this->commonController->uploadFile($id,'t_yz_yzbxxx','yzbxxx',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_yz_yzbxxx');
        return response()->json($res);
    }

    public function getBxxxList(Request $request)
    {
        $user = $request->user;
        $bxxxs = $this->YZBXXXService->showBxxxList($user->id);
        return RequestTool::response($bxxxs, 1000, "获取报修信息列表成功");
    }

    public function getBxxx($id, Request $request)
    {
        $bxxx = $this->YZBXXXService->showBxxx($id);
        return RequestTool::response($bxxx,1000, "获取报修信息详情成功");
    }

    public function evaluateBxxx($id, Request $request)
    {
        $rules = [
            'dpjrq' => 'required',
            'syzpj' => 'required',
            'sbmynr' => '',
        ];
        if($request->syzpj === "不满意")
            $rules['sbmynr'] = 'required';
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);


        $res = $this->YZBXXXService->evaluateBxxx($id,$data);
        if(!$res)
            return RequestTool::response(null,1003,"该项目未办结");
        return RequestTool::response(null,1000,"提交成功");
    }

    public function recallBxxx($id, Request $request)
    {
        $this->YZBXXXService->recallBxxx($id);
        return RequestTool::response(null, 1000, "撤回成功");
    }
}
