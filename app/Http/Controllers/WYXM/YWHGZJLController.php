<?php

namespace App\Http\Controllers\WYXM;

use App\Service\WYXM\YWHGZJLService;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class YWHGZJLController extends Controller
{
    //
    private $gzjlService;
    private $formRules=[
        'sbh'=>'required|max:40',
        'dhyrq'=>'required|',
        'shybt'=>'required',
        'sydsx'=>'required',
        'shyjd'=>'required',
        'scjry'=>'required',
        'sfbfw'=>'required'
    ];
    public function __construct(YWHGZJLService $YWHGZJLService)
    {
        $this->gzjlService=$YWHGZJLService;
    }

    /**
     * 房管局端用条件查询
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request){
        $xmmc = $request->xmmc;
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $size = $request->size ?? 20;
        $gzjls=$this->gzjlService->search($xmmc,$startdate,$enddate,$size,[
            't_ywh_gzjl.id',
            't_xm_jbxx.sxmmc',
            't_xm_jbxx.id as sxmid',
            't_ywh_gzjl.sbh',
            't_ywh_gzjl.dhyrq',
            't_ywh_gzjl.shybt',
            't_ywh_gzjl.sydsx',
            't_ywh_gzjl.shyjd',
            't_ywh_gzjl.scjry',
            't_ywh_gzjl.sfbfw',
            't_ywh_gzjl.ffj'
        ]);
        return response()->json([
            'code' => 1000,
            'message' => '查询业委会工作记录及决定成功',
            'data'=> $gzjls
        ]);

    }

    /**
     * 业委会端用条件查询
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchForYwh(Request $request){
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $size = $request->size ?? 20;
        $status = $request->status;
        $gzjls = $this->gzjlService->searchForYwh($request->user->id,$startdate,$enddate,$status,$size);
        return response()->json([
            'code' => 1000,
            'message' => "查询业委会工作记录及决定成功",
            'data' => $gzjls
        ]);
    }

    /**
     * 暂存工作记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveGzjl(Request $request){

        $rules=$this->formRules;
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors(),
            ]);
        }
        $data = ValidationHelper::getInputData($request,$rules);
        $data['ywhid']= $request->user->id;
        $data['sstatus']='暂存';
        if (!isset($request->gzjlid)){
            $data['id']=SqlTool::makeUUID();

            $this->gzjlService->create($data);
        }
        else{
            $this->gzjlService->updateGzjl($data['id'],$data);
        }
        return response()->json([
            'code' => 1000,
            'message' => '暂存成功'
        ]);
    }

    /**
     * 提交工作记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitGzjls(Request $request){
        if (!isset($request->gzjlid)||empty($request->gzjlid)){
            return response()->json([
                'code' => 1032,
                'message' => 'id is required'
            ]);
        }
        $rules=$this->formRules;
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors(),
            ]);
        }
        $data = ValidationHelper::getInputData($request,$rules);
        $data['sstatus']='提交';
        if ($this->gzjlService->submitGzjl($request->gzjlid,$data)){
            return response()->json([
                'code' => 1031,
                'message' => '不可提交'
            ]);
        }
        return response()->json([
            'code' => 1000,
            'message'=> '提交成功'
        ]);
    }


}
