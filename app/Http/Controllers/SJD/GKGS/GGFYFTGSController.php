<?php

namespace App\Http\Controllers\SJD\GKGS;

use App\Service\SJD\GKGS\GGFYFTGSService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GGFYFTGSController extends Controller
{
    //
    private $ggfyftgsService;
    public function __construct(GGFYFTGSService $GGFYFTGSService)
    {
        $this->ggfyftgsService = $GGFYFTGSService;
    }

    public function getGgfyft(Request $request){
        $user = $request->user;
        $gsmc = $request->gsmc ?? '';
        $monthNum = $request->month ?? 6;
        $size = $request->size ?? 20;
        $data = $this->ggfyftgsService->searchGgftysydgsForYz($user->id,$monthNum,$gsmc,$size);
        return response()->json([
            'code'=>1000,
            'message'=>'获取公关费用分摊情况成功',
            'data'=>$data
        ]);
    }


    public function getDetail(Request $request){
        $id = $request->ggfyftId;

        $data = $this->ggfyftgsService->showDetail($id);
        return response()->json([
            'code' => 1000,
            'message'=> '获取公共费用分摊详情成功',
            'data'=>$data
        ]);
    }
}
