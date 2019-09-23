<?php

namespace App\Http\Controllers\XCKP;

use App\Http\Controllers\Controller;
use App\Service\XCKP\JCRWService;
use App\Tools\DtcxTool;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class JCRWController extends Controller
{
    private $XCKPJCRWService;

    public function __construct(JCRWService $XCKPJCRWService)
    {
        $this->XCKPJCRWService = $XCKPJCRWService;
    }

    public function searchInspectionTask(Request $request)
    {
        $taskId = $request->task_number;
        $taskName = $request->task_name;
        $taskType = $request->task_type;
        $size = $request->size ?? 20;

        $regionId = $request->user->sxzqh;
        $regionId = DtcxTool::getBmByMc($regionId);
        $enterprises = $this->XCKPJCRWService->searchInspectionTask(
            $taskId, $taskName, $taskType,$regionId, $size);
        return RequestTool::response($enterprises, 1000, '查询成功');
    }

    private $taskRules = [
        'srwbh' => 'required',
        'srwmc' => 'required',
        'srwlx' => 'required',
        'sxzqh' => 'required',
        'ssfzj' => 'required',
        'sbz' => '',
    ];

    public function createInspectionTask(Request $request)
    {
        $rules = $this->taskRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'id' => SqlTool::makeUUID(),
            'slrr' => $request->user->id,
            'dlrsj' => Carbon::now(),
        ]);
        $data['sxzqh'] = DtcxTool::getBmByMc($data['sxzqh']);
        $this->XCKPJCRWService->create($data);
        return RequestTool::response(null,1000,'创建成功');
    }

    public function updateInspectionTask($id, Request $request)
    {
        $rules = $this->taskRules;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        $this->XCKPJCRWService->update($id,$data);
        return RequestTool::response(null,1000,'更新成功');
    }


    public function deleteInspectionTask(Request $request)
    {
        $taskIds = (array)$request->task_ids;
        $results =[];
        foreach ($taskIds as $id)
        {
            $res = $this->XCKPJCRWService->deleteInspectionTask($id);
            $results[] = [
                $id => ($res === true)?'删除成功':'该数据有下级关联数据，不能删除，请先删除下级关联数据'
            ];
        }

        return RequestTool::response($results,1000,'请求成功');
    }

    public function searchAllocalTask($taskId,Request $request)
    {
        $progectName = $request->progect_name;
        $progectType = $request->progect_type;
        $size = $request->size ?? 20;

        $unAllocalTasks = $this->XCKPJCRWService->searchAllocalTask($taskId,$progectName,$progectType,$size);
        return RequestTool::response($unAllocalTasks,1000,"查询成功");
    }

    public function getAllocaledTask($taskId, Request $request)
    {
        $allocaledTask = $this->XCKPJCRWService->getAllocaledTask($taskId);
        return RequestTool::response($allocaledTask,1000,"请求成功");
    }

    public function saveAllocalStatus($taskId,Request $request)
    {
        $projects = $request->projects;

        $res = $this->XCKPJCRWService->saveAllocalStatus($taskId,$projects);
        if($res === true)
            return RequestTool::response(null,1000,"请求成功");
        else
            return RequestTool::response(null,1005,"请求失败");

    }

    public function getInspectionTask()
    {
        $data = $this->XCKPJCRWService->getInspectionTask();
        if($data == null)
            return RequestTool::response([
                'srwmc' => '暂无数据',
                'id' => null
            ],1000,"请求成功");
        return RequestTool::response($data,1000,"请求成功");
    }
}
