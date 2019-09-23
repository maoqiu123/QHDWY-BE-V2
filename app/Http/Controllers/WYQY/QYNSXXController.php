<?php

namespace App\Http\Controllers\WYQY;

use App\Http\Controllers\Controller;
use App\Service\WYQY\QYNSXXService;
use App\Tools\ValidationHelper;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class QYNSXXController extends Controller
{

    private $qynsxxService;

    public function __construct(QYNSXXService $QYNSXXService)
    {
        $this->qynsxxService = $QYNSXXService;
    }

    public function searchQynsxx(Request $request)
    {
        $enterprise_code = $request->enterprise_code;
        $enterpriseName = $request->enterprise_name;
        $startnd = $request->startnd;
        $endnd = $request->endnd;
        $page = $request->page ?? 10;

        $glry = $this->qynsxxService->searchNsxx($enterprise_code, $enterpriseName, $startnd, $endnd, $page);

        return RequestTool::response($glry, 1000, '');
    }

    private $qynsxx=[
        'nnd'=>'required',
        'nnyysr'=>'required',
        'nnsje'=>'required',
        'nzyywsr'=>'required',
        'nzyyjsj'=>'required',
        'nqtsr'=>'required',
        'nqtyjsj'=>'required',
        'nyylr'=>'required',
        'nlrze'=>'required'
    ];

    public function createQynsxxByQy(Request $request)
    {
        $rules=$this->qynsxx;
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
        $this->qynsxxService->create($data);
        return RequestTool::response(null,1000,'创建成功');
    }

    public function updateQynsxxByQy($id,Request $request)
    {
        $rules=$this->qynsxx;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'stxr' => $request->user->id,
            'dtxrq' => Carbon::now(),
        ]);
        $this->qynsxxService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function showQynsxx($id,Request $request)
    {
        $data = $this->qynsxxService->showByQy($id);
        return RequestTool::response($data,1000,'查询成功');
    }

    public function deleteQynsxx($id,Request $request)
    {
        $this->qynsxxService->delete($id);
        return RequestTool::response(null,1000,'删除成功');
    }

    public function getForQy(Request $request)
    {
        $size=$request->size ?? 10;
        $id=$request->user->id;
        $glry=$this->qynsxxService->serarchForQy($id,$size);
        return RequestTool::response($glry,1000,'');
    }
}