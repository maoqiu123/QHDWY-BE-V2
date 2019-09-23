<?php

namespace App\Http\Controllers\WYYZ;

use App\Service\WYYZ\YZJYXXService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class YZJYSLController extends Controller
{
    private $YZJYXXService;
    public function __construct(YZJYXXService $YZJYXXService)
    {
        $this->YZJYXXService = $YZJYXXService;
    }

    public function searchSuggestionInfo(Request $request)
    {
        $xmid = $request->user->id;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $status=$request->status;
        $size = $request->size ?? 20;

        $SuggestionInfo = $this->YZJYXXService->searchSuggestionInfo($xmid,$startTime,$endTime,$status,$size);
        return RequestTool::response($SuggestionInfo,1000,"请求成功");
    }

    public function handleSuggestionInfo($recoedId,Request $request)
    {
        $res = $this->YZJYXXService->handleSuggestionInfo($recoedId);
        if(!$res)
            return RequestTool::response(null,1003,"该项目已受理");
        return RequestTool::response(null,1000,"受理成功");
    }

    public function finishSuggestionInfo($recoedId,Request $request)
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
        $res = $this->YZJYXXService->finishSuggestionInfo($recoedId,$data);
        if(!$res)
            return RequestTool::response(null,1003,"该项目未受理或已办结");
        return RequestTool::response(null,1000,"办结成功");
    }

    public function searchSuggestionInfoOwner(Request $request)
    {
        $ownerId = $request->user->id;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $size = $request->size ?? 20;
        $res = $this->YZJYXXService->searchSuggestionInfoOwner($ownerId,$startTime,$endTime,$size);
        return RequestTool::response($res,1000,"请求成功");
    }

    public function addSuggestionInfo(Request $request)
    {
        $rules = [
            'djyrq' => 'required',
            'slxdh' => 'required',
            'sjysx' => 'required',
            'sjynr' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        $userId =  $request->user->id;

        $this->YZJYXXService->createSuggestionInfoOwner($userId,$data);
        return RequestTool::response(null,1000,"暂存成功");
    }

    public function submitSuggestionInfo($id,Request $request)
    {
        $res = $this->YZJYXXService->submitSuggestionInfo($id);
        if(!$res)
            return RequestTool::response(null,1003,"该项目已提交");
        return RequestTool::response(null,1000,"提交成功");
    }

    public function evaluateSuggestionInfo($id,Request $request)
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


        $res = $this->YZJYXXService->evaluateSuggestionInfo($id,$data);
        if(!$res)
            return RequestTool::response(null,1003,"该项目未办结");
        return RequestTool::response(null,1000,"提交成功");
    }

    public function getSuggestionInfoBy($id,Request $request)
    {
        $SuggestionInfo = $this->YZJYXXService->getSuggestionInfoBy($id);
        return RequestTool::response($SuggestionInfo,1000,"请求成功");
    }

    public function getSuggestionType(Request $request)
    {
        $types = $this->YZJYXXService->getSuggestionType();
        return RequestTool::response($types,1000,"请求成功");
    }

    public function delete($id,Request $request){
        $status = $this->YZJYXXService->delete($id);

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
            'sjysx' => 'required',
            'sjynr' => 'required',
            'slxdh' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $status = $this->YZJYXXService->update($id,$data);

        if ($status == 1000){
            return RequestTool::response(null,1000,"修改成功");
        }elseif ($status == 1001){
            return RequestTool::response(null,1001,"已提交数据不允许修改");
        }else{
            return RequestTool::response(null,1002,"修改失败");
        }
    }

    public function getSuggestionInfoForXm($id,Request $request)
    {
        $suggestionInfo = $this->YZJYXXService->getSuggestionInfo($id);
        return RequestTool::response($suggestionInfo,1000,"请求成功");
    }
}
