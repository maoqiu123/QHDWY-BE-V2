<?php

namespace App\Http\Controllers\SJD\BMFW;

use App\Service\SJD\BMFW\CYDHService;
use App\Tools\RequestTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CYDHController extends Controller
{
    private  $CYDHService;
    public function __construct(CYDHService $CYDHService)
    {
        $this->CYDHService = $CYDHService;
    }

    public function getCydhList(Request $request)
    {
        $sxmid = $request->user->xmid;
        $cydh =$this->CYDHService->getCydh($sxmid);
        return RequestTool::response($cydh, '1000', '请求成功');
    }
}
