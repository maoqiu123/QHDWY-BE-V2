<?php

namespace App\Http\Controllers\WYYZ;

use App\Service\WYYZ\YZTSXXService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class YZTSSLController extends Controller
{
    private $YZTSXXService;
    public function __construct(YZTSXXService $YZTSXXService)
    {
        $this->YZTSXXService = $YZTSXXService;
    }

    public function searchComplainInfo(Request $request)
    {
        $xmid = $request->user->id;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $status=$request->status;
        $size = $request->size ?? 20;

        $complainInfo = $this->YZTSXXService->searchComplainInfo($xmid,$startTime,$endTime,$status,$size);
        return RequestTool::response($complainInfo,1000,"请求成功");
    }

    public function handleComplainInfo($recoedId,Request $request)
    {
        $res = $this->YZTSXXService->handleComplainInfo($recoedId);
        if(!$res)
            return RequestTool::response(null,1003,"该项目已受理");
        return RequestTool::response(null,1000,"受理成功");
    }

    public function finishComplainInfo($recoedId,Request $request)
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
        $res = $this->YZTSXXService->finishComplainInfo($recoedId,$data);
        if(!$res)
            return RequestTool::response(null,1003,"该项目未受理");
        return RequestTool::response(null,1000,"办结成功");
    }

    public function searchComplainInfoOwner(Request $request)
    {
        $ownerId = $request->user->id;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $size = $request->size ?? 20;
        $res = $this->YZTSXXService->searchComplainInfoOwner($ownerId,$startTime,$endTime,$size);
        return RequestTool::response($res,1000,"请求成功");
    }

    public function addComplainInfo(Request $request)
    {
        $rules = [
            'dtsrq' => 'required',
            'slxdh' => 'required',
            'stssx' => 'required',
            'stsnr' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        $userId = $request->user->id;

        $this->YZTSXXService->createComplainInfoOwner($userId,$data);
        return RequestTool::response(null,1000,"暂存成功");
    }

    public function submitComplainInfo($id,Request $request)
    {
        $res = $this->YZTSXXService->submitComplainInfo($id);
        if(!$res)
            return RequestTool::response(null,1003,"该项目已提交");
        return RequestTool::response(null,1000,"提交成功");
    }

    public function evaluateComplainInfo($id,Request $request)
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


        $res = $this->YZTSXXService->evaluateComplainInfo($id,$data);
        if(!$res)
            return RequestTool::response(null,1003,"该项目未办结");
        return RequestTool::response(null,1000,"提交成功");
    }

    public function getComplainInfoBy($id,Request $request)
    {
        $complainInfo = $this->YZTSXXService->getComplainInfoBy($id);
        return RequestTool::response($complainInfo,1000,"请求成功");
    }

    public function getComplaintType(Request $request)
    {
        $types = $this->YZTSXXService->getComplaintType();
        return RequestTool::response($types,1000,"请求成功");
    }
    public function delete($id,Request $request){
        $status = $this->YZTSXXService->delete($id);

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
            'stssx' => 'required',
            'stsnr' => 'required',
            'slxdh' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $status = $this->YZTSXXService->update($id,$data);

        if ($status == 1000){
            return RequestTool::response(null,1000,"修改成功");
        }elseif ($status == 1001){
            return RequestTool::response(null,1001,"已提交数据不允许修改");
        }else{
            return RequestTool::response(null,1002,"修改失败");
        }
    }

    public function getComplainInfoForXm($id,Request $request)
    {
        $complainInfo = $this->YZTSXXService->getComplainInfo($id);
        return RequestTool::response($complainInfo,1000,"请求成功");
    }
}
