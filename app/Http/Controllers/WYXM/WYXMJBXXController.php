<?php

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WYQY\CommonController;
use App\Service\WYXM\WYXMJBXXService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WYXMJBXXController extends Controller
{

    private $WYXMJBXXService;
    private $commonController;

    public function __construct(WYXMJBXXService $WYXMJBXXService,CommonController $commonController)
    {
        $this->WYXMJBXXService = $WYXMJBXXService;
        $this->commonController = $commonController;
    }

    public function uploadFile($id,Request $request)
    {
        // dd($request->file);
        $res = $this->commonController->uploadFile($id,'t_xm_jbxx','xmjbxx',$request->file);
        if(!$res['success'])
            return RequestTool::response($res,1101,"上传失败!");


        return RequestTool::response($res,1000,"上传成功!");
    }

    public function deleteFile(Request $request)
    {
        if(!isset($request->fileid)||!isset($request->rowid))
            return RequestTool::response(null,1001,"未填写记录id或文件id!");
        $fileId = $request->fileid;
        $rowId= $request->rowid;
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_xm_jbxx');
        return response()->json($res);
    }

    /**
     * 搜索物业项目信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $sxmmc = $request->sxmmc; // 项目名称
        $sxmbh = $request->sxmbh;// 项目编号
        $sxmlx = $request->sxmlx; // 项目类型
//        $sghlx = $request->sghlx; //规划类型
        $sxzqh =$request->xzqh; // 行政区划
        $szldd = $request->szldd ; // 坐落地点
        $skfdw = $request->kfdw; // 开发单位
        $swyqy = $request->wyqy; //物业企业名称
        $xmzt = $request->xmzt; //项目状态
        $status = $request->status ?? '';
        $size = $request->size ?? 20;

        if ($status!='提交'&&$status!='暂存'&&$status!='')
        {
            return response()->json([
                'code' => 1001,
                'message' => 'status只可能是提交或者暂存'
            ]);
        }

        $user = $request->user;
        /**
         * 对于市级房管局仅查询本区县
         */
        if ($user->jc == 4)
        {
            $sxzqh = SqlTool::getXzqhMcByBm($user->sxzqh);
        }

        $xms = $this->WYXMJBXXService->search($sxmmc, $sxmbh,$sxzqh, $szldd,$skfdw,$sxmlx,$swyqy, $xmzt, null,$status,$size,['t_qyjbxx.sqymc','t_xm_jbxx.*','t_xzqh.mc']);
        foreach ($xms as $xm){
            $xm->sssqx=$xm->mc;
        }
        return RequestTool::response($xms, '1000', '查询成功');
    }

    /**
     * 获取企业所有项目信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEnterpriseXm($qyid, Request $request)
    {
        $xms = $this->WYXMJBXXService->getXmsByQyId($qyid);
        return RequestTool::response($xms, '1000', '查询成功');
    }

    /**
     * 获取物业项目信息
     * @param $xmId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showWYXM($xmId)
    {
        $xm = $this->WYXMJBXXService->getXmByXmId($xmId);
        unset($xm->password);
        return RequestTool::response($xm, '1000', '查询物业项目基本信息成功');
    }

    /**
     * 添加物业项目信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // 弃用，已经不存在创建项目，只有初始化项目和完善项目信息
    public function create(Request $request)
    {
        $rules = [
            'sxmmc' => 'required|max:50',
            'sxmbh' => 'required|max:50',
            'sghlx' => 'required|max:20',
            'sxmlx' => 'required|max:20',
            'sssqx' => 'required|max:30',
            'sssjd' => 'required|max:30',
            'sssjwh' => 'required|max:30',
            'szldd' => 'required|max:100',
            'skfjsdw' => 'required|max:100',
            'njzmj' => 'required|numeric',
            'nxmgm' => 'required|numeric',
            'nrjl' => 'required|numeric',
            'ndsmj' => 'required|numeric',
            'ndxmj' => 'required|numeric',
            'ifwzs' => 'required|numeric',
            'nzzgm' => 'required|numeric',
            'szzgmlx' => 'required|max:20',
            'nfzzgm' => 'required|numeric',
            'ighjsqs' => 'required|numeric',
            'sxmzt' => 'required|max:20',
            'ifwzts' => 'required|numeric',
            'izzts' => 'required|numeric',
            'ifzzts' => 'required|numeric',
            'dsqjgsj' => 'required',
            'dsqkgsj' => 'required',
            'dsqrzsj' => 'required',
            'iyrzqs' => 'required|numeric',
            'nyrzmj' => 'required|numeric',
            'nwrjmj' => 'required|numeric',
            'nwyglyfmj' => 'required|numeric',
            'swyglyfwz' => 'required|max:100',
            'nlhmj' => 'required|numeric',
            'nhtmj' => 'required|numeric',
            'nlhv' => 'required|numeric',
            'ightcw' => 'required|numeric',
            'idstcw' => 'required|numeric',
            'idxtcw' => 'required|numeric',
            'ilstcw' => 'required|numeric',
            'nghhucw' => 'required|numeric',
            'nsjhjcw' => 'required|numeric',
            'idts' => 'required|numeric',
            'ishsb' => 'required|numeric',
            'idrsb' => 'required|numeric',
            'ijkxt' => 'required|numeric',
            'izsb' => 'required|numeric',
            'ixfsb' => 'required|numeric',
            'izysb' => 'required|numeric',
            'izlsb' => 'required|numeric',
            'sxpwyfs' => 'required|max:20',
            'dzbsj' => 'required',
            'szbqymc' => 'max:100',
            'sbz'=>''
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $this->WYXMJBXXService->save($request->all());
        return RequestTool::response(null, '1000', '暂存成功');
    }

    /**
     * 完善项目信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fixXmxx(Request $request){
        $xmid = $request->user->id;
        $rules = [
            'sxmmc' => 'required|max:50',
            'sghlx' => 'required|max:20',
            'sxmlx' => 'required|max:20',
            'sssjd' => '',
            'sssjwh' => '',
            'szldd' => 'required|max:100',
            'skfjsdw' => 'required|max:100',
            'njzmj' => 'required|numeric',
            'nxmgm' => 'required|numeric',
            'nrjl' => 'required|numeric',
            'ndsmj' => 'required|numeric',
            'ndxmj' => 'required|numeric',
            'ifwzs' => 'required|numeric',
            'nzzgm' => 'required|numeric',
            'nfzzgm' => 'required|numeric',
            'ighjsqs' => 'required|numeric',
            'sxmzt' => 'required|max:20',
            'ifwzts' => 'required|numeric',
            'izzts' => 'required|numeric',
            'ifzzts' => 'required|numeric',
            'dsqjgsj' => 'required',
            'dsqkgsj' => 'required',
            'dsqrzsj' => 'required',
            'iyrzqs' => 'required|numeric',
            'nyrzmj' => 'required|numeric',
            'nwrjmj' => 'required|numeric',
            'nwyglyfmj' => 'required|numeric',
            'swyglyfwz' => 'required|max:100',
            'nlhmj' => 'required|numeric',
            'nhtmj' => '',
            'nlhv' => 'required|numeric',
            'ightcw' => 'required|numeric',
            'idstcw' => 'required|numeric',
            'idxtcw' => 'required|numeric',
            'ilstcw' => 'required|numeric',
            'nghhucw' => 'required|numeric',
            'nsjhjcw' => 'required|numeric',
            'idts' => 'required|numeric',
            'ishsb' => 'required|numeric',
            'idrsb' => 'required|numeric',
            'ijkxt' => 'required|numeric',
            'izsb' => 'required|numeric',
            'ixfsb' => 'required|numeric',
            'izysb' => 'required|numeric',
            'izlsb' => 'required|numeric',
            'sxpwyfs' => 'required|max:20',
            'njd'=>'',
            'nwd'=>'',
            'dyzdhclsj'=>'',
            'sxmtgm'=>'',
            'dzbsj' => 'required',
            'szbqymc' => 'max:100',
            'sstatus' => 'required',
            'sbz'=>''
        ];
        $res = ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $xmxx = ValidationHelper::getInputData($request,$rules);
        $this->WYXMJBXXService->fixXmxx($xmid,$xmxx);
        return response()->json([
            'code' =>1000,
            'message' => '项目信息完善成功'
        ]);
    }
    /**
     * 获取项目所有的相关信息的概述
     * @param $xmid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getXmXgxx($xmid,Request $request){
        $xgxx = $this->WYXMJBXXService->getXmXgxx($xmid);
        return response()->json([
            'code' => 1000,
            'message' => '根据项目ID获取项目所有相关信息',
            'data' => $xgxx
        ]);
    }

    public function initXm(Request $request){
        $rules = [
            'sxmmc' =>'required',
            'sssqx' => 'required'
        ];
        $res = ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $xmxx = ValidationHelper::getInputData($request,$rules);
        // 获取行政区划编码
        $code = SqlTool::getCodeBmBymc($xmxx['sssqx']);

        // 项目基本信息
        $xmxx['id']=SqlTool::makeUUID();
        $bh=SqlTool::makeZZBM();
        $xmxx['sxmbh']='A'.$bh;
        $xmxx['sdlzh']=$xmxx['sxmbh'];
        $xmxx['sdlmm']=md5('123456');
        $xmxx['ssfdlg']= '否';
        $xmxx['sxmzt']='待管';
        $xmxx['sstatus']= '暂存';
        $xmxx['sssqx'] = $code;
        // 项目业委会基本信息
        $ywhxx['id']=SqlTool::makeUUID();
        $ywhxx['xmid']=$xmxx['id'];
        $ywhxx['sdlzh']='B'.$bh;
        $ywhxx['sdlmm']=md5('123456');
        $ywhxx['ssfdlg']= '否';
        // 项目人员配置
        $rypz['id']=SqlTool::makeUUID();
        $rypz['xmid']=$xmxx['id'];
        $rypz['sstatus']='暂存';
        // 项目业主信息
        $yzxx['id']=SqlTool::makeUUID();
        $yzxx['xmid']=$xmxx['id'];
        $yzxx['sstatus']='暂存';
//        // 项目收费标准
//        $sfbz['id']=SqlTool::makeUUID();
//        $sfbz['sxmid']=$xmxx['id'];
//        $sfbz['sstatus']='暂存';

        $this->WYXMJBXXService->initXm($xmxx,$ywhxx,$rypz,$yzxx);
        return response()->json([
            'code' => 1000,
            'message' => '初始化项目成功',
            'data' => [
                'xmxx' => $xmxx,
                'ywhxx' => $ywhxx
            ]
        ]);
    }

    public function getXmxxxByToken(Request $request){
        $user = $request->user;
        $xmid = $user->id;
        $xxxx=$this->WYXMJBXXService->getXmByXmId($xmid);
        unset($xxxx->sdlmm);
        return response()->json([
            'code' => 1000,
            'message'=> '根据登录账号获取项目详细信息成功',
            'data' => $xxxx
        ]);
    }

    public function deleteXmById($xmid,Request $request){
        $user_type = $request->header('token_type');
        if ($user_type!= 'fgj'){
            return response()->json([
                'code'=>1013,
                'message' => '没有权限'
            ]);
        }
        $this->WYXMJBXXService->deleteXm($xmid);
        return response()->json([
            'code' => 1000,
            'message' => '删除成功'
        ]);
    }

    public function resetPassword($xmid,Request $request){
        $user_type = $request->header('token_type');
        if ($user_type!= 'fgj'){
            return response()->json([
                'code'=>1013,
                'message' => '没有权限'
            ]);
        }
        $this->WYXMJBXXService->resetPassword($xmid);
        return response()->json([
            'code' => 1000,
            'message' => '重置密码成功'
        ]);
    }

    public function updateSbz($id,Request $request)
    {
        $rules = [
            'tbz' =>'required'
        ];
        $res = ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $tbz = ValidationHelper::getInputData($request,$rules);
        $this->WYXMJBXXService->update($id,$tbz);
        return RequestTool::response(null,1000,'修改成功');
    }

    public function getTbz($id)
    {
        $tbz=$this->WYXMJBXXService->getbz($id);
        return RequestTool::response($tbz,1000,'查询成功');
    }
}