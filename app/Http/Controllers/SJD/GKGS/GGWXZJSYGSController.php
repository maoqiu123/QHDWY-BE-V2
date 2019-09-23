<?php

namespace App\Http\Controllers\SJD\GKGS;

use App\Service\SJD\GKGS\GGWXZJSYGSService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GGWXZJSYGSController extends Controller
{
    //
    private $ggwxzjService;
    public function __construct(GGWXZJSYGSService $GGWXZJSYGSService)
    {
        $this->ggwxzjService=$GGWXZJSYGSService;
    }

    public function getGgwxzj(Request $request){
        $user = $request->user;
        $wxxm = $request->wxxm ?? '';
        $monthNum = $request->month ?? 6;
        $size = $request->size ?? 20;
        $data = $this->ggwxzjService->searchWxzjsyqkForYz($user->id,$monthNum,$wxxm,$size);
        return response()->json([
            'code'=>1000,
            'message'=>'手机端获取公共维修资金使用情况成功',
            'data'=>$data
        ]);
    }

    public function getDetail(Request $request){
        $id = $request->wxzjsy;

        $data = $this->ggwxzjService->showDetail($id);
        return response()->json([
            'code'=> 1000,
            'message'=> '手机端获取公共维修资金使用详情成功',
            'data'=>$data
        ]);
    }
}
