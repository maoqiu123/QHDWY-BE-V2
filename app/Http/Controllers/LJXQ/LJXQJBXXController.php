<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/6/23
 * Time: 下午8:21
 */
namespace App\Http\Controllers\LJXQ;

use App\Http\Controllers\Controller;
use App\Service\LJXQ\LJXQJBXXService;
use App\Tools\RequestTool;
use App\Tools\ValidationHelper;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LJXQJBXXController extends Controller
{
    private $ljxqjbxxService;
    public function __construct(LJXQJBXXService $LJXQJBXXService)
    {
        $this->ljxqjbxxService=$LJXQJBXXService;
    }
    private $ljxqJbxxRules = [
        'sxqmc'=>'required|max:100',
        'sdz'=>'required|max:100',
        'djgsj'=>'required|date_format:"Y-m-d"',
        'njzmj'=>'required',
        'sldjcs'=>'required|max:200',
        'ihs'=>'required',
        'icws'=>'required',
        'nlhmj'=>'required',
        'ssfyjsss'=>'required|max:10',
        'swyglms'=>'required|max:500',
        'sgznr'=>'required|max:1000',
        'syxbz'=>'required|max:20',
        'sgzzt'=>'required|max:20',
        'sysdw'=>'',
        'sysdwlxr'=>'',
        'sysdwlxfs'=>'',
        'shxcxjz'=>'',
        'ssjzt'=>''
    ];
    private $gzxxRules = [
        'dqdrq'=>'required|date_format:"Y-m-d"',
        'dwcrq'=>'required|date_format:"Y-m-d"',
        'ssgdw'=>'required|max:200',
        'ssgdwlxr'=>'required|max:40',
        'ssgdwlxfs'=>'required|max:40',
    ];
    public function create(Request $request){
        $rules = $this->ljxqJbxxRules;
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
        if ($request->sgzzt == '已启动'){
            $rules2 = $this->gzxxRules;
            //进行验证，成功则返回数据数组，失败则返回JsonResponse类
            $data2 = ValidationHelper::checkAndGet($request, $rules2, 1001);
            //如果data类型为response则return，否则继续
            $flag = 1;
            $className = null;
            try {
                $className = class_basename($data2);
            } catch (\Exception $exception) {
                $flag = 0;
            }
            if ($flag == 1 && $className == 'JsonResponse')
                return $data2;
        }
        if (isset($data2)){
            $data = array_merge($data, $data2);
        }
        //给数据添加唯一标识，所有数据的唯一标识均用次函数生成
        $data['id'] = SqlTool::makeUUID();
            //所属区县默认给值
        $data['sssqx'] = $request->user->sxzqh;
        //所属街道（乡镇）默认给值
        $data['sssjd'] = '';
        //所属居（村）委会默认给值
        $data['sssjwh'] = '';
        //录入人
        $data['slrr'] = $request->user->id;
        //录入时间
        $data['dlrsj'] = Carbon::now();
        //入库
        $this->ljxqjbxxService->create($data);
        //返回结果
        return RequestTool::response(null, 1000, '添加成功');
    }
    public function update($id,Request $request){
        //获取当前操作用户
//        $user = $request->user;
        $rules = $this->ljxqJbxxRules;
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
        if ($request->sgzzt == '已启动'){
            $rules2 = $this->gzxxRules;
            //进行验证，成功则返回数据数组，失败则返回JsonResponse类
            $data2 = ValidationHelper::checkAndGet($request, $rules2, 1001);
            //如果data类型为response则return，否则继续
            $flag = 1;
            $className = null;
            try {
                $className = class_basename($data2);
            } catch (\Exception $exception) {
                $flag = 0;
            }
            if ($flag == 1 && $className == 'JsonResponse')
                return $data2;
        }
        if (isset($data2)){
            $data = array_merge($data, $data2);
        }
        //入库
        $this->ljxqjbxxService->update($id,$data);
        //返回结果
        return RequestTool::response(null, 1000, "更新成功");
    }
    public function delete($id,Request $request){
        $res = $this->ljxqjbxxService->deleteJbxx($id);
        if(!$res)
            return RequestTool::response(null, 1002, '该小区已有改造进度计划，不能删除，请先删改造任务');
        return RequestTool::response(null, 1000, '删除成功');
    }
    public function searchLjxqjbxx(Request $request)
    {
        $community_name=$request->community_name;
        $address = $request->address;
        $transformation_state=$request->transformation_state;
        $effective_sign= $request->effective_sign;
        $size = $request->size ?? 10;
        $sssqx = SqlTool::getCodeBmBymc($request->sssqx);
        $userXzqh = $request->user->sxzqh;

        $ggftysydgs = $this->ljxqjbxxService->searchLjxqjbxx($community_name,$address,$transformation_state,$effective_sign,$size,$userXzqh,$sssqx);

        return RequestTool::response($ggftysydgs, 1000, '查询老旧小区基本信息成功');
    }

}