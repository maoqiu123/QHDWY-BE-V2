<?php

namespace App\Http\Controllers\SJD\GKGS;

use App\Service\SJD\GKGS\WYHTLXGSService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WYHTLXGSController extends Controller
{
    private $wyhtlxqkService;
    public function __construct(WYHTLXGSService $WYHTLXGSService)
    {
        $this->wyhtlxqkService =$WYHTLXGSService;
    }

    public function getWyhtlxqk(Request $request){
        $user = $request->user;
        $gsmc = $request->gsmc ?? '';
        $monthNum = $request->month ?? 6;
        $size = $request->size ?? 20;
        $data = $this->wyhtlxqkService->searchHtlxqkForYz($user->id,$monthNum,$gsmc,$size);
        return response()->json([
            'code' => 1000,
            'message'=>'业主手机端获取合同履行情况',
            'data'=>$data
        ]);
    }
    //根据id获取履行情况详情
    public function getDetail(Request $request){
        $htid = $request->htid;

        $data = $this->wyhtlxqkService->showDetail($htid);
        return response()->json([
            'code'=> 1000,
            'message'=> '业主手机端获取某条合同履行情况详情',
            'data'=>$data
        ]);
    }
}
