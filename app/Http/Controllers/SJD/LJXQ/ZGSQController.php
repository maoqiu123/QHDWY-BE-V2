<?php

namespace App\Http\Controllers\SJD\LJXQ;

use App\Service\SJD\LJXQ\ZGSQService;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZGSQController extends Controller
{
    //
    private $zgsqTpService;

    public function __construct(ZGSQService $ZGSQService)
    {
        $this->zgsqTpService = $ZGSQService;
    }

    public function vote(Request $request)
    {
        $yz = $request->user;
        $rule = [
            'szgsqid' => 'required',
            'stpjg' => 'required',
            'nxzdf' => 'required',
        ];
        $res = ValidationHelper::validateCheck($request->input(), $rule);
        if ($res->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $data = ValidationHelper::getInputData($request, $rule);
        if ($this->zgsqTpService->isYzHasTp($yz->id, $data['szgsqid'])->sstatus == '提交') {
            return response()->json([
                'code' => 8002,
                'message' => '不可重复投票'
            ]);
        }
        if ($data['stpjg'] == '不同意') {
            if (empty($request->input('sbtyyy'))) {
                return response()->json([
                    'code' => 1001,
                    'message' => '不同意需注明原因！'
                ]);
            }
            $data['sbtyyy'] = $request->input('sbtyyy');
        }
        $data['syzid'] = $yz->id;
        $data['dtprq'] = Carbon::now();
        $data['ssfzd'] = '否';
        $data['stxr'] = $yz->syzxm;
        $data['dtxrq'] = Carbon::now();
        $data['sstatus'] = '提交';
        $this->zgsqTpService->create($data);
        return response()->json([
            'code' => 1000,
            'message' => '投票成功'
        ]);
    }

    public function hasYzVoted(Request $request)
    {
        $szgsqid = $request->szgsqid;
        if (empty($szgsqid)) {
            return response()->json([
                'code' => 1001,
                'message' => '需要填写资格申请id'
            ]);
        }
        $res = $this->zgsqTpService->isYzHasTp($request->user->id, $szgsqid);
        if ($res)
            return response()->json([
                'code' => 1000,
                'message' => '查询成功',
                'data' => $res
            ]);
        else return response()->json([
            'code' => 1000,
            'message' => '查询成功',
            'data' => false
        ]);
    }
}
