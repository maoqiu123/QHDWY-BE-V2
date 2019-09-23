<?php

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\Controller;
use App\Service\WYXM\YZXXService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class YZXXController extends Controller
{

    private $YZXXService;

    public function __construct(YZXXService $YZXXService)
    {
        $this->YZXXService = $YZXXService;
    }

    /**
     * 搜索业主信息
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $sxmbh = $request->sxmbh;
        $size = $request->size ?? 20;

        $yzxx = $this->YZXXService->search($sxmbh, $size);
        return RequestTool::response($yzxx, '1000', '查询成功');
    }

    /**
     * 获取业主信息
     * @param $xmId
     * @param Request $request
     * @return JsonResponse
     */
    public function show($xmId,Request $request)
    {
        $yrzxx = $this->YZXXService
            ->show(['xmid', '=', $xmId]);
        return RequestTool::response($yrzxx, '1000', '查询成功');
    }

    /**
     * 添加业主信息
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $user=$request->user;
        $xmid=$user->id;
        $xmbh=$this->YZXXService->getXmbh($xmid);
        $rules = [
            'iyzhs' => 'required|numeric',
            'irkzs' => 'numeric',
            'ifwzts'=>'required|numeric',
            'iyzrzts'=>'required|numeric',
            'ijsdwkzfs'=>'required|numeric',
            'ssfclywh'=>'required|max:2',
            'dywhclsj'=>'',
            'dywhbzsj'=>'',
            'iywhcyzs'=>'numeric',
            'iywhzrs'=>'numeric',
            'dywhqssj'=>'',
            'dywhhjsj'=>'',
            'dhjzcyzsj'=>'',
            'shjycyy'=>'max:100',
            'snfzcgz'=>'',
            'sfzcgzyy'=>'max:100'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data=ValidationHelper::getInputData($request,$rules);
        $data=array_merge($data,[
                'xmid'=>$xmid,
                'sstatus'=>'保存',
                //'id'=>SqlTool::makeUUID(),
                'stxr'=>$xmid,
                'sxmbh'=>$xmbh
        ]);
        if(!$this->YZXXService->isExist($xmid))
        {
            $data=array_merge($data,[
                'id'=>SqlTool::makeUUID()
            ]);
            $this->YZXXService->create($data);
        }
        else
            {
                $id=$this->YZXXService->getId($xmid);
                $this->YZXXService->update($id,$data);
            }
        return RequestTool::response(null, '1000', '暂存成功');
    }

    public function searchForXm(Request $request)
    {
        $user=$request->user;
        $xmid=$user->id;
        $data=$this->YZXXService->searchByXmid($xmid);
        return RequestTool::response($data,1000,'查询成功');
    }
}