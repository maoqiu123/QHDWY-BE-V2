<?php

namespace App\Http\Controllers\WYQY;

use App\Http\Controllers\Controller;
use App\Service\WYQY\QYGLRYService;
use App\Tools\ValidationHelper;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QYGLRYController extends Controller
{
    private $qyglryService;

    public function __construct(QYGLRYService $QYGLRYService)
    {
        $this->qyglryService = $QYGLRYService;
    }

    private $glryrules = [
        'sxm' => 'required|max:20',
        'sxb' => 'required|max:2',
        'dcsrq' => 'required|date_format:"Y-m-d"',
        'sxl' => 'required',
        'sxrzw' => 'required',
        'drzrq' => 'required|date_format:"Y-m-d"',
        'szjlx' => 'required',
        'szjhm' => 'required|max:100',
        'szzmm' => 'required',
        'szslx' => 'max:50',
        'szsbh' => 'max:50',
        'szc' => 'max:20',
        'szcbh' => 'max:50',
        'shjrq' => 'max:1000',
        'scfqk' => 'max:1000',
    ];

    public function createQyglryByQy(Request $request)
    {
        $rules = $this->glryrules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data = ValidationHelper::getInputData($request, $rules);
        $data = array_merge($data, [
            'id' => SqlTool::makeUUID(),
            'qyid'=>$request->user->id,
            'dtxrq' => Carbon::now(),
            'stxr' => $request->user->id
        ]);
        $this->qyglryService->create($data);
        return RequestTool::response(null, 1000, '添加成功');
    }

    public function updateQyglryByQy($id, Request $request)
    {
        $rules = $this->glryrules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data = ValidationHelper::getInputData($request, $rules);
        $data = array_merge($data, [
            'stxr' => $request->user->id,
            'dtxrq' => Carbon::now()
        ]);
        $this->qyglryService->update($id, $data);
        return RequestTool::response(null, 1000, '更新成功');
    }

    public function showQyglryByQy($id, Request $request)
    {
        $data = $this->qyglryService->show([
            ['id', '=', $id]
        ]);
        return RequestTool::response($data,1000,'查询成功');
    }

    public function searchQyglry(Request $request)
    {
        $enterprise_code=$request->enterprise_code;
        $enterpriseName = $request->enterprise_name;
        $name=$request->name;
        $zhiwu= $request->zhiwu;
        $size = $request->size ?? 10;

        $glry = $this->qyglryService->searchGlry($enterprise_code,$enterpriseName,$zhiwu,$name,$size);

        return RequestTool::response($glry, 1000, '');
    }

    public function getGlryByQy(Request $request)
    {
        $size=$request->size ?? 10;
        $id=$request->user->id;
        $glry=$this->qyglryService->serarchForQy($id,$size);
        return RequestTool::response($glry,1000,'');
    }

    public function showGlry($id, Request $request)
    {
        $data = $this->qyglryService->show([
            ['id', '=', $id]
        ]);
        $data = $data->first();
        return RequestTool::response($data,1000,'查询成功');
    }
    public function deleteGlry($id, Request $request)
    {
        $this->qyglryService->deleteGlry($id);
        return RequestTool::response(null,1000,'删除成功');
    }


}