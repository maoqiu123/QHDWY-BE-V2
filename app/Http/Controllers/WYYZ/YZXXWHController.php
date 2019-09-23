<?php

namespace App\Http\Controllers\WYYZ;

use App\Service\WYYZ\YZJBXXService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class YZXXWHController extends Controller
{
    private $YZJBXXService;
    public function __construct(YZJBXXService $YZJBXXService)
    {
        $this->YZJBXXService = $YZJBXXService;
    }

    public function searchOwnerInfo(Request $request)
    {

        $xmid = $request->user->id;
        $sd = $request->block;
        $sdy = $request->unit;
        $sh=$request->room;
        $size = $request->size ?? 20;
        $ownersInfo  = $this->YZJBXXService->searchOwnerInfo($xmid,$sd,$sdy,$sh,$size);
        return RequestTool::response($ownersInfo,1000,"请求成功");
    }

    public function deleteOwnersInfo(Request $request)
    {
        $ownersIds = $request->owners_ids;
        $res = $this->YZJBXXService->deleteOwnersInfo($ownersIds);
        return RequestTool::response($res,1000,"请求成功");
    }

    public function initPassword(Request $request)
    {
        $ownersIds = $request->owners_ids;
        $this->YZJBXXService->initPassword($ownersIds);
        return RequestTool::response(null,1000,"请求成功");
    }

    public function createOwnerInfo(Request $request)
    {
        $ownerRules = [
            'sd' => 'required',
            'sdy' => 'required',
            'sh' => 'required',
            'syzxm' => 'required',
            'slxdh' => 'required'
        ];
        $validator = Validator::make($request->all(), $ownerRules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$ownerRules);
        $data['xmid'] = $request->user->id;
        $res = $this->YZJBXXService->createOwnersInfo($data);
        if(!$res)
            return RequestTool::response(null,1002,'用户已存在');
        return RequestTool::response(null,1000,'创建成功');
    }

    public function updateOwnerInfo($id,Request $request)
    {
        $ownerRules = [
            'syzxm' => 'required',
            'slxdh' => 'required'
        ];
        $validator = Validator::make($request->all(), $ownerRules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$ownerRules);

        $this->YZJBXXService->update($id,$data);
        return RequestTool::response(null,1000,'修改成功');
    }
    //todo 批量导入

    public function showQyjbxxForYz(Request $request)
    {
        $user=$request->user;
        $userid=$user->id;
        $xmid=$this->YZJBXXService->showXmidByYzid($userid);
        $qyid=$this->YZJBXXService->showQyidByXmid($xmid);
        $qyjbxx=$this->YZJBXXService->showQyjbxx($qyid);
        return RequestTool::response($qyjbxx,1000,'查询成功');
    }

    public function showXmjbxxForYz(Request $request)
    {
        $user=$request->user;
        $xmid=$this->YZJBXXService->showXmidByYzid($user->id);
        $xmjbxx=$this->YZJBXXService->showXmjbxx($xmid);
        return RequestTool::response($xmjbxx,1000,'查询成功');
    }
}
