<?php

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\Controller;
use App\Service\WYXM\XMJLService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class XMJLController extends Controller
{

    private $XMJLService;

    public function __construct(XMJLService $XMJLService)
    {
        $this->XMJLService = $XMJLService;
    }

    /**
     * 房管局端用项目经理查询
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchForFgj(Request $request)
    {
        $xmmc= $request->xmmc;
        $xm = $request->xm;
        $sfzh=$request->sfzh;
        $xl=$request->xl;
        $sfyz=$request->sfyz;
        $xmjlzsh=$request->xmjlzsh;
        $size = $request->size ?? 20;

        $yzxx = $this->XMJLService->searchForFgj($xmmc,$xm,$sfzh,$xl,$sfyz,$xmjlzsh,$size);
        return RequestTool::response($yzxx, '1000', '查询成功');
    }

    /**
     * 根据经理Id查询项目经理详细信息
     * @param $jlid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getXmjlXxxx($jlid,Request $request){
        $xmjl = $this->XMJLService->getXmjlxxxx($jlid);
        return response()->json([
            'code' => 1000,
            'message' => '根据经理ID查询项目经理详细信息成功',
            'data' => $xmjl
        ]);
    }

    /**
     * 获取项目经理
     * @param Request $request
     */
    public function show($xmId)
    {
        $yrzxx = $this->XMJLService
            ->show(['xmid', '=', $xmId]);
        return RequestTool::response($yrzxx, '1000', '查询成功');
    }

    /**
     * 添加项目经理
     * @param Request $request
     */
    private $rules=[
                'sxm'=>'required|max:30',
                'ssfz'=>'required|max:20',
                'sxb'=>'required|max:2',
                'dcsrq'=>'required',
                'swhcd'=>'max:20',
                'szzmm'=>'max:20',
                'syzbm'=>'max:20',
                'sjtzz'=>'max:100',
                'shkszd'=>'max:50',
                'stxdz'=>'max:100',
                'slxdh'=>'max:30',
                'ssj'=>'required|max:30',
                'sdzyx'=>'max:30',
                'ssfyz'=>'max:2',
                'sxmjlzsh'=>'max:30',
                'ssfwys'=>'max:2',
                'swyszsh'=>'max:30',
                'drzsj'=>'',
                'sbz'=>'max:200'
    ];
    public function create(Request $request)
    {
        $user=$request->user;
        $xmid=$user->id;
        $rule=$this->rules;
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data=ValidationHelper::getInputData($request,$rule);
        $xmbh=$this->XMJLService->getXmbh($xmid);
        $data=array_merge($data,[
                'xmid'=>$xmid,
                'stxr'=>$user->id,
                'sxmbh'=>$xmbh
        ]);
        $this->XMJLService->save($data);
        return RequestTool::response(null, '1000', '暂存成功');
    }

    public function update($id,Request $request)
    {
        $user=$request->user;
        $xmid=$user->id;
        $rule=$this->rules;
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data=ValidationHelper::getInputData($request,$rule);
        $xmbh=$this->XMJLService->getXmbh($xmid);
        $data=array_merge($data,[
            'xmid'=>$xmid,
            'stxr'=>$user->id,
            'sxmbh'=>$xmbh
        ]);
        $this->XMJLService->update($id,$data);
        return RequestTool::response(null, '1000', '暂存成功');
    }

    public function delete($id)
    {
        $this->XMJLService->delete($id);
        return RequestTool::response(null,1000,'删除成功');
    }


    public function searchForXm(Request $request)
    {
        $name=$request->sxm;
        $size = $request->size ?? 10;
        $user=$request->user;
        $xmid=$user->id;
        $data=$this->XMJLService->searchForXm($name,$xmid,$size);
        return RequestTool::response($data,'1000','查询成功');
    }
}