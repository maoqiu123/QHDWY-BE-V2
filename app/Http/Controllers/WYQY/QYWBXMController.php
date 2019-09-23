<?php
namespace App\Http\Controllers\WYQY;

use App\Http\Controllers\Controller;
use App\Service\WYQY\QYWBXMService;
use App\Tools\ValidationHelper;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class QYWBXMController extends Controller{

    private $qywbxmService;
    private $commonController;

    public function __construct(QYWBXMService $QYWBXMService,CommonController $commonController)
    {
        $this->qywbxmService=$QYWBXMService;
        $this->commonController = $commonController;
    }

    public function uploadFile($id,Request $request)
    {
        // dd($request->file);
        $res = $this->commonController->uploadFile($id,'t_qyjbxx_wbxm','wbxm',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_qyjbxx_wbxm');
        return response()->json($res);
    }

    public function searchWbxm(Request $request){
        $shehui=$request->shehuixindai;
        $enterpriseName = $request->enterprise_name;
        $xmmc=$request->xmmc;
        $zldz=$request->zldz;
        $page = $request->page ?? 20;

        $wbxm= $this->qywbxmService->searchWbxm($shehui,$enterpriseName,$xmmc,$zldz,$page);

        return RequestTool::response($wbxm, 1000, '');
    }
    public function showWbxm($id, Request $request)
    {
        $data = $this->qywbxmService->show([
            ['id', '=', $id]
        ]);
        $data = $data->first();
        return RequestTool::response($data,1000,'查询成功');
    }

    private $qywbxm=[
        'sxmmc'=>'required|max:100',
        'sssqh'=>'required|max:40',
        'szldz'=>'required|max:200',
        'sghlx'=>'required',
        'njzmj'=>'required',
        'ifwzts'=>'',
        'sxmhdch'=>'required|max:100',
        'dhtqsrq'=>'',
        'dhtzzrq'=>'',
        'njcwyf'=>'required',
        'nsbssyhfy'=>'required',
        'nwyfndsjl'=>'required',
        'nmydpc'=>'required',
        'sbz'=>'max:200'

    ];

    public function createQyWbxmByQy(Request $request)
    {
        $rules=$this->qywbxm;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'id'=>SqlTool::makeUUID(),
            'qyid'=>$request->user->id,//todo
            'stxr' => $request->user->id,//todo 从user中获取
            'dtxrq' => Carbon::now(),
        ]);
        $this->qywbxmService->create($data);
        return RequestTool::response(null,1000,'创建成功');
    }

    public function updateQywbxmByQy($id,Request $request)
    {
        $rules=$this->qywbxm;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'stxr' => $request->user->id,//todo 从user中获取
            'dtxrq' => Carbon::now(),
        ]);
        $this->qywbxmService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function showQywbxm($id,Request $request)
    {
        $data = $this->qywbxmService->show([
            ['id', '=', $id]
        ]);;
        return RequestTool::response($data,1000,'查询成功');
    }

    public function getForQy(Request $request)
    {
        $size=$request->size ?? 10;
        $id=$request->user->id;
        $glry=$this->qywbxmService->serarchForQy($id,$size);
        return RequestTool::response($glry,1000,'');
    }

    public function deleteQywbxm($id,Request $request)
    {
        $this->qywbxmService->deleteWbxm($id);
        return RequestTool::response(null,1000,'删除成功');
    }
}