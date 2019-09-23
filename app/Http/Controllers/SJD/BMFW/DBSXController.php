<?php

namespace App\Http\Controllers\SJD\BMFW;

use App\Service\SJD\BMFW\DBSXService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DBSXController extends Controller
{
    private $DBSXService;

    public function __construct(DBSXService $DBSXService)
    {
        $this->DBSXService = $DBSXService;
    }

    public function getTpbjTable($tpid, Request $request)
    {
        $user = $request->user;
        $tpbj = $this->DBSXService->getTpbjTable($tpid, $user->xmid, $user->id);
        return RequestTool::response($tpbj, 1000, "请求成功");
    }

    public function getTpbjRes($tpid, Request $request)
    {
        $user = $request->user;
        $tpRes = $this->DBSXService->getTpbjRes($tpid, $user->id);
        return RequestTool::response($tpRes, 1000, "获取用户投票记录成功");
    }

    public function voteTpbj($tpid, Request $request)
    {
        $user = $request->user;
        $rule = [
            'stpjg' => 'required',
        ];
        if ($request->stpjg == '不同意')
            $rule['sbtyyy'] = 'required';

        $res = ValidationHelper::validateCheck($request->input(), $rule);
        if ($res->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $res = $this->DBSXService->getTpbjRes($tpid, $user->id);
        if ($res->stpbjid != null) {
            return RequestTool::response(null, 4001, "不可重复提交");
        }
        $tpInfo = $this->DBSXService->getTpbjInfo($tpid);
        $nowTime = Carbon::now();
        if ($tpInfo == null || $tpInfo->djzrq < $nowTime)
            return RequestTool::response(null, 4002, "投票已截止");

        $data = ValidationHelper::getInputData($request, $rule);
        $data = array_merge($data, [
            'stpbjid' => $tpid,
            'syzid' => $user->id,
            'stxr' => $user->syzxm,
        ]);
        $tpid = $this->DBSXService->voteTpbj($res->bjjgid, $data);
        return response()->json([
            'code' => 1000,
            'message' => '提交成功',
            'tpid' => $tpid
        ]);
    }

    public function getZlpjTable($pjid, Request $request)
    {
        $user = $request->user;
        $zlpj = $this->DBSXService->getZlpjTable($pjid, $user->xmid, $user->id);
        return RequestTool::response($zlpj, 1000, "请求成功");
    }

    public function getZlpjRes($pjid, Request $request)
    {
        $user = $request->user;
        $tpRes = $this->DBSXService->getZlpjRes($pjid, $user->id);
        return RequestTool::response($tpRes, 1000, "获取用户投票记录成功");
    }

    public function getZlpjContent(Request $request)
    {
        $content = $this->DBSXService->getZlpjContent();
        return RequestTool::response($content, 1000, '获取质量评价内容成功');
    }

    public function voteZlpj($pjid, Request $request)
    {
        $user = $request->user;
        $rule = [
        ];
        $content = $this->DBSXService->getZlpjContent();
        foreach ($content as $item) {
            $name = $item->pym;
            $rule[$name] = 'required';
            $rule[$name . '_sbtyyy'] = '';
            if ($request->$name == '不满意') {
                $rule[$name . '_sbtyyy'] = 'required';
            }
        }
        $res = ValidationHelper::validateCheck($request->input(), $rule);
        if ($res->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $res = $this->DBSXService->ifZlpj($pjid, $user->id);
        if ($res) {
            return RequestTool::response(null, 4001, "不可重复提交");
        }
        $tpInfo = $this->DBSXService->getZlpjInfo($pjid);
        $nowTime = Carbon::now();
        if ($tpInfo == null || $tpInfo->djzrq < $nowTime)
            return RequestTool::response(null, 4002, "投票已截止");

        $data = ValidationHelper::getInputData($request, $rule);

        $toInsDatas = [];
        foreach ($content as $item) {
            $rowData = [];
            $name = $item->pym;

            $rowData['szlpjid'] = $pjid;
//            $rowData['syzid'] = $user->id;
            $rowData['spjxm'] = $item->mc;
            $rowData['spjyj'] = $data[$name];
            $rowData['sbmynr'] = $data[$name . '_sbtyyy'];
            $rowData['syzid'] = $user->id;
            $rowData['stxr'] = $user->syzxm;

            $toInsDatas[] = $rowData;
        }
        $this->DBSXService->voteZlpj($user->id, $pjid, $toInsDatas);
        return response()->json([
            'code' => 1000,
            'message' => '提交成功',
        ]);
    }

    public function getDbsxList(Request $request)
    {
        $user = $request->user;
        $dbsxList = $this->DBSXService->getDbsxList($user->xmid, $user->id);
        return response()->json([
            'code' => 1000,
            'message' => '请求成功',
            'data' => $dbsxList
        ]);
    }


}
