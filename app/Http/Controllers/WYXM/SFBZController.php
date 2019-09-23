<?php

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\Controller;
use App\Service\WYXM\SFBZService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SFBZController extends Controller
{

    private $SFBZService;

    public function __construct(SFBZService $SFBZService)
    {
        $this->SFBZService = $SFBZService;
    }


    private $rules = [
        'ssflb' => 'required',
        'ssfxm' => 'required',
        'dkssj' => 'required',
        'djzsj' => 'required',
        'nsfbz' => 'required',
        'sjsdw' => 'required',
        'ssfgm' => 'required',
        'sbz' => ''
    ];

    /**
     * 搜索收费标准
     * @param Request $request
     */
    public function search(Request $request)
    {
        $sxmbh = $request->sxmbh;
        $size = $request->size ?? 20;

        $yzxx = $this->SFBZService->search($sxmbh, $size);
        return RequestTool::response($yzxx, '1000', '查询成功');
    }

    /**
     * 获取收费标准
     * @param Request $request
     */
    public function show($xmId)
    {
        $yrzxx = $this->SFBZService
            ->show(['xmid', '=', $xmId]);
        return RequestTool::response($yrzxx, '1000', '查询成功');
    }

    //物业项目端
    /**
     * 添加业主信息
     * @param Request $request
     */
    public function create(Request $request)
    {
        $user=$request->user;
        $xmid=$user->id;
        $xmbh=$this->SFBZService->getXmbh($xmid);
        $rules = [
            'nzzjcf'=>'required|numeric',
            'nfzzjcf'=>'numeric',
            'nzzyhf'=>'required|numeric',
            'nfzzyhf'=>'numeric',
            'nzys'=>'numeric',
            'nzs'=>'numeric',
            'ndrs'=>'numeric',
            'nggys'=>'numeric',
            'nggyd'=>'numeric',
            'ndxtcf'=>'numeric',
            'ndstcf'=>'numeric',
            'nlstcf'=>'numeric',
            'sbz'=>'max:200'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data=ValidationHelper::getInputData($request,$rules);
        $data=array_merge($data,[
            'xmid'=>$xmid,
       //     'id'=>SqlTool::makeUUID(),
            'sxmbh'=>$xmbh,
            'sstatus'=>'提交',
            'stxr'=>$this->SFBZService->getXmmc($xmid)
        ]);
        if(!$this->SFBZService->isExist($xmid))
            {
                $data=array_merge($data,[
                   'id'=>SqlTool::makeUUID()
                ]);
                $this->SFBZService->createXm($data);
            }
         else
         {
             $id=$this->SFBZService->getId($xmid);
             $this->SFBZService->update($id,$data);
         }
        return RequestTool::response(null, '1000', '暂存成功');
    }
    public function searchForXm(Request $request)
    {
        $user=$request->user;
        $xmid=$user->id;
        $sflb = $request->sflb;
        $sfxm = $request->sfxm;
        $page = $request->page ?? 10;
        $data=$this->SFBZService->searchSfbz($xmid,$sflb,$sfxm,$page);
        return RequestTool::response($data,1000,'查询成功');
    }

    public function deleteForXm($id,Request $request)
    {
        $this->SFBZService->delete($id);
        return RequestTool::response(null,1000,'删除成功');
    }


    public function searchForId($id,Request $request)
    {
        $data=$this->SFBZService->searchById($id);
        return RequestTool::response($data,'1000','查询成功');
    }

    public function createSfbz(Request $request)
    {
        $xmid = $request->user->id;
        $rules=$this->rules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'id'=>SqlTool::makeUUID(),
            'sxmid'=>$xmid,
            'slrr'=>$this->SFBZService->getXmmc($xmid),
            'dlrsj' => Carbon::now(),
            'sstatus' => '保存'
        ]);
        $this->SFBZService->create($data);
        return RequestTool::response(null,1000,'创建成功');
    }

    public function updateSfbz($id,Request $request)
    {
        $rules=$this->rules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        if($this->SFBZService->getStatus($id) != '保存')
            return RequestTool::response(null,1002,'已提交项目不允许修改');
        $this->SFBZService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function submitSfbz($id,Request $request)
    {
        $status = $this->SFBZService->getStatus($id);
        if($status != '保存')
            return RequestTool::response(null,1002,'已提交项目不允许修改');
        $this->SFBZService->update($id,[
            'sstatus' => '提交'
        ]);
        return RequestTool::response(null,1000,'提交成功');
    }

    public function getSfbz($id)
    {
        $data=$this->SFBZService->getSfbz($id);
        return RequestTool::response($data,1000,'获取成功');
    }

}