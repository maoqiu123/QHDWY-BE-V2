<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/6/23
 * Time: 下午9:32
 */

namespace App\Http\Controllers\LJXQ;

use App\Http\Controllers\Controller;
use App\Service\LJXQ\GZGZJHService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GZGZJHController extends Controller
{
    private $gzgzjhService;
    public function __construct(GZGZJHService $GZGZJHService)
    {
        $this->gzgzjhService=$GZGZJHService;
    }
    private $gzgzjhRules = [
        'dksrq'=>'required|date_format:"Y-m-d"',
        'djzrq'=>'required|date_format:"Y-m-d"',
        'sxqid'=>'required|max:50',
        'sgznr'=>'required|max:1000',
        'syxbz'=>'required|max:20',
        'sbz'=>'',
        'ssjzt'=>''
    ];

    /**
     * 不同于老旧小区基本信息的基本信息查询
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchLjxqjbxx(Request $request)
    {
        $community_name=$request->community_name;
        /**
         * 获取用户地址
         * @param $address
         */
        //$address = $request->address;
        $transformation_state=$request->transformation_state;
        $effective_sign= '有效';
        $size = $request->size ?? 10;

        $ggftysydgs = $this->gzgzjhService->searchLjxqjbxx($community_name,$transformation_state,$effective_sign,$size);

        return RequestTool::response($ggftysydgs, 1000, '查询老旧小区基本信息成功');
    }
    public function create(Request $request){
        $rules = $this->gzgzjhRules;
        //进行验证，成功则返回数据数组，失败则返回JsonResponse类
        $data = ValidationHelper::checkAndGet($request, $rules, 1001);
        //如果data类型为response则return，否则继续
        $flag = 1;
        $className = null;
        try {
            $className = class_basename($data);
        } catch (\Exception $exception) {
            $flag = 0;
        }
        if ($flag == 1 && $className == 'JsonResponse')
            return $data;
        //给数据添加唯一标识，所有数据的唯一标识均用次函数生成
        $data['id'] = SqlTool::makeUUID();
        //录入人
        $data['slrr'] = $request->user->id;
        //录入时间
        $data['dlrsj'] = Carbon::now();
        //入库
        $this->gzgzjhService->create($data);
        //返回结果
        return RequestTool::response(null, 1000, '添加成功');
    }
    public function update($id,Request $request){
        //获取当前操作用户
//        $user = $request->user;
        $rules = $this->gzgzjhRules;
        //进行验证，成功则返回数据数组，失败则返回JsonResponse类
        $data = ValidationHelper::checkAndGet($request, $rules, 1001);
        //如果data类型为response则return，否则继续
        $flag = 1;
        $className = null;
        try {
            $className = class_basename($data);
        } catch (\Exception $exception) {
            $flag = 0;
        }
        if ($flag == 1 && $className == 'JsonResponse')
            return $data;
        //入库
        $this->gzgzjhService->update($id,$data);
        //返回结果
        return RequestTool::response(null, 1000, "更新成功");
    }
    public function delete($id,Request $request){
        $res = $this->gzgzjhService->deleteGzjh($id);
        if(!$res)
            return RequestTool::response(null, 1002, '该进度计划存在上报信息，不允许删除');
        return RequestTool::response(null, 1000, '删除成功');
    }
    public function searchGzgzjh(Request $request)
    {
        if ($request->ljxq_id == null||$request->ljxq_id == ''){
            return RequestTool::response('', 1000, 'ljxq_id is required.');
        }
        $ljxq_id=$request->ljxq_id;
        $size = $request->size ?? 10;

        $ggftysydgs = $this->gzgzjhService->searchGzgzjh($ljxq_id,$size);

        return RequestTool::response($ggftysydgs, 1000, '查询改造工作计划成功');
    }

}