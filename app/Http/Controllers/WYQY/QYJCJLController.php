<?php

namespace App\Http\Controllers\WYQY;

use App\Http\Controllers\Controller;
use App\Service\FileService;
use App\Service\WYQY\QYJCJLService;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use App\Tools\RequestTool;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QYJCJLController extends Controller
{
    private $QYJCJLService;
    private $fileService;
    private $commonController;

    public function __construct(QYJCJLService $QYJCJLService,FileService $fileService,CommonController $commonController)
    {
        $this->QYJCJLService = $QYJCJLService;
        $this->fileService = $fileService;
        $this->commonController = $commonController;
    }


    public function uploadFile($id,Request $request)
    {
        $res = $this->commonController->uploadFile($id,'t_qyjbxx_jcjl','jcjl',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_qyjbxx_jcjl');
        return response()->json($res);
    }

    public function searchQyjcjl(Request $request)
    {
        $enterprise_code = $request->enterprise_code;
        $enterpriseName = $request->enterprise_name;

        $size = $request->size ?? 20;

        $enterprises = $this->QYJCJLService->searchZzxx(
            $enterprise_code, $enterpriseName, $size);
        return RequestTool::response($enterprises, 1000, '查询成功');
    }

    public function getForQy(Request $request)
    {
        $size=$request->size ?? 10;
        $id=$request->user->id;
        $glry=$this->QYJCJLService->serarchForQy($id,$size);
        return RequestTool::response($glry,1000,'');
    }


    private $qyjcjl=[
        'slx'=>'required|max:2',
        'djcrq'=>'required|date_format:Y-m-d',
        'sjcnr'=>'required|max:1000',
        'sjcjg'=>'required|max:50',
        'sjcjb'=>'required|max:2'
    ];

    public function createQyjcjlByQy(Request $request)
    {
        $rules=$this->qyjcjl;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'id'=>SqlTool::makeUUID(),
            'qyid'=>$request->user->id,
            'stxr' => $request->user->id,
            'dtxrq' => Carbon::now(),
        ]);
        $this->QYJCJLService->create($data);
        return RequestTool::response(null,1000,'创建成功');
    }

    public function updateQyjcjlByQy($id,Request $request)
    {
        $rules=$this->qyjcjl;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'stxr' => $request->user->id,
            'dtxrq' => Carbon::now(),
        ]);
        $this->QYJCJLService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function showQyjcjl($qyid,Request $request)
    {
        $data = $this->QYJCJLService->showByQy($qyid);
        return RequestTool::response($data,1000,'查询成功');
    }

    public function deleteQyjcjl($id,Request $request)
    {
        $this->QYJCJLService->deleteQyjcjl($id);
        return RequestTool::response(null,1000,'删除成功');
    }

}
