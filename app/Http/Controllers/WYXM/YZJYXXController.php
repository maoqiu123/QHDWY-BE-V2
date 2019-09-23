<?php

namespace App\Http\Controllers\WYXM;

use App\Service\WYXM\YZJYXXService;
use App\Service\WYXM\YZTSXXService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class YZJYXXController extends Controller
{
    private $YZJYXXService;

    public function __construct(YZJYXXService $YZJYXXService)
    {
        $this->YZJYXXService = $YZJYXXService;
    }
    public function searchForYwh(Request $request)
    {
        $ywhId = $request->user->id;
        $startDate = $request->startdate;
        $endDate = $request->enddate;
        $status = $request->status;
        $assess = $request->assess;
        $size = $request->size ?? 20;
        $jyxxs=$this->YZJYXXService->searchForYwh($ywhId,$startDate,$endDate,$status,$assess,$size);
        return response()->json([
            'code' => 1000,
            'message'=>'获取业主建议信息成功',
            'data'=> $jyxxs
        ]);
    }

    public function getSuggestionInfoBy($id,Request $request)
    {
        $jyxx = $this->YZJYXXService->getSuggestionInfoBy($id);
        return response()->json([
            'code' => 1000,
            'message'=>'获取业主建议信息成功',
            'data'=> $jyxx
        ]);
    }
}
