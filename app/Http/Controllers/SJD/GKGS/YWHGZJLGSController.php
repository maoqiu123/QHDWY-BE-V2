<?php

namespace App\Http\Controllers\SJD\GKGS;

use App\Service\SJD\GKGS\YWHGZJLGSService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class YWHGZJLGSController extends Controller
{
    //业委会工作记录
    private $ywhgzjlService;
    public function __construct(YWHGZJLGSService $YWHGZJLGSService)
    {
        $this->ywhgzjlService= $YWHGZJLGSService;
    }

    public function getYwhgzjl(Request $request){
        $user = $request->user;
        $gsmc = $request->hybt ?? '';
        $monthNum = $request->month ?? 6;
        $size = $request->size ?? 20;
        $data = $this->ywhgzjlService->search($user->id,$monthNum,$gsmc,$size);
        return response()->json([
            'code'=>1000,
            'message'=>'手机端获取业委会工作记录及决定成功',
            'data'=>$data
        ]);
    }

    public function getDetail(Request $request){
        $id = $request->hyjl;

        $data = $this->ywhgzjlService->showDetail($id);
        return response()->json([
            'code'=>1000,
            'message'=>'手机端获取业委会工作记录及决定详情成功',
            'data'=>$data
        ]);
    }
}
