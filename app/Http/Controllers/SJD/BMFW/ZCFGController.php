<?php

namespace App\Http\Controllers\SJD\BMFW;

use App\Service\SJD\BMFW\ZCFGService;
use App\Tools\RequestTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZCFGController extends Controller
{
    private $zcfgService;

    public function __construct(ZCFGService $zcfgService)
    {
        $this->zcfgService = $zcfgService;
    }

    public function searchZcfg(Request $request)
    {
        $time = $request->time;
        $name = $request->name;

        $zcfgList = $this->zcfgService->searchZcfg($time, $name);
        return RequestTool::response($zcfgList, 1000, '查询成功');
    }

    public function getZcfgInfo($id, Request $request)
    {
        $zcfg = $this->zcfgService->getZcfgById($id);
        return RequestTool::response($zcfg, 1000, '获取成功');
    }
}
