<?php

namespace App\Http\Controllers\SJD\BMFW;

use App\Service\SJD\BMFW\TZGGService;
use App\Tools\RequestTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TZGGController extends Controller
{
    private $tzggService;

    public function __construct(TZGGService $tzggService)
    {
        $this->tzggService = $tzggService;
    }

    public function searchTzgg(Request $request)
    {
        $time = $request->time;
        $name = $request->name;

        $tzggList = $this->tzggService->searchTzgg($time, $name);
        return RequestTool::response($tzggList, 1000, '查询成功');
    }

    public function getTzggInfo($id, Request $request)
    {
        $tzgg = $this->tzggService->getTzggById($id);
        return RequestTool::response($tzgg, 1000, '获取成功');
    }
}
