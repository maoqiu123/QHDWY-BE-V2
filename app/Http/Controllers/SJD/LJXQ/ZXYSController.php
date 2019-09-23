<?php

namespace App\Http\Controllers\SJD\LJXQ;

use App\Service\SJD\LJXQ\ZXYSService;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZXYSController extends Controller
{
    //
    private $zxysqrService;
    public function __construct(ZXYSService $ZXYSService)
    {
        $this->zxysqrService = $ZXYSService;
    }

    public function makeSure(Request $request){
        $yz = $request->user;
        $rule = [
            'szxysid' => 'required',
            'sysjg' => 'required'
        ];
        $res = ValidationHelper::validateCheck($request->input(), $rule);
        if ($res->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $data = ValidationHelper::getInputData($request, $rule);
        if ($this->zxysqrService->hasQr($yz->id, $data['szxysid'])->sstatus == '提交') {
            return response()->json([
                'code' => 8002,
                'message' => '不可重复确认'
            ]);
        }
        if ($data['sysjg'] == '不同意') {
            if (empty($request->input('sbtyyy'))) {
                return response()->json([
                    'code' => 1001,
                    'message' => '不同意需注明原因！'
                ]);
            }
            $data['sbtyyy'] = $request->input('sbtyyy');
        }
        $data['syzid'] = $yz->id;
        $data['dysrq'] = Carbon::now();
        $data['ssfzd'] = '否';
        $data['stxr'] = $yz->syzxm;
        $data['dtxrq'] = Carbon::now();
        $data['sstatus'] = '提交';
        $this->zxysqrService->create($data);
        return response()->json([
            'code' => 1000,
            'message' => '投票成功'
        ]);
    }

    public function hasYzYS(Request $request){
        $szxysid = $request->szxysid;
        if (empty($szxysid)) {
            return response()->json([
                'code' => 1001,
                'message' => '需要填写在线验收id'
            ]);
        }
        $res = $this->zxysqrService->hasQr($request->user->id, $szxysid);
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
