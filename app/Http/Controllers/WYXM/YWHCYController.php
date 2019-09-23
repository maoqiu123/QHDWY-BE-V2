<?php

namespace App\Http\Controllers\WYXM;

use App\Service\WYXM\YWHCYService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class YWHCYController extends Controller
{
    // 业委会成员
    private $ywhcyService;
    public function __construct(YWHCYService $YWHCYService)
    {
        $this->ywhcyService = $YWHCYService;
    }

    /**
     * 业委会端用条件查询业委会成员信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchForYwh(Request $request){
        $user = $request->user;
        $size = $request->size ?? 20;
        $ywhId = $user->id;
        $sryzt = $request->ryzt;
        $sstatus = $request->status;
        $cy=$this->ywhcyService->searchForYwh($ywhId, $sryzt, $sstatus, $size);

        return response()->json([
            'code' => 1000,
            'message' => '业委会端查询业委会成员成功',
            'data' => $cy
        ]);
    }

    public function getCyInfoById($cyid,Request $request){
        $cyinfo=$this->ywhcyService->getCyxxByCyId($cyid);
        return response()->json([
            'code' => 1000,
            'message' => '根据ID获取业委会成员信息成功',
            'data' => $cyinfo
        ]);
    }

    public function getCyxxList(Request $request)
    {
        $ywhid=$request->user->id;
        $size=$request->size ?? 10;
        $data=$this->ywhcyService->getCyList($ywhid,$size);
        return RequestTool::response($data,1000,'查询成功');
    }

    public function createCyxx(Request $request)
    {
        $rules=[
            'sxm'=>'max:20',
            'ssfzh'=>'max:20',
            'ssrzw'=>'max:20',
            'drqksrq'=>'',
            'drqjzrq'=>'',
            'sxb'=>'required|max:20',
            'szzmm'=>'max:20',
            'dcsrq'=>'',
            'sxl'=>'max:20',
            'sgzdw'=>'max:100',
            'syzw'=>'max:30',
            'slxdh'=>'max:30',
            'sjtzz'=>'max:100',
            'sryzt'=>'max:20',
            'sbz'=>'max:200'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data=array_merge($data,[
                    'sstatus'=>'暂存',
                    'id'=>SqlTool::makeUUID(),
                    'stxr'=>$request->user->id,
                    'stxsj'=>Carbon::now(),
                    'ywhid'=>$request->user->id
        ]);
        $this->ywhcyService->create($data);
        return RequestTool::response(null,1000,"添加成功");
    }

    public function deleteCyxx($id,Request $request)
    {
        $status=$this->ywhcyService->getstatus($id);
        if($status=='提交')
            return RequestTool::response(null,1002,'已提交状态不允许删除');
        $this->ywhcyService->delete($id);
        return RequestTool::response(null,1000,'删除成功');
    }

    public function updateCyxx($id,Request $request)
    {
        $rules=[
            'sxm'=>'max:20',
            'ssfzh'=>'max:20',
            'ssrzw'=>'max:20',
            'drqksrq'=>'',
            'drqjzrq'=>'',
            'sxb'=>'max:20',
            'szzmm'=>'max:20',
            'dcsrq'=>'',
            'sxl'=>'max:20',
            'sgzdw'=>'max:100',
            'syzw'=>'max:30',
            'slxdh'=>'max:30',
            'sjtzz'=>'max:100',
            'sryzt'=>'max:20',
            'sbz'=>'max:200',
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data=array_merge($data,[
            'stxr'=>$request->user->id,
            'stxsj'=>Carbon::now(),
        ]);
        $this->ywhcyService->update($id,$data);
        return RequestTool::response(null,1000,'修改成功');
    }

    public function changeStatus($id)
    {
        $status=$this->ywhcyService->getstatus($id);
        if($status=='提交')
            return RequestTool::response(null,1002,'已提交无须重复提交');
        $data['sstatus']='提交';
        $this->ywhcyService->update($id,$data);
        return RequestTool::response(null,1000,'提交成功');
    }
}
