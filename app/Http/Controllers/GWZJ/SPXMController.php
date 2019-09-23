<?php

namespace App\Http\Controllers\GWZJ;

use App\Service\GWZJ\SPXMService;
use App\Tools\RequestTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SPXMController extends Controller
{

    private $SPXMService;

    public function __construct(SPXMService $SPXMService)
    {
        $this->SPXMService = $SPXMService;
    }

    public function searchProject(Request $request)
    {

        $region = $request->region_id;
        $endStatus = $request->status;
        $size = $request->size ?? 20;

        $data = $this->SPXMService->searchProject($region, $endStatus, $size);

        return RequestTool::response($data, 1000, '查询成功');
    }

    public function showProject($id, Request $request)
    {
        $projectInfo = $this->SPXMService->showProject($id);
        return RequestTool::response($projectInfo,1000,'查询详情成功');
    }


}
