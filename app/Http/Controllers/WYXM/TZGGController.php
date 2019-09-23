<?php
/**
 * Created by PhpStorm.
 * User: plyjdz
 * Date: 18-8-2
 * Time: 下午7:35
 */

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WYQY\CommonController;
use App\Service\WYXM\TZGGService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TZGGController extends Controller
{

    private $TZGGService;
    private $commonController;

    public function __construct(TZGGService $TZGGService,CommonController $commonController)
    {
        $this->TZGGService = $TZGGService;
        $this->commonController = $commonController;
    }

    private $rules = [
        'sbt'=>'required|max:200',
        'snr'=>'required',
        'siffgj'=>'max:2',
        'sifxm'=>'max:2',
        'sifqy'=>'max:2',
        'sifywh'=>'max:2',
        'sifjsdw'=>'max:2',
        'sifyz'=>'max:2',
    ];

    public function create(Request $request)
    {
        $user=$request->user;
        $rules = $this->rules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $type = $this->TZGGService->getType($request->header('token_type'));
        $data = ValidationHelper::getInputData($request, $rules);
        $id = SqlTool::makeUUID();
        $data = array_merge($data,[
                'id'=>$id,
                'slb' => $type,
                'sxzqh' => $user->sxzqh,
                'sfbr'=>$user->smc,
                'sstatus'=>'暂存'
        ]);
        $this->TZGGService->create($data);
        return RequestTool::response($id,1000,'新增成功');
    }

    public function publish($id)
    {
        $status=$this->TZGGService->getstatus($id);
        if($status=='提交')
            return RequestTool::response(null,1002,'已经发布，不允许此操作！');
        $this->TZGGService->publish($id);
        return RequestTool::response(null,1000,'发布成功');
    }

    public function update($id,Request $request)
    {
        $status=$this->TZGGService->getstatus($id);
        if($status=='提交')
            return RequestTool::response(null,1002,'已经发布，不允许此操作！');
        $rules = $this->rules;

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data = ValidationHelper::getInputData($request, $rules);
        $this->TZGGService->update($id,$data);
        return RequestTool::response(null,1000,'修改成功');
    }

    public function delete($id)
    {
        $status=$this->TZGGService->getstatus($id);
        if($status=='提交')
            return RequestTool::response(null,1002,'已经发布，不允许此操作！');
        $this->TZGGService->delete($id);
        return RequestTool::response(null,1000,'删除成功');
    }

    public function uploadFile($id,Request $request)
    {
        $res = $this->commonController->uploadFile($id,'t_tzgg','tzgg',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_tzgg');
        return response()->json($res);
    }

    public function searchForFgj(Request $request)
    {
        $nowUser = $request->header('token_type');
        $type = $this->TZGGService->getType($nowUser);
        $xzqh = $request->user->sxzqh;

        $title = $request->title;
        $time = $request->time;
        $persion = $request->persion;
        $size = $request->size ?? 10;

        $res = $this->TZGGService->search($type, $xzqh, $title, $time, $persion, $size, $nowUser);
        return RequestTool::response($res, 1000, '查询成功');
    }

    public function getTzgg($id)
    {
        $data=$this->TZGGService->getTzgg($id);
        return RequestTool::response($data,1000,'获取成功');
    }
}