<?php

namespace App\Http\Controllers\SJD\GKGS;

use App\Service\SJD\GKGS\WTJYGSService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WTJYGSController extends Controller
{
    //
    private $wtjygsService;
    public function __construct(WTJYGSService $WTJYGSService)
    {
        $this->wtjygsService = $WTJYGSService;
    }

    public function getWtjy(Request $request){
        $user = $request->user;
        $gsmc = $request->gsmc ?? '';
        $monthNum = $request->month ?? 6;
        $size = $request->size ?? 20;
        $data = $this->wtjygsService->searchWtjyszqkForYz($user->id,$monthNum,$gsmc,$size);
        return response()->json([
            'code'=>1000,
            'message'=>'手机端获取委托经营收支情况成功',
            'data'=>$data
        ]);
    }

    public function getDetail(Request $request){
        $id = $request->wtjyId;

        $data = $this->wtjygsService->showDetail($id);
        return response()->json([
            'code' => 1000,
            'message'=> '手机端获取委托经营收支情况详情',
            'data'=>$data
        ]);
    }
}
