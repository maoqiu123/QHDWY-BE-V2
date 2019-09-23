<?php

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\WYQY\CommonController;
use App\Service\WYXM\BACJCYService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BACJCYController extends Controller
{
    //
    private $cjcyService;
    private $commonController;

    public function searchCjcyForFgj(Request $request)
    {
        $sxmmc = $request->sxmmc;
        $skfjsdw = $request->skfjsdw;
        $swyfuqy = $request->swyfuqy;
        $scylb = $request->scylb;
        $dstart = $request->dstart;
        $dend = $request->dend;
        $size = $request->size ?? 20;
        $datas = $this->cjcyService->searchCjcyForFgj($sxmmc,$skfjsdw,$swyfuqy,$scylb,$dstart,$dend,$size);
        return RequestTool::response($datas,1000,"查询成功");
    }

    public function __construct(BACJCYService $cjcyService,CommonController $commonController)
    {
        $this->cjcyService = $cjcyService;
        $this->commonController=$commonController;
    }

    public function getCjcysByXmid($xmid,Request $request){
        $size = $request->size ?? 20;
        $cjcys = $this->cjcyService->getCjcyByXmid($xmid,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据项目Id获取承接查验备案列表成功',
            'data' => $cjcys
        ]);
    }

    public function getCjcyByCjcyId($cjcyid){
        $cjcy=$this->cjcyService->getCjcyByCjcyid($cjcyid);
        return response()->json([
            'code' => 1000,
            'message' => '根据承接查验ID查看承接查验详细信息',
            'data' => $cjcy
        ]);
    }
    public function searchCjcyForXm(Request $request)
    {
        $xmid = $request->user->id;
        $type = $request->xmlb;
        $startTime = $request->start;
        $endTime = $request->end;
        $size = $request->size ?? 20;
        $datas = $this->cjcyService->searchCjcyForXm($xmid,$type,$startTime,$endTime,$size);
        return RequestTool::response($datas,1000,"查询成功");
    }

    private $CjcyRules = [
//        'id'=>'',
//        'xmid'=>'',
//        'sxmbm'=>'required|max:30',
        'skfjsdw'=>'max:100',
        'skfjsdwlxr'=>'max:20',
        'skfjsdwlxdh'=>'max:30',
        'swyfuqy'=>'max:100',
        'sywhmc'=>'max:100',
        'sywhlxr'=>'max:20',
        'sywhlxdh'=>'max:30',
        'swyfwfzr'=>'required|max:20',
        'swyfwfzrlxdh'=>'required|max:30',
        'swyfwqyzzzsh'=>'',
        'sxmlx'=>'',
        'sxmdz'=>'',
        'nzjzmj'=>'',
        'nzzjzmj'=>'',
        'nfzzjzmj'=>'',
        'scylb'=>'required|max:20',
        'dcysj'=>'date_format:"Y-m-d"',
        'ssfyzdb'=>'',
        'sfgsfcj'=>'',
        'ssfdsf'=>'',
        'sdsfmc'=>'',
        'sdsflxr'=>'',
        'sdsflxdh'=>'',
        'scyjg'=>'required|max:200',
        'sjgsfgz'=>'',
        'dcyfy'=>'',
        'sfycc'=>'',
        'sbz'=>'',
//        'sshr'=>'',
//        'dshsj'=>'',
//        'sshsm'=>'',
//        'sstatus'=>'',
//        'ssfyfj'=>'',
    ];

    public function createCjcy(Request $request)
    {
        $rules = $this->CjcyRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        $data = array_merge($data,[
            'id' => SqlTool::makeUUID(),
            'xmid' => $request->user->id,
            'sxmbm' => $request->user->sxmbh,
            'sqyid' => $request->user->sqyid,
            'skfjsdw' => $request->user->skfjsdw,
            'sstatus' => '暂存',
            'stxr' => $request->user->id,
            'dtxsj' => Carbon::now()
        ]);
        $this->cjcyService->create($data);
        return RequestTool::response(null,1000,'添加成功');
    }

    public function updateCjcy($id,Request $request)
    {
        $rules = $this->CjcyRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        if(!$this->cjcyService->getCjcyEditStatus($id))
            return RequestTool::response(null,1003,'已提交不允许编辑');
        $this->cjcyService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function submitCjcy($id,Request $request)
    {
        $res = $this->cjcyService->submitCjcy($id);
        if($res)
            return RequestTool::response(null,1002,'不允许重复提交');
        return RequestTool::response(null,1000,'提交成功');
    }

    public function getCjcy($id,Request $request)
    {
        $CjcyInfo = $this->cjcyService->getCjcyByCjcyid($id);
        return RequestTool::response($CjcyInfo,1000,'请求成功');
    }

    public function deleteCjcy($id,Request $request)
    {
        $res = $this->cjcyService->deleteCjcy($id);
        if(!$res)
            return RequestTool::response(null,1002,'已提交数据不允许删除');
        return RequestTool::response(null,1000,'删除成功');

    }

    public function uploadFile($id,Request $request)
    {
        // dd($request->file);
        $res = $this->commonController->uploadFile($id,'t_xm_ba_cjcy','bacjcy',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_xm_ba_cjcy');
        return response()->json($res);
    }

    public function getQymc(Request $request)
    {
        $id=$request->user->id;
        $qymc=$this->cjcyService->getQymc($id);
        return RequestTool::response($qymc,1000,'查询成功');
    }

}
