<?php

namespace App\Http\Controllers\XCKP;

use App\Service\XCKP\QYZJService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class QYZJController extends Controller
{
    private $QYZJService;

    /**
     * QYZJController constructor.
     * @param QYZJService $QYZJService
     */
    public function __construct(QYZJService $QYZJService)
    {
        $this->QYZJService = $QYZJService;
    }

    public function searchSelfRecord(Request $request)
    {
        $taskId = $request->task_number;
        $projectId = $request->project_number;
        $size = $request->size ?? 20;

        if(!$this->QYZJService->isSelf($taskId))
            return RequestTool::response(null, 1002, '该任务非自检任务');

        if(!$this->QYZJService->isAllocled($taskId,$projectId))
            return RequestTool::response(null, 1003, '该项目尚未分配该任务');

        $enterprises = $this->QYZJService->searchSelfRecord(
            $taskId, $projectId,$size);
        return RequestTool::response($enterprises, 1000, '查询成功');
    }

    private $selfRecordRules = [
        'data.*.sjcbzid' => 'required',
        'data.*.szjjg' => 'required',
        'data.*.swtms' => 'required',
        'data.*.szjry' => 'required',
        'data.*.dzjsj' => 'required',
        'data.*.szgcs' => 'required',
        'data.*.dzgsx' => 'required',
        'data.*.szgzrr' => 'required',
        'data.*.szgjg' => 'required',
        'data.*.szgqk' => 'required',
        'data.*.sbz' => '',
    ];

    public function saveSelfCheck(Request $request)
    {
        $taskId = $request->task_number;
        $projectId = $request->project_number;
        $data = $request->data;
        if(!isset($request->data) || $data==[] ||!isset($request->task_number)||!isset($request->project_number))
        {
            return RequestTool::response(null, 1001, '未提交数据');
        }

        if(!$this->QYZJService->isSelf($taskId))
            return RequestTool::response(null, 1002, '该任务非自检任务');

        if(!$this->QYZJService->isAllocled($taskId,$projectId))
            return RequestTool::response(null, 1003, '该项目尚未分配该任务');


        $rules = $this->selfRecordRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');

        $data  =ValidationHelper::getArrayInputData($request->input('data'),$rules);
        $enterpriseId = 90000000001204;//todo $request->user->qyid;
        $inputPerson = $request->user->id;
        $this->QYZJService->saveSelfRecord($taskId, $enterpriseId, $projectId,$data, $inputPerson);
        return RequestTool::response(null, 1000, '保存成功');

    }

    public function submitSelfRecord(Request $request)
    {
        $taskId = $request->task_number;
        $projectId = $request->project_number;
        $rowId = $request->row_ids;
        $this->QYZJService->submitSelfRecord($rowId);
        $this->QYZJService->changeAllocledStatus($taskId,$projectId,'已提交');
        return RequestTool::response(null, 1000, '提交成功');
    }

    public function getInspectionTask(Request $request)
    {
        $data = $this->QYZJService->getInspectionTask();
        if($data == null)
            return RequestTool::response([
                'srwmc' => '暂无数据',
                'id' => null
            ],1000,"请求成功");
        return RequestTool::response($data, 1000, '请求成功');
    }

    public function getInspectionProject($taskId,Request $request)
    {
        $data = $this->QYZJService->getInspectionProject($taskId);
        if($data == null)
            return RequestTool::response([
                'srwmc' => '暂无数据',
                'id' => null
            ],1000,"请求成功");
        return RequestTool::response($data, 1000, '请求成功');
    }

}
