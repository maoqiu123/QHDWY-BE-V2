<?php

namespace App\Http\Controllers\WYXM;

use App\Service\WYXM\YWHJBXXService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Illuminate\Support\Facades\Validator;

class YWHJBXXController extends Controller
{
    private $ywhService;

    public function __construct(YWHJBXXService $YWHJBXXService)
    {
        $this->ywhService = $YWHJBXXService;
    }

    /**
     * 模糊搜索业委会列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $xmmc = $request->xmmc;
        $zrxm = $request->zrxm;
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $size = $request->size ?? 20;
        $ywhs = $this->ywhService->search($xmmc, $zrxm, $startdate, $enddate, $size, [
            't_xm_jbxx.sxmmc',
            't_ywh_jbxx.id as ywhid',
            't_ywh_jbxx.szrxm',
            't_ywh_jbxx.dclsj',
            't_ywh_jbxx.ncyzs',
            't_ywh_jbxx.nzfzrs',
            't_ywh_jbxx.sbgdd',
            't_ywh_jbxx.szrlxdh',
            't_ywh_jbxx.sdlzh',
            't_ywh_jbxx.ssfdlg'
        ]);
        return response()->json([
            'code'=>1000,
            'message' => '查询业委会列表成功',
            'data' => $ywhs
        ]);
    }

    /**
     * 初始化业委会密码(123456)
     * @param $ywhid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initPassword($ywhid,Request $request){
        if ($this->ywhService->initPassword($ywhid)){
            return response()->json([
                'code' => 1000,
                'message' => '密码已重置',
            ]);
        }
        return response()->json([
            'code' => 1030,
            'message' => '未知错误'
        ]);
    }

    /**
     * 根据 token 获取用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getYwhxxxxForYwh(Request $request){
        $user = $request->user;
        $xxxx = $this->ywhService->getYwhxxxxByYwhId($user->id);
        unset($xxxx->sdlmm);
        return response()->json([
            'code' => 1000,
            'message' => '获取用户详细信息成功',
            'data' => $xxxx
        ]);
    }

    /**
     * 完善业委会基本信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private $Rules = [
        'dclsj'=>'required|date_format:"Y-m-d"',
        'ncyzs'=>'required|integer',
        'nzfzrs'=>'required|integer',
        'sbgdd'=>'required',
        'sdlzh'=>'required',
        'szrlxdh'=>'required',
        'szrxm'=>'required',
    ];
    public function fix(Request $request){
        $rules = $this->Rules;
        $user = $request->user;
        $data = ValidationHelper::checkAndGet($request, $rules, 1001);
        //如果data类型为response则return，否则继续
        $flag = 1;
        $className = null;
        try {
            $className = class_basename($data);
        } catch (\Exception $exception) {
            $flag = 0;
        }
        if ($flag == 1 && $className == 'JsonResponse')
            return $data;
        $this->ywhService->update($user->id,$data);
        //返回结果
        return RequestTool::response(null, 1000, '完善成功');
    }
}
