<?php

namespace App\Http\Controllers\JSDW;

use App\Http\Controllers\WYQY\CommonController;
use App\Service\JSDW\JsdwZlyjService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JSDWZLYJController extends Controller
{
    private $zlyjService;
    private $commonController;
    public function __construct(JsdwZlyjService $jsdwZlyjService,CommonController $commonController)
    {
        $this->zlyjService=$jsdwZlyjService;
        $this->commonController = $commonController;
    }
    /*****************************建设单位端********************************/

    public function search(Request $request){
        $user=$request->user;
        $xmmc = $request->xmmc??'';
        $size = $request->size??10;
        $yjjl =$this->zlyjService->searchByXmmc($xmmc,$user->id,$size);
        return response()->json([
            'code'=>1000,
            'message' => '建设单位端查询前期查验资料移交记录成功',
            'data'=>$yjjl
        ]);
    }

    public function create(Request $request){
     $rules=[
       'sxmid' => 'required',
       'sqyid' => 'required',
       'dyjrq' =>'required',
       'syjzllb' =>'required',
       'sjsdwjsr'=>'required',
       'sqyjsr' =>'required'
     ];
     $res = ValidationHelper::validateCheck($request->input(),$rules);
     if ($res->fails()){
         return response()->json([
             'code'=>1001,
             'message'=> $res->errors()
         ]);
     }
     $data = ValidationHelper::getInputData($request,$rules);
     $data['sjsdwid']=$request->user->id;
     $data['id']=SqlTool::makeUUID();
     $data['sstatus']='暂存';
     $data['dtxrq']=Carbon::now();
     $this->zlyjService->create($data);
     return response()->json([
         'code'=>1000,
         'message'=>'创建前期查验资料移交记录成功'
     ]);
    }

    public function update($zlyjId,Request $request){
        $rules=[
            'sxmid' => 'required',
            'sqyid' => 'required',
            'dyjrq' =>'required',
            'syjzllb' =>'required',
            'sjsdwjsr'=>'required',
            'sqyjsr' =>'required'
        ];
        $res = ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code'=>1001,
                'message'=> $res->errors()
            ]);
        }
        $data = ValidationHelper::getInputData($request,$rules);
        $this->zlyjService->update($zlyjId,$data);
        return response()->json([
            'code'=>1000,
            'message'=>'更新前期查验资料移交更新成功'
        ]);
    }

    public function showById($zlyjId){
        $zlyjjl = $this->zlyjService->showById($zlyjId);
        return response()->json([
            'code'=>1000,
            'message'=>'根据Id查询资料移交详情成功',
            'data'=> $zlyjjl
        ]);
    }

    public function delete($zlyjId,Request $request){
        if ($this->zlyjService->deleteById($zlyjId)){
            return response()->json([
                'code'=> 1000,
                'message'=> '删除成功'
            ]);
        }else{
            return response()->json([
                'code'=>1032,
                'message'=>'删除失败'
            ]);
        }
    }

    public function submit($zlyjId,Request $request){
        if ($this->zlyjService->submitById($zlyjId)){
            return response()->json([
                'code'=>1000,
                'message'=>'提交成功'
            ]);
        }
        else{
            return response()->json([
                'code'=>1032,
                'message'=>'提交失败'
            ]);
        }
    }

    public function uploadFile($id,Request $request)
    {
        $res = $this->commonController->uploadFile($id,'t_jsdw_zlyj','zlyj',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_jsdw_zlyj');
        return response()->json($res);
    }


}
