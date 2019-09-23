<?php

namespace App\Http\Controllers\GWZJ;

use App\Service\GWZJ\SPGCService;
use App\Tools\RequestTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class XMSPGCController extends Controller
{
    private $SPGCService;

    public function __construct(SPGCService $SPGCService)
    {
        $this->SPGCService = $SPGCService;
    }

    public function getApproveRecord($projectId,Request $request)
    {
        $rocord =  $this->SPGCService->getApproveRecord($projectId);
        return RequestTool::response($rocord,1000,'查询详情成功');
    }

}
