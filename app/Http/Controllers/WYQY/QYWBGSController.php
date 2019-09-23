<?php

namespace App\Http\Controllers\WYQY;

use App\Http\Controllers\Controller;
use App\Service\WYQY\QYWBGSService;
use App\Tools\ValidationHelper;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QYWBGSController extends Controller
{

    private $QYWBGSService;
    private $commonController;

    public function __construct(QYWBGSService $QYWBGSService,CommonController $commonController)
    {
        $this->QYWBGSService = $QYWBGSService;
        $this->commonController = $commonController;
    }

    public function uploadFile($id,Request $request)
    {
        // dd($request->file);
        $res = $this->commonController->uploadFile($id,'t_qyjbxx_wbgs','wbgs',$request->file);
        if(!$res['success'])
            return RequestTool::response($res,1101,"上传失败!");


        return RequestTool::response($res,1000,"上传成功!");
    }

    public function searchWbgs(Request $request)
    {
        $enterpriseCode = $request->enterprise_code;
        $sqymc = $request->sqymc;

        $size = $request->size ?? 20;

        $wbgs = $this->QYWBGSService->searchWbgs(
            $enterpriseCode, $sqymc, $size);
        return RequestTool::response($wbgs, 1000, '查询成功');
    }

    public function showWbgs($id)
    {
        $wbgs = $this->QYWBGSService->show([
            ['id', '=', $id]
        ]);
        return RequestTool::response($wbgs, 1000, '查询成功');
    }


    private $qywbgs=[
        'swbgsmc'=>'required|max:200',
        'sshxydm' => 'required',
        'sfddbr'=>'required|max:40',
        'dclrq'=>'required|date_format:Y-m-d',
        'nzczj'=>'',
        'slxdh'=>'required|max:40',
        'swbxm'=>'required|max:1000',
        'sbz'=>'max:200',
        'iyyqx'=>'required|integer'
    ];

    public function createQywbgsByQy(Request $request)
    {
        $rules=$this->qywbgs;
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
        $this->QYWBGSService->create($data);
        return RequestTool::response(null,1000,'创建成功');
    }

    public function updateQywbgsByQy($id,Request $request)
    {
        $rules=$this->qywbgs;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'stxr' => $request->user->id,
            'dtxrq' => Carbon::now(),
        ]);
        $this->QYWBGSService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function deleteQywbgs($id,Request $request)
    {
        $this->QYWBGSService->delete($id);
        return RequestTool::response(null,1000,'删除成功');
    }

    public function getForQy(Request $request)
    {
        $size=$request->size ?? 10;
        $id=$request->user->id;
        $glry=$this->QYWBGSService->serarchForQy($id,$size);
        return RequestTool::response($glry,1000,'');
    }
}
