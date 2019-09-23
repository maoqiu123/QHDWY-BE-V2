<?php

namespace App\Http\Controllers\XCKP;

use App\Service\XCKP\JCDXService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class JCDXController extends Controller
{
    private $JCDXService;
    public function __construct(JCDXService $JCDXService)
    {
        $this->JCDXService = $JCDXService;
    }

    public function searchInspectionMajorTerm(Request $request)
    {
        $taskId = $request->task_number;
        $size = $request->size ?? 20;

        $enterprises = $this->JCDXService->searchInspectionMajorTerm($taskId, $size);
        return RequestTool::response($enterprises, 1000, '查询成功');
    }

    private $MajorTermRules = [
        'sdxbh' => 'required',
        'sdxmc' => 'required',
        'sbz' => '',
    ];

    public function createInspectionMajorTerm($taskId, Request $request)
    {
        $rules = $this->MajorTermRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'id' => SqlTool::makeUUID(),
            'srwid' => $taskId,
        ]);
        $this->JCDXService->create($data);
        return RequestTool::response(null,1000,'创建成功');
    }

    public function updateInspectionMajorTerm($id, Request $request)
    {
        $rules = $this->MajorTermRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        $this->JCDXService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function deleteInspectionMajorTerm(Request $request)
    {
        $majorTerms = (array)$request->majorterm_ids;
        $results =[];
        foreach ($majorTerms as $id)
        {
            $res = $this->JCDXService->deleteInspectionMajorTerm($id);
            $results[] = [
                $id => ($res === true)?'删除成功':'该数据有下级关联数据，不能删除，请先删除下级关联数据'
            ];
        }

        return RequestTool::response($results,1000,'请求成功');
    }





}
