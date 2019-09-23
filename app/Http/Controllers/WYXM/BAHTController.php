<?php

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\WYQY\CommonController;
use App\Service\WYXM\BAHTService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BAHTController extends Controller
{
    //
    private $bahtService;
    private $commonController;
    public function __construct(BAHTService $BAHTService,CommonController $commonController)
    {
        $this->bahtService = $BAHTService;
        $this->commonController=$commonController;
    }

    /**
     *  房管局端条件查询项目相关备案合同
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
//    public function searchForFgj(Request $request){
//        $xmbm = $request->xmbm;
//        $htmc = $request->htmc;
//        $htlx = $request->htlx;
//        $qymc = $request->qymc;
//        $fzr = $request->fzr;
//        $fzrlxdh = $request->fzrlxdh;
//        $startdate = $request->startdate;
//        $enddate = $request->enddate;
//        $size = $request->size ?? 20;
//        $hts = $this->bahtService->search($xmbm,$htmc,$htlx,$qymc,$fzr,$fzrlxdh,$startdate,$enddate,$size,['t_qyjbxx.sqymc','t_xm_ba_ht.*']);
//        return response()->json([
//            'code' => 1000,
//            'message' => '条件查询获取企业服务合同列表成功',
//            'data' =>$hts
//        ]);
//    }

    /**
     * 根据合同ID查询合同详细信息
     * @param $htid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBahtByHtId($htid,Request $request){
        $ht = $this->bahtService->getHtById($htid);
        return response()->json([
            'code' => 1000,
            'message' => '根据ID获取备案合同信息成功',
            'data'=> $ht
        ]);
    }

    public function getBahtByXmid($xmid,Request $request){
        $size = $request->size ?? 20;
        $ht = $this->bahtService->getHtByXmid($xmid,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据Id查询项目备案合同成功',
            'data'=> $ht
        ]);
    }

    public function searchHtForXm(Request $request)
    {
        $xmid = $request->user->id;

        $htmc = $request->htmc;
        $htlx = $request->htlx;
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $size = $request->size ?? 20;
        $datas = $this->bahtService->searchHtForXm($xmid,$htmc,$htlx,$startdate,$enddate,$size);
        return RequestTool::response($datas,1000,"查询成功");
    }

    private $HtRules = [
//        'id'=>'',
//        'xmid'=>'required|max:50',
//        'sxmbm'=>'required|max:30',
        'shtmc'=>'required|max:100',
        'shtlb' => 'required',
        'skfjsdw' => '',
        'skfjsdwlxr' => '',
        'skfjsdwlxdh' => '',
        'swyfuqy'=>'required|max:100',
        'swyfwfzr'=>'required|max:20',
        'swyfwfzrlxdh'=>'required',
        'swyfwqyzzzsh'=>'',
        'sxmlx'=>'required|max:20',
        'sxmdz'=>'required|max:100',
        'nzjzmj' => '',
        'nzzjzmj' => '',
        'nfzzjzmj' => '',
        'sywhmc' => '',
        'sywhlxr' => '',
        'sywhlxdh' => '',
        'dhtkssj'=>'required|date_format:"Y-m-d"',
        'dhtjssj'=>'required|date_format:"Y-m-d"',
        'sbz'=>'',
//        'sshr'=>'',
//        'dshsj'=>'',
//        'sshsm'=>'',
//        'sstatus'=>'',
//        'ssfyfj'=>'',
        ];

    public function createHt(Request $request)
    {
        $rules = $this->HtRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        $data = array_merge($data,[
            'id' => SqlTool::makeUUID(),
            'xmid' => $request->user->id,
            'qyid' => $request->user->sqyid,
            'sstatus' => '暂存',
            'stxr' => $request->user->id,
            'dtxsj' => Carbon::now()
        ]);
        $this->bahtService->create($data);
        return RequestTool::response(null,1000,'添加成功');
    }

    public function updateHt($id,Request $request)
    {
        $rules = $this->HtRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        if(!$this->bahtService->getHtEditStatus($id))
            return RequestTool::response(null,1003,'已提交不允许编辑');
        $this->bahtService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function submitHt($id,Request $request)
    {
        $res = $this->bahtService->submitHt($id);
        if($res)
            return RequestTool::response(null,1002,'不允许重复提交');
        return RequestTool::response(null,1000,'提交成功');
    }

    public function getHt($id,Request $request)
    {
        $HtInfo = $this->bahtService->getHtById($id);
        return RequestTool::response($HtInfo,1000,'请求成功');
    }

    public function deleteHt($id,Request $request)
    {
        $res = $this->bahtService->deleteHt($id);
        if(!$res)
            return RequestTool::response(null,1002,'已提交数据不允许删除');
        return RequestTool::response(null,1000,'删除成功');

    }


    public function searchHtForFgj(Request $request)
    {
        $sxmmc = $request->sxmmc;
        $shtmc = $request->shtmc;
        $shtlb = $request->shtlb;
        $dhtkssj = $request->dhtkssj;
        $dhtjssj = $request->dhtjssj;
        $size = $request->size ?? 20;
        $datas = $this->bahtService->searchHtForFgj($sxmmc,$shtmc,$shtlb,$dhtkssj,$dhtjssj,$size);
        return RequestTool::response($datas,1000,"查询成功");
    }

    public function uploadFile($id,Request $request)
    {
        // dd($request->file);
        $res = $this->commonController->uploadFile($id,'t_xm_ba_ht','baht',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_xm_ba_ht');
        return response()->json($res);
    }
}
