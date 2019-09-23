<?php

namespace App\Http\Controllers\SJD\GKGS;

use App\Service\SJD\GKGS\YZXGXXService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class YZXGXXController extends Controller
{
    //业主相关信息
    /*
     * 业委会信息
     * 物业企业信息
     * 小区信息
     *
     * */

    private $yzxgxxService;
    public function __construct(YZXGXXService $YZXGXXService)
    {
        $this->yzxgxxService = $YZXGXXService;
    }

    public function getYwhxx(Request $request){
        $user = $request->user;
        $ywh = $this->yzxgxxService->getYwhjbxx($user->id);
        return response()->json([
            'code'=> 1000,
            'message'=> '手机端获取当前用户所在业委会信息成功',
            'data'=>$ywh
        ]);
    }

    public function getQyxx(Request $request){
        $user = $request->user;
        $qyxx = $this->yzxgxxService->getQyjbxx($user->id);
        return response()->json([
            'code'=> 1000,
            'message'=> '手机端获取当前用户所在企业基本信息成功',
            'data'=>$qyxx
        ]);
    }

    public function getXqxx(Request $request){
        $user = $request->user;
        $xqxx = $this->yzxgxxService->getXqxx($user->id);
        return response()->json([
            'code'=>1000,
            'message'=>'手机端获取当前用户所在小区信息成功',
            'data' => $xqxx
        ]);
    }
}
