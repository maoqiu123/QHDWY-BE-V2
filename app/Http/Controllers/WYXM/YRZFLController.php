<?php

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\Controller;
use App\Service\WYXM\YRZFLService;
use App\Tools\RequestTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class YRZFLController extends Controller
{

    private $YRZFLService;

    public function __construct(YRZFLService $YRZFLService)
    {
        $this->YRZFLService = $YRZFLService;
    }

    /**
     * 搜索已入住分类
     * @param Request $request
     */
    public function search(Request $request)
    {
        $sxxbh = $request->sxxbh;
        $irzqs = $request->irzqs;
        $sghlx = $request->sghlx;
        $sfwlx = $request->sfwlx;
        $size = $request->size ?? 20;

        $yzxx = $this->YRZFLService->search($sxxbh, $irzqs, $sghlx, $sfwlx, $size);
        return RequestTool::response($yzxx, '1000', '查询成功');
    }

    /**
     * 获取物业项目入住分类
     * @param Request $request
     */
    public function show($xmId)
    {
        $yrzxx = $this->YRZFLService
            ->show(['xmid', '=', $xmId]);
        return RequestTool::response($yrzxx, '1000', '查询成功');
    }

    /**
     * 添加物业项目入驻分类
     * @param Request $request
     */
    public function create(Request $request)
    {
        //TODO 从token获取项目id
        $rules = [
            'sxxbh' => 'required|max:50',
            'irzqs' => 'required|numeric',
            'sghlx' => 'required|max:20',
            'sfwlx' => 'required|max:20',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $this->YRZFLService->save($request->all());
        return RequestTool::response(null, '1000', '暂存成功');
    }
}