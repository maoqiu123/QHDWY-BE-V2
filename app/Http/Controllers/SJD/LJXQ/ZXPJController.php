<?php

namespace App\Http\Controllers\SJD\LJXQ;

use App\Service\SJD\LJXQ\ZXPJService;
use App\Service\SJD\LJXQ\ZXYSService;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZXPJController extends Controller
{
    //
    private  $zxpjService;
    public function __construct(ZXPJService $ZXYSService)
    {
        $this->zxpjService = $ZXYSService;
    }

    public function dafen(Request $request){
        $yz = $request->user;
        $rule = [
            'szxpjid'=>'required',
            'npjdf' => 'required',
            'spjms'=>''
        ];
        $res = ValidationHelper::validateCheck($request->input(),$rule);
        if ($res->fails()){
            return response()->json([
                'code'=>1001,
                'message'=>$res->errors()
            ]);
        }
        $data = ValidationHelper::getInputData($request,$rule);
        if ($this->zxpjService->hasDF($yz->id,$data['szxpjid'])->sstatus == '提交'){
            return response()->json([
                'code' => 8002,
                'message' => '不可重复打分'
            ]);
        }
        $data['syzid']=$yz->id;
        $data['dpjrq']=Carbon::now();
        $data['ssfzd']='否';
        $data['stxr'] = $yz->syzxm;
        $data['dtxrq'] = Carbon::now();
        $data['sstatus'] = '提交';
        $this->zxpjService->create($data);
        return response()->json([
            'code' => 1000,
            'message' => '打分成功'
        ]);

    }

    public function hasDafen(Request $request){
        $szxpjdfid = $request->szxpjdf;
        if (empty($szxpjdfid)) {
            return response()->json([
                'code' => 1001,
                'message' => '需要填写在线评价id'
            ]);
        }
        $res = $this->zxpjService->hasDF($request->user->id, $szxpjdfid);
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
