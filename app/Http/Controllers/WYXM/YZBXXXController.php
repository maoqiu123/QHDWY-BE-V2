<?php

namespace App\Http\Controllers\WYXM;

use App\Service\WYXM\YZBXQKService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class YZBXXXController extends Controller
{
    //
    private $yzbxxxService;
    public function __construct(YZBXQKService $YZBXQKService)
    {
        $this->yzbxxxService=$YZBXQKService;
    }

    public function search(Request $request){
        $xmmc = $request->xmmc;
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $qymc=$request->qymc;
        $size = $request->size ?? 20;
        $bxsx = $request->bxsx;
        $slzt = $request->slzt;
        $bxxxs=$this->yzbxxxService->search($xmmc,$startdate,$enddate,$qymc,$size,$bxsx,$slzt,[
            't_qyjbxx.sqymc',
            't_xm_jbxx.sxmmc',
            't_yz_yzjbxx.xmid',
            't_yz_yzjbxx.syzxm',
            't_yz_yzbxxx.*',
        ]);
        return response()->json([
            'code' => 1000,
            'message'=>'获取业主报修信息成功',
            'data'=> $bxxxs
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
        $bxxxs=$this->yzbxxxService->searchForYwh($ywhId,$startDate,$endDate,$status,$assess,$size);
        return response()->json([
            'code' => 1000,
            'message'=>'获取业主报修信息成功',
            'data'=> $bxxxs
        ]);
    }

    public function getRepairInfoBy($id,Request $request)
    {
        $bxxx = $this->yzbxxxService->getRepairInfoBy($id);
        return response()->json([
            'code' => 1000,
            'message'=>'获取业主报修信息成功',
            'data'=> $bxxx
        ]);
    }
}
