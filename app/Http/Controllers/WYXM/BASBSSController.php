<?php

namespace App\Http\Controllers\WYXM;

use App\Service\WYXM\BASBSSService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BASBSSController extends Controller
{

    private $sbssService;
    public function __construct(BASBSSService $BASBSSService)
    {
        $this->sbssService=$BASBSSService;
    }

    /**
     * 根据项目Id查询设备设施备案列表（房管局端用）
     * @param $xmid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSbssByXmid($xmid,Request $request){
        $size = $request->size ?? 20;
        $sbsss=$this->sbssService->getSbssByXmid($xmid,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据项目Id查询设备设施备案列表成功',
            'data' => $sbsss
        ]);
    }

    /**
     * 根据设备设施Id查询设备设施备案详细信息（房管局端用）
     * @param $sbid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSbssBySbid($sbid){
        $sbss=$this->sbssService->getSbssBySbid($sbid);
        return response()->json([
            'code' => 1000,
            'message' => '根据设备设施Id查询设备设施备案详细信息',
            'data' => $sbss
        ]);
    }
    public function searchSbssForXm(Request $request)
    {
        $xmid = $request->user->id;
        $sbbh = $request->sbbh;
        $sbmc = $request->sbmc;
        $pp = $request->pp;
        $sccj = $request->sccj;
        $gys = $request->gys;
        $size = $request->size ?? 20;
        $datas = $this->sbssService->searchSbssForXm($xmid,$sbbh,$sbmc, $pp,$sccj,$gys,$size);
        return RequestTool::response($datas,1000,"查询成功");
    }

    private $SbssRules = [
//        'id'=>'',
//        'xmid'=>'required|max:50',
//        'sxmbm'=>'required|max:30',
        'ssbbh'=>'required|max:30',
        'ssbmc'=>'required|max:50',
        'isl'=>'required',
        'sjldw'=>'required|max:20',
        'spp'=>'required|max:30',
        'sggxh'=>'',
        'ssblx'=>'required|max:20',
        'ssccj'=>'required|max:100',
        'sgys'=>'',
        'sgyslxfs'=>'',
        'dccrq'=>'required|date_format:"Y-m-d"',
        'sccbh'=>'required',
        'njg'=>'',
        'dgzsj'=>'required|date_format:"Y-m-d"',
        'sgzfs'=>'',
        'sazdd'=>'',
        'dbxdqsj'=>'',
        'ijxzq'=>'',
        'dscjxsj'=>'',
        'dxcjxsj'=>'',
        'isbsm'=>'',
        'dyjbfsj'=>'',
        'ssyzk'=>'required|max:20',
        'ssfyfj'=>'',
        'sbz'=>'',
//        'sshr'=>'',
//        'dshsj'=>'',
//        'sshsm'=>'',
    ];

    public function createSbss(Request $request)
    {
        $rules = $this->SbssRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        $data = array_merge($data,[
            'id' => SqlTool::makeUUID(),
            'xmid' => $request->user->id,
            'sqyid' =>$request->user->sqyid,
            'sxmbm' => $request->user->sxmbh,
            'sstatus' => '暂存',
            'stxr' => $request->user->id,
            'dtxsj' => Carbon::now()
        ]);
        $this->sbssService->create($data);
        return RequestTool::response(null,1000,'添加成功');
    }

    public function updateSbss($id,Request $request)
    {
        $rules = $this->SbssRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        if(!$this->sbssService->getSbssEditStatus($id))
            return RequestTool::response(null,1003,'已提交不允许编辑');
        $this->sbssService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function submitSbss($id,Request $request)
    {
        $res = $this->sbssService->submitSbss($id);
        if($res)
            return RequestTool::response(null,1002,'不允许重复提交');
        return RequestTool::response(null,1000,'提交成功');
    }

    public function getSbss($id,Request $request)
    {
        $SbssInfo = $this->sbssService->getSbssBySbid($id);
        return RequestTool::response($SbssInfo,1000,'请求成功');
    }

    public function deleteSbss($id,Request $request)
    {
        $res = $this->sbssService->deleteSbss($id);
        if(!$res)
            return RequestTool::response(null,1002,'已提交数据不允许删除');
        return RequestTool::response(null,1000,'删除成功');

    }

    public function searchSbssForFgj(Request $request)
    {
        $sxmmc = $request->sxmmc;
        $sbbh = $request->ssbbh;
        $sbmc = $request->ssbmc;
        $pp = $request->spp;
        $sccj = $request->ssccj;
        $gys = $request->sgys;
        $ssyzk = $request->ssyzk;
        $size = $request->size ?? 20;
        $datas = $this->sbssService->searchSbssForFgj($sxmmc,$sbbh,$sbmc, $pp,$sccj,$gys,$ssyzk,$size);
        return RequestTool::response($datas,1000,"查询成功");
    }
}
