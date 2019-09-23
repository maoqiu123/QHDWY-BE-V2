<?php

namespace App\Http\Controllers\WYXM;

use App\Service\WYXM\XMJJService;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class XMJJController extends Controller
{
    //

    private $xmjjService;
    public function __construct(XMJJService $XMJJService)
    {
        $this->xmjjService = $XMJJService;
    }

    /**
     * 根据信用代码查询部分企业基本信息
     * @param $xydm
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQy($xydm,Request $request){
        $qy = $this->xmjjService->getQy($xydm);
        if (!$qy){
            return response()->json([
                'code'=> 1001,
                'message' => '不存在该企业'
            ]);
        }
        return response()->json([
            'code' =>1000,
            'message' => '获取企业部分信息成功',
            'data' => $qy
        ]);
    }

    /**
     * 根据Token检索企业在管企业的部分信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQyByXm(Request $request){
        $user = $request->user;
        $xmid = $user->id;
        $qy = $this->xmjjService->getQyByXm($xmid);
        return response()->json([
            'code' =>1000,
            'message' => '获取在管企业部分信息成功',
            'data' => $qy
        ]);
    }

    /**
     * 判断项目的接管与否状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isXmCanBeJieGuan(Request $request){
        $user = $request->user;
        $xmid = $user->id;
        $res = $this->xmjjService->isXmCanBeJieGuan($xmid);
        if ($res){
            return response()->json([
                'code' => 1000,
                'message' => '项目处于待管状态，可以被接管'
            ]);
        }
        else{
            return response()->json([
                'code' => 1001,
                'message' => '项目处于在管状态，不可以被新企业接管'
            ]);
        }
    }
    // 新企入驻
    public function qyRz(Request $request){
        $user = $request->user;
        $rule= [
            'sqyid'=>'required',
            'drzsj'=>'required',
            'srzjbr'=>'required'
        ];
        $res = ValidationHelper::validateCheck($request->input(),$rule);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        if (!$this->xmjjService->isXmCanBeJieGuan($user->id)){
            return response()->json([
                'code' =>1002,
                'message' => '项目已有企业在管！'
            ]);
        }
        $data = ValidationHelper::getInputData($request,$rule);
        $data['id']=SqlTool::makeUUID();
        $data['xmid'] = $user->id;
        $this->xmjjService->qyRz($data);
        return response()->json([
            'code' => 1000,
            'message' => '企业入驻成功'
         ]);
    }
    // 企业退出
    public function qyTc(Request $request){
        $user = $request->user;
        $rule = [
            'dtcsj'=>'required',
            'stcjbr'=>'required'
        ];
        $res = ValidationHelper::validateCheck($request->input(),$rule);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $data = ValidationHelper::getInputData($request,$rule);
        $data['xmid'] = $user->id;
        $data['id']=SqlTool::makeUUID();
        $this->xmjjService->qyTc($data);
        return response()->json([
            'code' => 1000,
            'message' => '企业退出成功'
        ]);
    }
    //根据项目token 获取交接记录
    public function searchJjJl(Request $request){
        $user = $request->user;
        $jjjl = $this->xmjjService->getJJJL($user->id);
        return response()->json([
            'code'=>1000,
            'message' => '获取项目交接记录成功',
            'data'=> $jjjl
        ]);
    }
}
