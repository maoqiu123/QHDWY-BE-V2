<?php

namespace App\Http\Controllers\WYYZ;

use App\Service\WYYZ\YZBXXXService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class YZBXSLController extends Controller
{
    private $YZBXXXService;
    public function __construct(YZBXXXService $YZBXXXService)
    {
        $this->YZBXXXService = $YZBXXXService;
    }

    public function searchRepairInfo(Request $request)
    {
        $xmid = $request->user->id;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $status=$request->status;
        $size = $request->size ?? 20;

        $RepairInfo = $this->YZBXXXService->searchRepairInfo($xmid,$startTime,$endTime,$status,$size);
        return RequestTool::response($RepairInfo,1000,"请求成功");
    }

    public function handleRepairInfo($recoedId,Request $request)
    {
        $res = $this->YZBXXXService->handleRepairInfo($recoedId);
        if(!$res)
            return RequestTool::response(null,1003,"该项目已受理");
        return RequestTool::response(null,1000,"受理成功");
    }

    public function finishRepairInfo($recoedId,Request $request)
    {
        $rules = [
            'sblqk' => 'required',
            'dbjrq' => 'required',
            'dhfrq' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data['sslzt'] = '已办结';
        $res = $this->YZBXXXService->finishRepairInfo($recoedId,$data);
        if(!$res)
            return RequestTool::response(null,1003,"该项目未受理");
        return RequestTool::response(null,1000,"办结成功");
    }

    public function searchRepairInfoOwner(Request $request)
    {
        $ownerId = $request->user->id;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $size = $request->size ?? 20;
        $res = $this->YZBXXXService->searchRepairInfoOwner($ownerId,$startTime,$endTime,$size);
        return RequestTool::response($res,1000,"请求成功");
    }

    public function addRepairInfo(Request $request)
    {
        $rules = [
            'dbxrq' => 'required',
            'slxdh' => 'required',
            'sbxsx' => 'required',
            'sbxnr' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        $userId = $request->user->id;

        $this->YZBXXXService->createRepairInfoOwner($userId,$data);
        return RequestTool::response(null,1000,"暂存成功");
    }

    public function submitRepairInfo($id,Request $request)
    {
        $res = $this->YZBXXXService->submitRepairInfo($id);
        if(!$res)
            return RequestTool::response(null,1003,"该项目已提交");
        return RequestTool::response(null,1000,"提交成功");
    }

    public function evaluateRepairInfo($id,Request $request)
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


        $res = $this->YZBXXXService->evaluateRepairInfo($id,$data);
        if(!$res)
            return RequestTool::response(null,1003,"该项目未办结");
        return RequestTool::response(null,1000,"提交成功");
    }

    public function getRepairInfoBy($id,Request $request)
    {
        $RepairInfo = $this->YZBXXXService->getRepairInfoBy($id);
        return RequestTool::response($RepairInfo,1000,"请求成功");
    }

    public function getRepairType(Request $request)
    {
        $types = $this->YZBXXXService->getRepairType();
        return RequestTool::response($types,1000,"请求成功");
    }

    public function delete($id,Request $request){
        $status = $this->YZBXXXService->delete($id);

        if ($status == 1000){
            return RequestTool::response(null,1000,"删除成功");
        }elseif ($status == 1001){
            return RequestTool::response(null,1001,"已提交数据不允许删除");
        }else{
            return RequestTool::response(null,1002,"删除失败");
        }

    }
    public function update($id,Request $request){
        $rules = [
            'sbxsx' => 'required',
            'sbxnr' => 'required',
            'slxdh' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $status = $this->YZBXXXService->update($id,$data);

        if ($status == 1000){
            return RequestTool::response(null,1000,"修改成功");
        }elseif ($status == 1001){
            return RequestTool::response(null,1001,"已提交数据不允许修改");
        }else{
            return RequestTool::response(null,1002,"修改失败");
        }
    }

    public function getRepairInfoForXm($id,Request $request)
    {
        $RepairInfo = $this->YZBXXXService->getRepairInfo($id);
        return RequestTool::response($RepairInfo,1000,"请求成功");
    }
}
