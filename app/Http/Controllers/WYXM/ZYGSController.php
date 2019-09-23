<?php

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\Controller;
use App\Service\WYXM\ZYGSService;
use App\Tools\RequestTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZYGSController extends Controller
{

    private $ZYGSService;

    public function __construct(ZYGSService $ZYGSService)
    {
        $this->ZYGSService = $ZYGSService;
    }

    /**
     * 搜索专业公司
     * @param Request $request
     */
    public function search(Request $request)
    {
        $sxmbh = $request->sxmbh;
        $swblb = $request->swblb;
        $sgxmc = $request->sgxmc;
        $size = $request->size ?? 20;

        $yzxx = $this->ZYGSService->search($sxmbh, $swblb, $sgxmc, $size);
        return RequestTool::response($yzxx, '1000', '查询成功');
    }

    /**
     * 获取专业公司
     * @param Request $request
     */
    public function show($xmId)
    {
        $yrzxx = $this->ZYGSService
            ->show(['xmid', '=', $xmId]);
        return RequestTool::response($yrzxx, '1000', '查询成功');
    }

    /**
     * 添加专业公司
     * @param Request $request
     */
    public function create(Request $request)
    {
        $xmid = $request->user->id;
        $rules = [
            'xmid' => 'required|max:50',
            'sxmbh' => 'required|max:30',
            'swblb' => 'required|max:20',
            'sgxmc' => 'required|max:100',
            'sfrdb' => 'required|max:30',
            'sgszhhm' => 'required|max:30',
            'sgsdh' => 'required|max:30',
            'iwbryzs' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        //todo debug
        $this->ZYGSService->save($request->all());
        return RequestTool::response(null, '1000', '暂存成功');
    }
}