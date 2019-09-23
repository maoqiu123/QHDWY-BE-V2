<?php

namespace App\Http\Controllers\XCKP;

use App\Service\XCKP\JCBZService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class JCBZController extends Controller
{
    private $JCBZService;
    public function __construct(JCBZService $JCBZService)
    {
        $this->JCBZService = $JCBZService;
    }

    public function searchInspectionStandard(Request $request)
    {
        $taskId = $request->task_number;
        $majorTermId = $request->majorterm_number;
        $size = $request->size ?? 20;

        $enterprises = $this->JCBZService->searchInspectionStandard($taskId, $majorTermId,$size);
        return RequestTool::response($enterprises, 1000, '查询成功');
    }

    private $StandardRules = [
        'sjcbzbh' => 'required',
        'sjcbzwb' => 'required',
        'sbz' => '',
    ];

    public function createInspectionStandard(Request $request)
    {
        $rules = $this->StandardRules;
        $rules = array_merge($rules,[
            'srwid' => 'required',
            'sdxid' => 'required',
            ]);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'id' => SqlTool::makeUUID(),
        ]);
        $this->JCBZService->create($data);
        return RequestTool::response(null,1000,'创建成功');
    }

    public function updateInspectionStandard($id, Request $request)
    {
        $rules = $this->StandardRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        $this->JCBZService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function deleteInspectionStandard(Request $request)
    {
        $Standards = (array)$request->standard_ids;
        $results =[];
        foreach ($Standards as $id)
        {
            $res = $this->JCBZService->deleteInspectionStandard($id);
            $results[] = [
                $id => ($res === true)?'删除成功':'该数据有下级关联数据，不能删除，请先删除下级关联数据'
            ];
        }

        return RequestTool::response($results,1000,'请求成功');
    }

    public function getInspectionMajorTermByTaskId($taskId)
    {
        $data = $this->JCBZService->getInspectionMajorTermByTaskId($taskId);
        return RequestTool::response($data,1000,"请求成功");
    }
}
