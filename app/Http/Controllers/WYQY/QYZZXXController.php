<?php

namespace App\Http\Controllers\WYQY;

use App\Http\Controllers\Controller;
use App\Service\FileService;
use App\Service\WYQY\QYJCJLService;
use App\Service\WYQY\QYZZXXService;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use App\Tools\RequestTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class QYZZXXController extends Controller
{

    private $QYZZXXService;
    private $fileService;
    private $commonController;

    public function __construct(QYZZXXService $QYZZXXService, FileService $fileService, CommonController $commonController)
    {
        $this->QYZZXXService = $QYZZXXService;
        $this->fileService = $fileService;
        $this->commonController = $commonController;
    }

    public function uploadFile($id,Request $request)
    {
        // dd($request->file);
        $res = $this->commonController->uploadFile($id,'t_qyjbxx_zzxx','zzxx',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_qyjbxx_zzxx');
        return response()->json($res);
    }

    public function searchQyzzxx(Request $request)
    {
        $enterprise_code = $request->enterprise_code;
        $enterpriseName = $request->enterprise_name;

        $size = $request->size ?? 20;

        $enterprises = $this->QYZZXXService->searchZzxx(
            $enterprise_code, $enterpriseName, $size);
        return RequestTool::response($enterprises, 1000, '查询成功');
    }

    private $qyzzxx=[
        'szzdj'=>'required|max:100',
        'dqdrq'=>'required|date_format:Y-m-d'
    ];

    public function createQyzzxxByQy(Request $request)
    {
        $rules=$this->qyzzxx;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'id'=>SqlTool::makeUUID(),
            'stxr' => $request->user->id,//todo 从user中获取
            'qyid'=>$request->user->id,//todo
            'dtxrq' => Carbon::now(),
        ]);
        $this->QYZZXXService->create($data);
        return RequestTool::response(null,1000,'创建成功');
    }

    public function updateQyzzxxByQy($id,Request $request)
    {
        $rules=$this->qyzzxx;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'stxr' => $request->user->id,//todo 从user中获取
            'dtxrq' => Carbon::now(),
        ]);
        $this->QYZZXXService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function showQyzzxx($id,Request $request)
    {
        $data = $this->QYZZXXService->showByQy($id);
        return RequestTool::response($data,1000,'查询成功');
    }

    public function deleteQyzzxx($id,Request $request)
    {
        $this->QYZZXXService->deleteQyzzxx($id);
        return RequestTool::response(null,1000,'删除成功');
    }
    public function getForQy(Request $request)
    {
        $size=$request->size ?? 10;
        $id=$request->user->id;
        $glry=$this->QYZZXXService->serarchForQy($id,$size);
        return RequestTool::response($glry,1000,'');
    }
}
