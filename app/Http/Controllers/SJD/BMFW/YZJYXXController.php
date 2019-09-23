<?php

namespace App\Http\Controllers\SJD\BMFW;

use App\Http\Controllers\WYQY\CommonController;
use App\Service\SJD\BMFW\YZJYXXService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class YZJYXXController extends Controller
{
    private $YZJYXXService;
    private $commonController;
    public function __construct(YZJYXXService $YZJYXXService, CommonController $commonController)
    {
        $this->YZJYXXService = $YZJYXXService;
        $this->commonController = $commonController;
    }

    public function createJyxx(Request $request)
    {
        $user = $request->user;
        $rules = [
            'sjysx' => 'required',
            'djyrq' => 'required',
            'slxdh' => 'required',
            'sjynr' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data = ValidationHelper::getInputData($request, $rules);

        $this->YZJYXXService->createJyxx($user->id, $data);

        return RequestTool::response(null, 1000, '提交成功');
    }

    public function uploadFile($id,Request $request)
    {
        $res = $this->commonController->uploadFile($id,'t_yz_yzjyxx','yzjyxx',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_yz_yzjyxx');
        return response()->json($res);
    }

    public function getJyxxList(Request $request)
    {
        $user = $request->user;
        $Jyxxs = $this->YZJYXXService->showJyxxList($user->id);
        return RequestTool::response($Jyxxs, 1000, "获取报修信息列表成功");
    }

    public function getJyxx($id, Request $request)
    {
        $Jyxx = $this->YZJYXXService->showJyxx($id);
        return RequestTool::response($Jyxx,1000, "获取报修信息详情成功");
    }

    public function evaluateJyxx($id, Request $request)
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


        $res = $this->YZJYXXService->evaluateJyxx($id,$data);
        if(!$res)
            return RequestTool::response(null,1003,"该项目未办结");
        return RequestTool::response(null,1000,"提交成功");
    }

    public function recallJyxx($id, Request $request)
    {
        $this->YZJYXXService->recallJyxx($id);
        return RequestTool::response(null, 1000, "撤回成功");
    }
}
