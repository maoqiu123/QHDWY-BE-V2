<?php

namespace App\Http\Controllers\XCKP;

use App\Service\XCKP\JCJLService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class JCJLController extends Controller
{
    private $JCJLService;

    /**
     * JCJLController constructor.
     * @param JCJLService $JCJLService
     */
    public function __construct(JCJLService $JCJLService)
    {
        $this->JCJLService = $JCJLService;
    }

    public function searchInspectionResult(Request $request)
    {
        $taskId = $request->task_number;
        $projectId = $request->project_number;
        $size = $request->size ?? 20;

        if(!$this->JCJLService->isNotSelf($taskId))
            return RequestTool::response(null, 1002, '该任务非房管部门检查任务');

        if(!$this->JCJLService->isAllocled($taskId,$projectId))
            return RequestTool::response(null, 1003, '该项目尚未分配该任务');

        $params = 'sjcjg,swtms,sjcry,djcsj,szgcs,dzgsx,szgzrr,sxmzrr,t_xckp_jcjl.sbz,t_xckp_jcjl.sjczt';
        $enterprises = $this->JCJLService->searchInspectionResult(
            $taskId, $projectId,$size,$params);
        return RequestTool::response($enterprises, 1000, '查询成功');
    }

    public function saveInspectionResult(Request $request)
    {
        $taskId = $request->task_number;
        $projectId = $request->project_number;
        $data = $request->data;
        if(!isset($request->data) || $data==[] ||!isset($request->task_number)||!isset($request->project_number))
        {
            return RequestTool::response(null, 1001, '未提交数据');
        }

        if(!$this->JCJLService->isNotSelf($taskId))
            return RequestTool::response(null, 1002, '该任务非房管部门检查任务');

        if(!$this->JCJLService->isAllocled($taskId,$projectId))
            return RequestTool::response(null, 1003, '该项目尚未分配该任务');


        $rules = [
            'data.*.sjcbzid' => 'required',
            'data.*.sjcjg' => 'required|in:0,1',
            'data.*.swtms' => 'required',
            'data.*.sjcry' => 'required',
            'data.*.djcsj' => 'required',
            'data.*.szgcs' => 'required',
            'data.*.dzgsx' => 'required',
            'data.*.szgzrr' => 'required',
            'data.*.sxmzrr' => 'required',
            'data.*.sbz' => '',
        ];;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');

        $data  =ValidationHelper::getArrayInputData($request->input('data'),$rules);
        $enterpriseId = 90000000001204;//todo $request->user->qyid;
        $inputPerson = $request->user->id;
        $this->JCJLService->saveInspectionResult($taskId, $enterpriseId, $projectId,$data, $inputPerson);
        return RequestTool::response(null, 1000, '保存成功');

    }

    public function submitInspectionResult(Request $request)
    {
        $taskId = $request->task_number;
        $projectId = $request->project_number;
        $rowId = $request->row_ids;
        $this->JCJLService->submitInspectionResult($rowId);
        $this->JCJLService->changeAllocledStatus($taskId,$projectId,'已提交');
        return RequestTool::response(null, 1000, '提交成功');
    }

    public function getInspectionTask(Request $request)
    {
        $data = $this->JCJLService->getInspectionTask();
        if($data == null)
            return RequestTool::response([
                'srwmc' => '暂无数据',
                'id' => null
            ],1000,"请求成功");
        return RequestTool::response($data, 1000, '请求成功');
    }

    public function getInspectionProject($taskId,Request $request)
    {
        $data = $this->JCJLService->getInspectionProject($taskId);
        if($data == null)
            return RequestTool::response([
                'srwmc' => '暂无数据',
                'id' => null
            ],1000,"请求成功");
        return RequestTool::response($data, 1000, '请求成功');
    }

    public function getUnqualifiedProject($taskId,Request $request)
    {
        $data = $this->JCJLService->getUnqualifiedProject($taskId,['szgzt','=','已提交']);
        if($data == null)
            return RequestTool::response([
                'srwmc' => '暂无数据',
                'id' => null
            ],1000,"请求成功");
        return RequestTool::response($data, 1000, '请求成功');
    }

    public function searchUnqualifiedResult(Request $request)
    {
        $taskId = $request->task_number;
        $projectId = $request->project_number;
        $size = $request->size ?? 20;

        if(!$this->JCJLService->isNotSelf($taskId))
            return RequestTool::response(null, 1002, '该任务非房管部门检查任务');

        if(!$this->JCJLService->isAllocled($taskId,$projectId))
            return RequestTool::response(null, 1003, '该项目尚未分配该任务');

        $params = 'sjcjg,swtms,szgcs,dzgsx,szgzrr,sxmzrr,t_xckp_jcjl.sbz,szgtzsbh,szgzt,if(szgtzsbh != null,\'是\',\'否\') as sfdyg';
        $where = [['sjcjg','=','不合格']];
        $enterprises = $this->JCJLService->searchInspectionResult(
            $taskId, $projectId,$size,$params,$where);
        return RequestTool::response($enterprises, 1000, '查询成功');
    }

    public function printNotice(Request $request)
    {
        $recordIds = $request->record_numbers;
        if(!isset($recordIds) ||$recordIds==[])
            return RequestTool::response(null, 1001, '未提交数据');
        $data = $this->JCJLService->printNotice($recordIds);
        $data = array_merge($data,[
            'department' => '海港区房管局'//todo user->'行政区划'+'房产局'
        ]);
        return RequestTool::response($data, 1000, '打印成功');

    }

    public function getUnqualifiedUnSubmitProject($taskId,Request $request)
    {
        $data = $this->JCJLService->getUnqualifiedProject($taskId,['sjczt','=','未提交']);
        if($data == null)
            return RequestTool::response([
                'srwmc' => '暂无数据',
                'id' => null
            ],1000,"请求成功");
        return RequestTool::response($data, 1000, '请求成功');
    }

    public function searchResultEntry(Request $request)
    {
        $taskId = $request->task_number;
        $projectId = $request->project_number;
        $size = $request->size ?? 20;

        if(!$this->JCJLService->isNotSelf($taskId))
            return RequestTool::response(null, 1002, '该任务非房管部门检查任务');

        if(!$this->JCJLService->isAllocled($taskId,$projectId))
            return RequestTool::response(null, 1003, '该项目尚未分配该任务');

        $params = 'sjcjg,swtms,szgcs,dzgsx,szgjg,szgqk,szgjyr,dzgjysj,t_xckp_jcjl.sbz,szgtzsbh,szgzt';
        $where = [['sjcjg','=','不合格']];
        $enterprises = $this->JCJLService->searchInspectionResult(
            $taskId, $projectId,$size,$params,$where);
        return RequestTool::response($enterprises, 1000, '查询成功');
    }

    public function saveResultEntry(Request $request)
    {
        $taskId = $request->task_number;
        $projectId = $request->project_number;
        $data = $request->data;
        if(!isset($request->data) || $data==[] ||!isset($request->task_number)||!isset($request->project_number))
        {
            return RequestTool::response(null, 1001, '未提交数据');
        }

        if(!$this->JCJLService->isNotSelf($taskId))
            return RequestTool::response(null, 1002, '该任务非房管部门检查任务');

        if(!$this->JCJLService->isAllocled($taskId,$projectId))
            return RequestTool::response(null, 1003, '该项目尚未分配该任务');


        $rules = [
            'data.*.id' => 'required',
            'data.*.dzgjysj' => 'required',
            'data.*.szgjyr' => 'required',
            'data.*.szgjg' => 'required',
            'data.*.szgqk' => 'required',
            'data.*.sbz' => '',
        ];;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');

        $data  =ValidationHelper::getArrayInputData($request->input('data'),$rules);
        $this->JCJLService->saveResultEntry($data);
        return RequestTool::response(null, 1000, '保存成功');

    }

    public function submitResultEntry(Request $request)
    {
        $rowId = $request->row_ids;
        $this->JCJLService->submitResultEntry($rowId);
        return RequestTool::response(null, 1000, '提交成功');
    }

    public function getInspectionSituation($taskId,Request $request)
    {
        //todo
    }
}