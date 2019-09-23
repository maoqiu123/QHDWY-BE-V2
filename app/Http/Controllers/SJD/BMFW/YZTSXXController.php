<?php

namespace App\Http\Controllers\SJD\BMFW;

use App\Http\Controllers\WYQY\CommonController;
use App\Service\SJD\BMFW\YZTSXXService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class YZTSXXController extends Controller
{
    private $YZTSXXService;
    private $commonController;
    public function __construct(YZTSXXService $YZTSXXService, CommonController $commonController)
    {
        $this->YZTSXXService = $YZTSXXService;
        $this->commonController = $commonController;
    }

    public function createTsxx(Request $request)
    {
        $user = $request->user;
        $rules = [
            'sTssx' => 'required',
            'dTsrq' => 'required',
            'slxdh' => 'required',
            'sTsnr' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data = ValidationHelper::getInputData($request, $rules);

        $this->YZTSXXService->createTsxx($user->id, $data);

        return RequestTool::response(null, 1000, '提交成功');
    }

    public function uploadFile($id,Request $request)
    {
        $res = $this->commonController->uploadFile($id,'t_yz_yztsxx','yztsxx',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_yz_yztsxx');
        return response()->json($res);
    }

    public function getTsxxList(Request $request)
    {
        $user = $request->user;
        $Tsxxs = $this->YZTSXXService->showTsxxList($user->id);
        return RequestTool::response($Tsxxs, 1000, "获取报修信息列表成功");
    }

    public function getTsxx($id, Request $request)
    {
        $Tsxx = $this->YZTSXXService->showTsxx($id);
        return RequestTool::response($Tsxx,1000, "获取报修信息详情成功");
    }

    public function evaluateTsxx($id, Request $request)
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


        $res = $this->YZTSXXService->evaluateTsxx($id,$data);
        if(!$res)
            return RequestTool::response(null,1003,"该项目未办结");
        return RequestTool::response(null,1000,"提交成功");
    }

    public function recallTsxx($id, Request $request)
    {
        $this->YZTSXXService->recallTsxx($id);
        return RequestTool::response(null, 1000, "撤回成功");
    }
}
