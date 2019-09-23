<?php

namespace App\Http\Controllers\WYXM;

use App\Service\WYXM\YZTSXXService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class YZTSXXController extends Controller
{
    //
    private $yztsxxService;
    public function __construct(YZTSXXService $YZTSXXService)
    {
        $this->yztsxxService=$YZTSXXService;
    }

    public function search(Request $request){
        $xmmc = $request->xmmc;
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $qymc=$request->qymc;
        $size = $request->size ?? 20;
        $tssx = $request->tssx;
        $slzt = $request->slzt;

        $tsxxs=$this->yztsxxService->search($xmmc,$startdate,$enddate,$qymc,$size,$tssx,$slzt,[
            't_qyjbxx.sqymc',
            't_xm_jbxx.sxmmc',
            't_yz_yzjbxx.xmid',
            't_yz_yzjbxx.syzxm',
            't_yz_yztsxx.*',
        ]);
        return response()->json([
            'code' => 1000,
            'message'=>'获取业主投诉信息成功',
            'data'=> $tsxxs
        ]);
    }

    public function searchForYwh(Request $request)
    {
        $ywhId = $request->user->id;
        $startDate = $request->startdate;
        $endDate = $request->enddate;
        $status = $request->status;
        $assess = $request->assess;
        $size = $request->size ?? 20;
        $tsxxs=$this->yztsxxService->searchForYwh($ywhId,$startDate,$endDate,$status,$assess,$size);
        return response()->json([
            'code' => 1000,
            'message'=>'获取业主投诉信息成功',
            'data'=> $tsxxs
        ]);
    }

    public function getComplainInfoBy($id,Request $request)
    {
        $tsxx = $this->yztsxxService->getComplainInfoBy($id);
        return response()->json([
            'code' => 1000,
            'message'=>'获取业主投诉信息成功',
            'data'=> $tsxx
        ]);
    }
}
