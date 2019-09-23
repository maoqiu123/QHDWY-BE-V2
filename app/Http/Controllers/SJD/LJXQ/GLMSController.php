<?php

namespace App\Http\Controllers\SJD\LJXQ;

use App\Service\SJD\LJXQ\GLMSService;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GLMSController extends Controller
{
    //
    private $glmsService;
    public function __construct(GLMSService $GLMSService)
    {
        $this->glmsService=$GLMSService;
    }


    public function makeSure(Request $request){
        $yz = $request->user;
        $rule = [
            'sglmsid' => 'required',
            'sqrjg' => 'required'
        ];
        $res = ValidationHelper::validateCheck($request->input(), $rule);
        if ($res->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $data = ValidationHelper::getInputData($request, $rule);
        if ($this->glmsService->hasQr($yz->id, $data['sglmsid'])->sstatus == '提交') {
            return response()->json([
                'code' => 8002,
                'message' => '不可重复确认'
            ]);
        }
        if ($data['sqrjg'] == '不同意') {
            if (empty($request->input('sbtyyy'))) {
                return response()->json([
                    'code' => 1001,
                    'message' => '不同意需注明原因！'
                ]);
            }
            $data['sbtyyy'] = $request->input('sbtyyy');
        }
        $data['syzid'] = $yz->id;
        $data['dqrrq'] = Carbon::now();
        $data['ssfzd'] = '否';
        $data['stxr'] = $yz->syzxm;
        $data['dtxrq'] = Carbon::now();
        $data['sstatus'] = '提交';
        $this->glmsService->create($data);
        return response()->json([
            'code' => 1000,
            'message' => '投票成功'
        ]);
    }

    public function hasQr(Request $request){
        $sglmsid = $request->sglmsid;
        if (empty($sglmsid)) {
            return response()->json([
                'code' => 1001,
                'message' => '需要填写管理模式id'
            ]);
        }
        $res = $this->glmsService->hasQr($request->user->id, $sglmsid);
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
